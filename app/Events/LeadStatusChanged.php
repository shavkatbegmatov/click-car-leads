<?php

namespace App\Events;

use App\Models\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lead $lead;
    public string $oldStatus;
    public string $newStatus;

    public function __construct(Lead $lead, string $oldStatus, string $newStatus)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
