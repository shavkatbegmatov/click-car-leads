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

        Log::info('SendStatusChangeNotification listener fired', [
            'lead_id'    => $lead->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'manager'    => $manager,
        ]);

        $text2 = "*Lead Status Changed*\n".
                "*ID*: {$lead->id}\n".
                "*From*: {$oldStatus}\n".
                "*To*: {$newStatus}\n".
                "*Manager*: {$manager}";

        $text3 = "*Lead Status Changed*\n".
            "*ID*: {$lead->id}\n".
            "*From*: {$oldStatus}\n".
            "*To*: {$newStatus}";

        $text = sprintf(
            '<b>Lead Status Changed</b>%s' .
            '<b>ID</b>: %d%s' .
            '<b>From</b>: %s%s' .
            '<b>To</b>: %s%s' .
            '<b>Manager</b>: %s',
            PHP_EOL,
            $lead->id, PHP_EOL,
            e($oldStatus), PHP_EOL,
            e($newStatus), PHP_EOL,
            e($manager)
        );

        Log::info('Prepared Telegram payload', [
            'chat_id'    => config('services.telegram.chat_id'),
            'text'       => $text,
            'parse_mode' => 'HTML',
        ]);

        $response = Http::post(
            "https://api.telegram.org/bot".config('services.telegram.bot_token')."/sendMessage",
            [
                'chat_id'    => config('services.telegram.chat_id'),
                'text'       => $text,
                'parse_mode' => 'HTML',
            ]
        );

        Log::info('Telegram API response', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);
    }
}
