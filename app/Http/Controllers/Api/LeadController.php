<?php

namespace App\Http\Controllers\Api;

use App\Events\LeadStatusChanged;
use App\Events\NewLeadCreated;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    public function index(): JsonResponse
    {
        Log::info(__METHOD__ . ' called');

        $leads = Lead::with('manager')
            ->orderByDesc('created_at')
            ->get();

        Log::info(__METHOD__ . ' returning ' . $leads->count() . ' leads');

        return response()->json($leads);
    }

    public function store(Request $request): JsonResponse
    {
        Log::info(__METHOD__ . ' called', [
            'payload' => $request->all(),
        ]);

        $data = $request->validate([
            'name'      => 'required|string',
            'phone'     => 'required|string',
            'car_model' => 'required|string',
            'note'      => 'nullable|string',
        ]);

        Log::info(__METHOD__ . ' validation passed', [
            'validated' => $data,
        ]);

        $lead = Lead::create($data);

        Log::info(__METHOD__ . ' lead created', [
            'lead_id' => $lead->id,
        ]);

        event(new NewLeadCreated($lead));

        Log::info(__METHOD__ . ' NewLeadCreated event dispatched', [
            'lead_id' => $lead->id,
        ]);

        return response()->json($lead, 201);
    }

    public function assign(Request $request, Lead $lead): JsonResponse
    {
        Log::info(__METHOD__ . ' called', [
            'lead_id'    => $lead->id,
            'old_status' => $lead->status,
            'payload'    => $request->all(),
        ]);

        $data = $request->validate([
            'manager_id' => 'required|exists:managers,id',
        ]);

        Log::info(__METHOD__ . ' validation passed', [
            'validated' => $data,
        ]);

        if ($lead->status !== 'new') {
            Log::warning(__METHOD__ . ' invalid status for assignment', [
                'lead_id'    => $lead->id,
                'status'     => $lead->status,
            ]);

            return response()->json([
                'error' => 'Only NEW leads can be assigned'
            ], 400);
        }

        $oldStatus = $lead->status;

        $lead->update([
            'manager_id' => $data['manager_id'],
            'status'     => 'in_progress',
        ]);

        Log::info(__METHOD__ . ' lead updated', [
            'lead_id'    => $lead->id,
            'old_status' => $oldStatus,
            'new_status' => $lead->status,
        ]);

        event(new LeadStatusChanged($lead, $oldStatus, $lead->status));

        Log::info(__METHOD__ . ' LeadStatusChanged event dispatched', [
            'lead_id'    => $lead->id,
            'old_status' => $oldStatus,
            'new_status' => $lead->status,
        ]);

        return response()->json($lead, 200);
    }

    public function updateStatus(Request $request, Lead $lead): JsonResponse
    {
        Log::info(__METHOD__ . ' called', [
            'lead_id'    => $lead->id,
            'old_status' => $lead->status,
            'payload'    => $request->all(),
        ]);

        $data = $request->validate([
            'status' => 'required|in:completed,cancelled',
        ]);

        Log::info(__METHOD__ . ' validation passed', [
            'validated' => $data,
        ]);

        if ($lead->status !== 'in_progress') {
            Log::warning(__METHOD__ . ' invalid status transition', [
                'lead_id'    => $lead->id,
                'status'     => $lead->status,
                'new_status' => $data['status'],
            ]);

            return response()->json([
                'error' => 'Only IN_PROGRESS leads can be updated'
            ], 400);
        }

        $oldStatus = $lead->status;

        $lead->update([
            'status' => $data['status'],
        ]);

        Log::info(__METHOD__ . ' lead status updated', [
            'lead_id'    => $lead->id,
            'old_status' => $oldStatus,
            'new_status' => $lead->status,
        ]);

        event(new LeadStatusChanged($lead, $oldStatus, $lead->status));

        Log::info(__METHOD__ . ' LeadStatusChanged event dispatched', [
            'lead_id'    => $lead->id,
            'old_status' => $oldStatus,
            'new_status' => $lead->status,
        ]);

        return response()->json($lead, 200);
    }
}
