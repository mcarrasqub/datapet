<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message to a specific number.
     *
     * @param  string  $to  The phone number (with country code, e.g. 573XXXXXXXX)
     * @param  string  $message  The message body
     */
    public function sendMessage(string $to, string $message): bool
    {
        $driver = config('services.whatsapp.driver', 'log');

        if ($driver === 'ultramsg') {
            $instance = config('services.whatsapp.ultramsg.instance');
            $token = config('services.whatsapp.ultramsg.token');

            if (empty($instance) || empty($token)) {
                Log::warning('[WHATSAPP API] UltraMsg configuration is incomplete (instance or token missing). Fallback to log.');

                return $this->logMessage($to, $message);
            }

            // Normalise the phone number format (digits only, e.g. 573XXXXXXXX)
            $phone = preg_replace('/\D/', '', $to);

            try {
                $response = Http::asForm()->post("https://api.ultramsg.com/{$instance}/messages/chat", [
                    'token' => $token,
                    'to' => $phone,
                    'body' => $message,
                ]);

                if ($response->successful()) {
                    Log::info("[WHATSAPP API] Message successfully sent to {$to} via UltraMsg.");

                    return true;
                }

                Log::error('[WHATSAPP API] Failed to send message via UltraMsg. Response: '.$response->body());

                return false;
            } catch (\Exception $e) {
                Log::error('[WHATSAPP API] Exception while sending message via UltraMsg: '.$e->getMessage());

                return false;
            }
        }

        return $this->logMessage($to, $message);
    }

    /**
     * Log the WhatsApp message locally.
     */
    protected function logMessage(string $to, string $message): bool
    {
        Log::info(sprintf(
            '[WHATSAPP RECORDATORIO] Mensaje simulado enviado al %s: %s',
            $to,
            $message
        ));

        return true;
    }
}
