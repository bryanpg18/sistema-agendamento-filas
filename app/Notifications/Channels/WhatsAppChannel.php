<?php

namespace App\Notifications\Channels;

use App\Models\Cliente;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notifiable instanceof Cliente || ! method_exists($notification, 'toWhatsApp')) {
            throw new InvalidArgumentException('O canal WhatsApp precisa de um cliente e uma notificação compatível.');
        }

        $phoneNumber = preg_replace('/\D+/', '', $notifiable->routeNotificationForWhatsApp()) ?? '';
        $countryCode = (string) config('services.whatsapp.country_code');

        if ($phoneNumber === '') {
            throw new InvalidArgumentException('O cliente não possui um telefone válido para WhatsApp.');
        }

        if ($countryCode !== '' && ! str_starts_with($phoneNumber, $countryCode)) {
            $phoneNumber = $countryCode.$phoneNumber;
        }

        $version = config('services.whatsapp.graph_version');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        Http::acceptJson()
            ->connectTimeout(3)
            ->timeout(10)
            ->withToken((string) config('services.whatsapp.access_token'))
            ->post("https://graph.facebook.com/{$version}/{$phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $phoneNumber,
                'type' => 'template',
                'template' => $notification->toWhatsApp(),
            ])->throw();
    }
}
