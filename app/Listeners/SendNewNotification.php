<?php

namespace App\Listeners;

use App\Events\NewLeadCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class SendNewNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(NewLeadCreated $event): void
    {
        $lead = $event->lead;
        $text = " *New Lead*: {$lead->name}\n".
                " *Phone Number*: {$lead->phone}\n".
                " *Car Model*: {$lead->car_model}\n".
                " *Text*: {$lead->note}\n".
                " *Created At*: {$lead->created_at->format('Y-m-d H:i:s')}";

        Http::post("https://api.telegram.org/bot".config('services.telegram.bot_token').'/sendMessage',[
            'chat_id' => config('services.telegram.chat_id'),
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }
}
