<?php

namespace App\Events;

use App\Models\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewLeadCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lead $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;

        Log::info('NewLeadCreated event instantiated', [
            'lead_id'   => $lead->id,
            'name'      => $lead->name,
            'phone'     => $lead->phone,
            'car_model' => $lead->car_model,
            'created_at'=> $lead->created_at->toDateTimeString(),
        ]);
    }
}
