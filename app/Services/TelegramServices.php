<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function sendMessage($chatId, string $message): bool
    {
        $token = config('services.telegram.bot_token');

        if (!$token || !$chatId) {
            return false;
        }

        try {
            $response = Http::timeout(15)->post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $message,
                ]
            );

            if (!$response->successful()) {
                Log::error('Telegram gagal mengirim pesan', [
                    'response' => $response->body(),
                ]);
            }

            return $response->successful();

        } catch (\Throwable $e) {
            Log::error('Telegram error: ' . $e->getMessage());
            return false;
        }
    }

    // Kirim ke semua user dengan role tertentu.
    public function sendToRole(string $role, string $message): void
    {
        User::where(function ($query) use ($role) {
            $query->where('role', $role)
                  ->orWhere('role2', $role);
        })
        ->whereNotNull('telegram_chat_id')
        ->where('telegram_chat_id', '!=', '')
        ->get()
        ->each(function ($user) use ($message) {
            $this->sendMessage($user->telegram_chat_id, $message);
        });
    }

    // Kirim ke user berdasarkan ID karyawan.
    public function sendToKaryawanId($karyawanId, string $message): void
    {
        if (!$karyawanId) {
            return;
        }

        $user = User::where('karyawan_id', $karyawanId)
            ->whereNotNull('telegram_chat_id')
            ->where('telegram_chat_id', '!=', '')
            ->first();

        if ($user) {
            $this->sendMessage($user->telegram_chat_id, $message);
        }
    }
}
