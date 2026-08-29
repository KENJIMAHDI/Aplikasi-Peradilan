<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Kirim pesan Notifikasi Pengingat Sidang via Telegram Bot API
     */
    public function kirimPengingatSidang($chatId, $pesan)
    {
        $token = env('TELEGRAM_BOT_TOKEN', 'dummy-token-12345');
        
        if ($token === 'dummy-token-12345') {
            // Simulasi dummy ke file log jika tidak ada token
            Log::info("TELEGRAM NOTIFICATION (Simulated): To ChatID {$chatId} -> {$pesan}");
            return true;
        }

        try {
            $url = "https://api.telegram.org/bot{$token}/sendMessage";
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'text' => $pesan,
                'parse_mode' => 'Markdown'
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi Telegram: " . $e->getMessage());
            return false;
        }
    }
}
