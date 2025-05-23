<?php

namespace App\Console\Commands;

use App\Events\LeadStatusChanged;
use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelExpiredLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel leads with status "new" older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoff = now()->subDay();
        $expiredLeads = Lead::where('status', 'new')
            ->where('created_at', '<', $cutoff)
            ->get();

        if ($expiredLeads->isEmpty()) {
            Log::info('[CancelExpiredLeads] No expired leads found');
            return 0;
        }

        foreach ($expiredLeads as $lead) {
            $old = $lead->status;
            $lead->update(['status' => 'cancelled']);

            Log::info("[CancelExpiredLeads] Lead #{$lead->id} cancelled (created at {$lead->created_at})");
            event(new LeadStatusChanged($lead, $old, 'cancelled'));
        }

        Log::info('[CancelExpiredLeads] Completed: ' . $expiredLeads->count() . ' leads canceled.');
        return 0;
    }
}
