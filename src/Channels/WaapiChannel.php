<?php

namespace Voxsar\WhatsAppNotification\Channels;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WaapiChannel
{
    public function __construct(private ?string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl ?? config('whatsapp-notification.base_url', 'https://waapi.app/api/v1/instances/');
    }

    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! config('whatsapp-notification.enabled', true)) {
            Log::info('WAAPI channel is disabled');

            return;
        }

        $message = $notification->toWaapi($notifiable);

        if (! $message) {
            return;
        }

        $this->sendMessage($message);
    }

    /**
     * Send WhatsApp message via WAAPI
     */
    protected function sendMessage(array $message): string
    {
        $base = rtrim($this->baseUrl, '/').'/'.trim((string) config('whatsapp-notification.instance_id'));
        $client = new Client;

        Log::info('Sending WAAPI message', $message);
        sleep(1);

        try {
            $response = $client->request('POST', rtrim($base, '/').'/client/action/send-message', [
                'timeout' => config('whatsapp-notification.timeout', 180),
                'connect_timeout' => config('whatsapp-notification.connect_timeout', 180),
                'read_timeout' => config('whatsapp-notification.read_timeout', 180),
                'headers' => [
                    'Authorization' => 'Bearer '.config('whatsapp-notification.token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => $message,
            ]);

            $result = $response->getBody()->getContents();
            Log::info('WAAPI response', ['response' => $result]);

            return $result;
        } catch (Exception $e) {
            Log::error('WAAPI send failed', [
                'error' => $e->getMessage(),
                'message' => $message,
            ]);

            throw $e;
        }
    }
}
