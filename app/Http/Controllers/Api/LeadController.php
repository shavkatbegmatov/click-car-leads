<?php

namespace App\Http\Controllers\Api;

use App\Events\LeadStatusChanged;
use App\Events\NewLeadCreated;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(): Collection
    {
        return Lead::with('manager')
            ->orderByDesc('created_at')
            ->get();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'required|string',
            'phone'     => 'required|string',
            'car_model' => 'required|string',
            'note'      => 'nullable|string',
        ]);

        $lead = Lead::create($data);

        // Telegram
        event(new NewLeadCreated($lead));

        return response()->json($lead, 201);
    }

    public function assign(Request $request, Lead $lead): JsonResponse
    {
        $data = $request->validate([
            'manager_id' => 'required|exists:managers,id',
        ]);

        if ($lead->status !== 'new') {
            return response()->json([
                'error' => 'Only NEW leads can be assigned'
            ], 400);
        }

        $oldStatus = $lead->status;

        $lead->update([
            'manager_id' => $data['manager_id'],
            'status'     => 'in_progress',
        ]);

        event(new LeadStatusChanged($lead, $oldStatus, $lead->status));

        return response()->json($lead, 200);
    }

    public function updateStatus(Request $request, Lead $lead): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:completed,cancelled',
        ]);

        if ($lead->status !== 'in_progress') {
            return response()->json([
                'error' => 'Only IN_PROGRESS leads can be updated'
            ], 400);
        }

        $oldStatus = $lead->status;

        $lead->update([
            'status' => $data['status'],
        ]);

        event(new LeadStatusChanged($lead, $oldStatus, $lead->status));

        return response()->json($lead, 200);
    }
}
