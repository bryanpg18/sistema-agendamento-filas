<?php

namespace App\Notifications;

use App\Models\Cliente;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListaEsperaNotificacao extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $nomeCliente,
        public string $nomeServico,
        public string $data,
        public string $horario,
    ) {
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
            ->subject('Vaga liberada para o serviço '.$this->nomeServico)
            ->greeting('Olá, '.$this->nomeCliente.'!')
            ->line('Uma vaga foi liberada para o serviço '.$this->nomeServico.'.')
            ->line('Data: '.$this->data.' às '.$this->horario.'.')
            ->line('Entre em contato para confirmar seu agendamento.');
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
                        $this->nomeCliente,
                        'Uma vaga foi liberada para você',
                        $this->nomeServico,
                        $this->data,
                        $this->horario,
                    ],
                ),
            ]],
        ];
    }
}
