<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Material;
use App\Helpers\ApiResponse;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materials = Material::latest()->get();
        return ApiResponse::success($materials, 'Materials fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'image_url' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $material = Material::create($validated);
        return ApiResponse::success($material, 'Material created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $material = Material::findOrFail($id);
        return ApiResponse::success($material, 'Material details fetched');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'image_url' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $material->update($validated);
        return ApiResponse::success($material, 'Material updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $material = Material::findOrFail($id);
        $material->delete();
        return ApiResponse::success(null, 'Material deleted successfully');
    }
}
