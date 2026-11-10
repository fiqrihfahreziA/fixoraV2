<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;

class TelegramController extends Controller
{
    public function connect()
    {
        $user = Auth::user();

        if ($user->telegram_chat_id) {
            return back()->with('success', 'Telegram sudah terhubung.');
        }

        $username = config('services.telegram.bot_username');

        if (!$username) {
            return back()->with('error', 'Username bot Telegram belum diatur.');
        }

        $linkToken = Str::random(40);

        Cache::store('file')->put(
            'telegram_link:' . $linkToken,
            $user->id,
            now()->addMinutes(10)
        );

        $link = 'https://t.me/' . ltrim($username, '@')
            . '?start=' . $linkToken;

        return redirect()->away($link);
    }

    public function webhook(Request $request)
    {
        $message = $request->input('message');

        if (
            !$message ||
            empty($message['text']) ||
            empty($message['chat']['id'])
        ) {
            return response()->json(['ok' => true]);
        }

        $text = trim($message['text']);
        $chatId = $message['chat']['id'];

        if (!preg_match('/^\/start(?:@\w+)?\s+([A-Za-z0-9_-]+)/', $text, $matches)) {
            return response()->json(['ok' => true]);
        }

        $linkToken = $matches[1];

        $userId = Cache::store('file')->pull(
            'telegram_link:' . $linkToken
        );

        if (!$userId) {
            $this->sendTelegramMessage(
                $chatId,
                'Link penghubung sudah kedaluwarsa. Silakan klik Hubungkan Telegram dari aplikasi Fixora lagi.'
            );

            return response()->json(['ok' => true]);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['ok' => true]);
        }

        $user->forceFill([
            'telegram_chat_id' => (string) $chatId
        ])->save();

        $this->sendTelegramMessage(
            $chatId,
            'Halo ' . $user->name . '! Telegram kamu berhasil terhubung dengan aplikasi Fixora.'
        );

        return response()->json(['ok' => true]);
    }

    private function sendTelegramMessage($chatId, $message)
    {
        $token = config('services.telegram.bot_token');

        if (!$token) {
            return false;
        }

        $response = Http::timeout(15)->post(
            "https://api.telegram.org/bot{$token}/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => $message
            ]
        );

        return $response->successful();
    }

    public function test()
    {
        $user = Auth::user();

        $sent = $this->sendTelegramMessage(
            $user->telegram_chat_id,
            'Tes notifikasi Fixora berhasil! 🎉'
        );

        return back()->with(
            $sent ? 'success' : 'error',
            $sent ? 'Pesan Telegram berhasil dikirim!' : 'Pesan Telegram gagal dikirim.'
        );
    }
}
