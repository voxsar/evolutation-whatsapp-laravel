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
        $this->baseUrl = $baseUrl ?? config('whatsapp-notification.base_url', 'https://your-evolution-api-server.com');
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

        $message = $this->convertToEvolutionFormat($message);

        $this->sendMessage($message);
    }

    /**
     * Convert old WAAPI format to Evolution API format
     */
    protected function convertToEvolutionFormat(array $message): array
    {
        $converted = $message;

        // Convert chatId to number
        if (isset($message['chatId']) && !isset($message['number'])) {
            $converted['number'] = $message['chatId'];
            unset($converted['chatId']);
        }

        // Convert body to text
        if (isset($message['body']) && !isset($message['text'])) {
            $converted['text'] = $message['body'];
            unset($converted['body']);
        }

        // Map other old WAAPI fields to Evolution API equivalents
        if (isset($message['quotedMessageId'])) {
            $converted['quoted'] = [
                'key' => [
                    'id' => $message['quotedMessageId']
                ]
            ];
            unset($converted['quotedMessageId']);
        }

        return $converted;
    }

    /**
     * Send WhatsApp message via Evolution API
     */
    protected function sendMessage(array $message): string
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $instance = config('whatsapp-notification.instance');
        $url = "{$baseUrl}/message/sendText/{$instance}";
        
        $client = new Client;

        Log::info('Sending Evolution API message', $message);
        sleep(1);

        try {
            $response = $client->request('POST', $url, [
                'timeout' => config('whatsapp-notification.timeout', 180),
                'connect_timeout' => config('whatsapp-notification.connect_timeout', 180),
                'read_timeout' => config('whatsapp-notification.read_timeout', 180),
                'headers' => [
                    'apikey' => config('whatsapp-notification.apikey'),
                    'Content-Type' => 'application/json',
                ],
                'json' => $message,
            ]);

            $result = $response->getBody()->getContents();
            Log::info('Evolution API response', ['response' => $result]);

            return $result;
        } catch (Exception $e) {
            Log::error('Evolution API send failed', [
                'error' => $e->getMessage(),
                'message' => $message,
            ]);

            throw $e;
        }
    }
}
