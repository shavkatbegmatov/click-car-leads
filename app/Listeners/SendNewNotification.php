<?php

namespace App\Listeners;

use App\Events\NewLeadCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendNewNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(NewLeadCreated $event): void
    {
        $lead = $event->lead;

        Log::info('SendNewNotification listener fired', [
            'lead_id'    => $lead->id,
            'name'       => $lead->name,
            'phone'      => $lead->phone,
            'car_model'  => $lead->car_model,
            'created_at' => $lead->created_at->toDateTimeString(),
        ]);

        $text = " *New Lead*: {$lead->name}\n".
                " *ID*: {$lead->id}\n".
                " *Phone Number*: {$lead->phone}\n".
                " *Car Model*: {$lead->car_model}\n".
                " *Text*: {$lead->note}\n".
                " *Created At*: {$lead->created_at->format('Y-m-d H:i:s')}";

        Log::info('Prepared Telegram payload', [
            'chat_id'    => config('services.telegram.chat_id'),
            'text'       => $text,
            'parse_mode' => 'Markdown',
        ]);

        $response = Http::post(
            "https://api.telegram.org/bot".config('services.telegram.bot_token').'/sendMessage',
            [
                'chat_id' => config('services.telegram.chat_id'),
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]
        );

        Log::info('Telegram API response', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);
    }
}
