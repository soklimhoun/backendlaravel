<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramOrderController extends Controller
{
    public function send(Request $request)
    {
        $orderItems = $request->input('items', []);

        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (empty($orderItems)) {
            return response()->json(['error' => 'No order items'], 400);
        }

        $message = "🛒 *New Order:*\n";
        foreach ($orderItems as $item) {
            $message .= "• {$item['name']} x{$item['quantity']} = \$" .
                number_format($item['price'] * $item['quantity'], 2) . "\n";
        }

        $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);

        if ($response->successful()) {
            return response()->json(['message' => 'Order sent to Telegram']);
        } else {
            return response()->json(['error' => 'Failed to send order'], 500);
        }
    }
}
