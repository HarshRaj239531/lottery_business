<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MaterialStock;
use App\Helpers\ApiResponse;

class MaterialStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stocks = MaterialStock::with('material')->latest()->get();
        return ApiResponse::success($stocks, 'Material stocks fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'nullable|exists:materials,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:success,pending,failed',
            'type' => 'nullable|string|in:credit,debit',
        ]);

        $stock = MaterialStock::create($validated);
        return ApiResponse::success($stock->load(['material', 'user']), 'Material stock created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stock = MaterialStock::with(['material', 'user'])->findOrFail($id);
        return ApiResponse::success($stock, 'Material stock details fetched');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $stock = MaterialStock::findOrFail($id);

        $validated = $request->validate([
            'material_id' => 'nullable|exists:materials,id',
            'user_id' => 'nullable|exists:users,id',
            'title' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:success,pending,failed',
            'type' => 'nullable|string|in:credit,debit',
        ]);

        $stock->update($validated);
        return ApiResponse::success($stock->load(['material', 'user']), 'Material stock updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stock = MaterialStock::findOrFail($id);
        $stock->delete();
        return ApiResponse::success(null, 'Material stock deleted successfully');
    }
}
