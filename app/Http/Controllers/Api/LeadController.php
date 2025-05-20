<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(): \Illuminate\Database\Eloquent\Collection
    {
        return Lead::with('manager')->orderByDesc('created_at')->get();
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'car_model' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $lead = Lead::create($data);

        // Telegram

        return response()->json($lead, 201);
    }

    public function assign(Request $request, Lead $lead): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'manager_id' => 'required|exists:manager_id',
        ]);

        if ($lead->status !== 'new') {
            return response()->json(['error' => 'Only NEW leads can be assigned'], 400);
        }

        $lead->update([
            'manager_id' => $data['manager_id'],
            'status' => 'in_progress',
        ]);

        // Telegram

        return response()->json($lead, 200);
    }

    public function updateStatus(Request $request, Lead $lead): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:completed,cancelled',
        ]);

        if ($lead->status !== 'in_progress') {
            return response()->json(['error' => 'Only IN_PROGRESS leads can be updated'], 400);
        }

        $lead->update(['status' => $data['status']]);

        // Telegram

        return response()->json($lead, 200);
    }
}
