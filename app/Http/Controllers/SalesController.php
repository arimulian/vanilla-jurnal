<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesCreateRequest;
use App\Models\Sales;
use App\Services\SalesService;
use Illuminate\Http\Request;

class SalesController extends Controller
{


    public function __construct(protected SalesService $salesService) {}
    public function store(SalesCreateRequest $request)
    {
        $validated = $request->validated();
        $sales = $this->salesService->create($validated);

        return response()->json([
            'message' => 'Sales created successfully',
            'data' => $sales
        ], 201);
    }
}
