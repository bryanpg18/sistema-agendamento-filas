<?php

namespace App\Notifications;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgendamentoNotificacao extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var array{cliente: string, servico: string, data: string, horario: string, mensagem: string}
     */
    private array $dados;

    public string $evento;

    public function __construct(Agendamento $agendamento, string $evento)
    {
        $agendamento->loadMissing(['cliente', 'servico']);
        $this->evento = $evento;

        $this->dados = [
            'cliente' => $agendamento->cliente->nome_completo,
            'servico' => $agendamento->servico->nome,
            'data' => $agendamento->data->format('d/m/Y'),
            'horario' => substr($agendamento->horario, 0, 5),
            'mensagem' => match ($evento) {
                'confirmacao' => 'Seu agendamento foi confirmado',
                'alteracao' => 'Seu agendamento foi alterado',
                'cancelamento' => 'Seu agendamento foi cancelado',
                'lembrete' => 'Lembrete do seu atendimento',
                'concluido' => 'Seu atendimento foi concluído',
                default => 'Há uma atualização no seu agendamento',
            },
        ];

        $this->afterCommit();
    }

    /**
     * @return array<int, class-string|string>
     */
    public function via(object $notifiable): array
    {
        if (! $notifiable instanceof Cliente) {
            return [];
        }

        $channels = [];

        if ($notifiable->receber_notificacoes_email && filled($notifiable->email)) {
            $channels[] = 'mail';
        }

        if (
            $notifiable->receber_notificacoes_whatsapp
            && filled($notifiable->telefone)
            && filled(config('services.whatsapp.access_token'))
            && filled(config('services.whatsapp.phone_number_id'))
            && filled(config('services.whatsapp.graph_version'))
            && filled(config('services.whatsapp.template_name'))
        ) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->dados['mensagem'].' - '.config('app.name'))
            ->greeting('Olá, '.$this->dados['cliente'].'!')
            ->line($this->dados['mensagem'].'.')
            ->line('Serviço: '.$this->dados['servico'])
            ->line('Data: '.$this->dados['data'].' às '.$this->dados['horario'].'.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toWhatsApp(): array
    {
        return [
            'name' => config('services.whatsapp.template_name'),
            'language' => [
                'code' => config('services.whatsapp.template_language'),
            ],
            'components' => [[
                'type' => 'body',
                'parameters' => array_map(
                    fn (string $value): array => ['type' => 'text', 'text' => $value],
                    [
                        $this->dados['cliente'],
                        $this->dados['mensagem'],
                        $this->dados['servico'],
                        $this->dados['data'],
                        $this->dados['horario'],
                    ],
                ),
            ]],
        ];
    }
}
