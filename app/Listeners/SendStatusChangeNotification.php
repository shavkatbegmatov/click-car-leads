<?php

namespace App\Listeners;

use App\Events\LeadStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class SendStatusChangeNotification
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(LeadStatusChanged $event): void
    {
        $lead = $event->lead;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;
        $manager = optional($lead->manager)->name ?? '-';

        $text = " *Lead Status Changed*\n".
                " *ID*: _{$lead->id}_\n".
                " *From*: _{$oldStatus}_\n".
                " *To*: _{$newStatus}_\n".
                " *Manager*: _{$manager}_";

        Http::post("https://api.telegram.org/bot".config('services.telegram.bot_token')."/sendMessage", [
            'chat_id' => config('services.telegram.chat_id'),
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }
}
