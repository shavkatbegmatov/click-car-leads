<?php

namespace App\Console\Commands;

use App\Events\LeadStatusChanged;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelExpiredLeads extends Command
{
    protected $signature = 'leads:cancel-expired';

    protected $description = 'Cancel leads with status "new" older than 24 hours';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $cutoff = Carbon::now()->subDay();

        $this->info("Checking for leads older than {$cutoff}...");

        $expiredLeads = Lead::where('status', 'new')
            ->where('created_at', '<', $cutoff)
            ->get();

        if ($expiredLeads->isEmpty()) {
            $this->info('No expired leads found.');
            Log::info('[CancelExpiredLeads] No expired leads found');
            return 0;
        }

        $count = $expiredLeads->count();
        $this->info("Found {$count} leads older than {$cutoff}. Canceling...}");

        foreach ($expiredLeads as $lead) {
            $old = $lead->status;
            $lead->update(['status' => 'cancelled']);

            $message = "Lead #{$lead->id} (created at {$lead->created_at}) has been cancelled.";
            $this->line("  - {$message}");
            Log::info("[CancelExpiredLeads] {$message}");

            event(new LeadStatusChanged($lead, $old, 'cancelled'));

            $this->info("    >> Dispatched LeadStatusChanged for Lead #{$lead->id}");
            Log::info("[CancelExpiredLeads] Dispatched LeadStatusChanged for lead #{$lead->id}");

        }

        $this->info("Done. {$count} lead(s) have been cancelled.");
        Log::info("[CancelExpiredLeads] Completed: {$count} leads canceled.");
        return 0;
    }
}
