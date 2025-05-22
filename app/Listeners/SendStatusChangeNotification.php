<?php

namespace App\Listeners;

use App\Events\LeadStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendStatusChangeNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(LeadStatusChanged $event): void
    {
        $lead      = $event->lead;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;
        $manager   = optional($lead->manager)->name ?? '-';

        $text = " *Lead Status Changed*\n".
                " *ID*: {$lead->id}\n".
                " *From*: {$oldStatus}\n".
                " *To*: {$newStatus}\n".
                " *Manager*: {$manager}";

        $response = Http::post(
            "https://api.telegram.org/bot".config('services.telegram.bot_token')."/sendMessage",
            [
                'chat_id' => config('services.telegram.chat_id'),
                'text'    => $text,
                'parse_mode'  => 'Markdown',
            ]
        );

        Log::info("SendStatusChangeNotification fired for Lead #{$lead->id}");
        Log::info('Telegram API response: ' . $response->body());
    }
}
