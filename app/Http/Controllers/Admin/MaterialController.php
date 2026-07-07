<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_url' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $data = [
            'name' => $validated['name'],
            'price' => $validated['price'],
            'unit' => $validated['unit'],
            'status' => $validated['status'] ?? 'active',
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('materials', 'public');
            $data['image_url'] = Storage::disk('public')->url($path);
        } elseif ($request->filled('image_url')) {
            $data['image_url'] = $validated['image_url'];
        }

        $material = Material::create($data);
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_url' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $data = [];

        if ($request->has('name')) {
            $data['name'] = $validated['name'];
        }
        if ($request->has('price')) {
            $data['price'] = $validated['price'];
        }
        if ($request->has('unit')) {
            $data['unit'] = $validated['unit'];
        }
        if ($request->has('status')) {
            $data['status'] = $validated['status'];
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('materials', 'public');
            $data['image_url'] = Storage::disk('public')->url($path);
        } elseif ($request->has('image_url')) {
            $data['image_url'] = $validated['image_url'];
        }

        $material->update($data);
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
