<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function index(): \Illuminate\Database\Eloquent\Collection
    {
        return Manager::all();
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $manager = \App\Models\Manager::create($data);

        return response()->json($manager, 201);
    }
}
