<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\User;
use App\Notifications\AgendamentoNotificacao;
use App\Notifications\Channels\WhatsAppChannel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AgendamentoNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_creation_queues_email_and_whatsapp_for_consented_channels(): void
    {
        $this->travelTo(Carbon::parse('2026-10-01 08:00:00', 'America/Sao_Paulo'));
        config([
            'services.whatsapp.access_token' => 'test-token',
            'services.whatsapp.phone_number_id' => 'phone-number-id',
            'services.whatsapp.graph_version' => 'v26.0',
            'services.whatsapp.template_name' => 'agendamento_aviso',
        ]);
        Notification::fake();

        $user = User::factory()->create();
        $cliente = $this->criarCliente([
            'receber_notificacoes_email' => true,
            'receber_notificacoes_whatsapp' => true,
        ]);
        $servico = $this->criarServico();

        $this->actingAs($user)
            ->post(route('agendamentos.store'), [
                'cliente_id' => $cliente->id,
                'servico_id' => $servico->id,
                'data' => '2026-10-05',
                'horario' => '09:00',
            ])
            ->assertRedirect(route('agendamentos.index'));

        Notification::assertSentTo(
            $cliente,
            AgendamentoNotificacao::class,
            fn (AgendamentoNotificacao $notificacao, array $channels): bool => $notificacao->evento === 'confirmacao'
                && $channels === ['mail', WhatsAppChannel::class],
        );
    }

    public function test_appointment_changes_and_cancellations_queue_notifications(): void
    {
        $this->travelTo(Carbon::parse('2026-10-01 08:00:00', 'America/Sao_Paulo'));
        Notification::fake();

        $user = User::factory()->create();
        $cliente = $this->criarCliente(['receber_notificacoes_email' => true]);
        $servico = $this->criarServico();
        $agendamento = $this->criarAgendamento($cliente, $servico);

        $this->actingAs($user)
            ->put(route('agendamentos.update', $agendamento), [
                'cliente_id' => $cliente->id,
                'servico_id' => $servico->id,
                'data' => '2026-10-05',
                'horario' => '09:30',
            ])
            ->assertRedirect(route('agendamentos.index'));

        Notification::assertSentTo(
            $cliente,
            AgendamentoNotificacao::class,
            fn (AgendamentoNotificacao $notificacao): bool => $notificacao->evento === 'alteracao',
        );

        $this->patch(route('agendamentos.cancelar', $agendamento->fresh()))
            ->assertRedirect(route('agendamentos.index'));

        Notification::assertSentTo(
            $cliente,
            AgendamentoNotificacao::class,
            fn (AgendamentoNotificacao $notificacao): bool => $notificacao->evento === 'cancelamento',
        );
    }

    public function test_whatsapp_channel_sends_template_to_meta_cloud_api(): void
    {
        config([
            'services.whatsapp.access_token' => 'test-token',
            'services.whatsapp.phone_number_id' => 'phone-number-id',
            'services.whatsapp.graph_version' => 'v26.0',
            'services.whatsapp.template_name' => 'agendamento_aviso',
            'services.whatsapp.template_language' => 'pt_BR',
            'services.whatsapp.country_code' => '55',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'graph.facebook.com/v26.0/phone-number-id/messages' => Http::response(['messages' => [['id' => 'wamid.test']]]),
        ]);

        $cliente = $this->criarCliente(['telefone' => '(11) 99999-9999']);
        $agendamento = $this->criarAgendamento($cliente, $this->criarServico());
        $notificacao = new AgendamentoNotificacao($agendamento, 'confirmacao');

        (new WhatsAppChannel)->send($cliente, $notificacao);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://graph.facebook.com/v26.0/phone-number-id/messages'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->data()['to'] === '5511999999999'
            && $request->data()['type'] === 'template'
            && $request->data()['template']['name'] === 'agendamento_aviso'
            && count($request->data()['template']['components'][0]['parameters']) === 5);
    }

    public function test_reminder_is_queued_once_24_hours_before_the_appointment(): void
    {
        $this->travelTo(Carbon::parse('2026-10-01 09:00:35', 'America/Sao_Paulo'));
        Notification::fake();

        $cliente = $this->criarCliente(['receber_notificacoes_email' => true]);
        $agendamento = $this->criarAgendamento($cliente, $this->criarServico(), [
            'data' => '2026-10-02',
            'horario' => '09:00',
        ]);

        $this->artisan('agendamentos:enviar-lembretes')->assertSuccessful();
        $this->artisan('agendamentos:enviar-lembretes')->assertSuccessful();

        $this->assertNotNull($agendamento->fresh()->lembrete_enviado_em);
        Notification::assertSentToTimes($cliente, AgendamentoNotificacao::class, 1);
        Notification::assertSentTo(
            $cliente,
            AgendamentoNotificacao::class,
            fn (AgendamentoNotificacao $notificacao): bool => $notificacao->evento === 'lembrete',
        );
    }

    public function test_reminder_does_not_mark_or_notify_clients_without_channel_consent(): void
    {
        $this->travelTo(Carbon::parse('2026-10-01 09:00:35', 'America/Sao_Paulo'));
        Notification::fake();

        $cliente = $this->criarCliente();
        $agendamento = $this->criarAgendamento($cliente, $this->criarServico(), [
            'data' => '2026-10-02',
            'horario' => '09:00',
        ]);

        $this->artisan('agendamentos:enviar-lembretes')->assertSuccessful();

        $this->assertNull($agendamento->fresh()->lembrete_enviado_em);
        Notification::assertNothingSent();
    }

    public function test_client_notification_preferences_can_be_saved(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('clientes.store'), [
                'nome_completo' => 'Cliente com Preferências',
                'cpf' => '12345678901',
                'telefone' => '(11) 99999-9999',
                'email' => 'cliente@example.com',
                'receber_notificacoes_email' => '1',
            ])
            ->assertRedirect(route('clientes.index'));

        $cliente = Cliente::where('cpf', '12345678901')->firstOrFail();

        $this->assertTrue($cliente->receber_notificacoes_email);
        $this->assertFalse($cliente->receber_notificacoes_whatsapp);
    }

    /**
     * @param  array<string, mixed>  $atributos
     */
    private function criarCliente(array $atributos = []): Cliente
    {
        return Cliente::create(array_replace([
            'nome_completo' => 'Cliente de Notificação',
            'cpf' => '12345678901',
            'telefone' => '11999999999',
            'email' => 'cliente@example.com',
        ], $atributos));
    }

    private function criarServico(): Servico
    {
        return Servico::create([
            'nome' => 'Consulta',
            'duracao_minutos' => 30,
            'preco' => 100,
            'ativo' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $atributos
     */
    private function criarAgendamento(Cliente $cliente, Servico $servico, array $atributos = []): Agendamento
    {
        return Agendamento::create(array_replace([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-10-05',
            'horario' => '09:00',
            'status' => 'confirmado',
        ], $atributos));
    }
}
