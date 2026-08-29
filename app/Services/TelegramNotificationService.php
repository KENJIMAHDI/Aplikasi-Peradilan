<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    protected $botToken;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN', '');
    }

    /**
     * Kirim pesan ke Telegram chat ID.
     *
     * @param string $chatId
     * @param string $message
     * @return bool
     */
    public function sendMessage($chatId, $message): bool
    {
        if (empty($this->botToken) || empty($chatId)) {
            Log::warning('Telegram Notification failed: Bot token or Chat ID is empty.');
            return false;
        }

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        try {
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram API Error: ' . $e->getMessage());
            return false;
        }
    }
}
