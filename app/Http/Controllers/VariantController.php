<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Variant;

class VariantController extends Controller
{
    // GET /api/variant
    public function index()
    {
        return response()->json(Variant::all());
    }

    // POST /api/variant
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|uuid|exists:products,id',
            'size' => 'nullable|array',
            'image_front' => 'nullable|array',
            'image_back' => 'nullable|array',
        ]);

        $variant = Variant::create([
            'id' => Str::uuid(),
            'product_id' => $validated['product_id'],
            'size' => json_encode($validated['size']),
            'image_front' => json_encode($validated['image_front']),
            'image_back' => json_encode($validated['image_back']),
        ]);

        return response()->json($variant, 201);
    }

    // GET /api/variant/{id}
    public function show($id)
    {
        $variant = Variant::find($id);
        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }
        return response()->json($variant);
    }

    // PUT /api/variant/{id}
    public function update(Request $request, $id)
    {
        $variant = Variant::find($id);
        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        $validated = $request->validate([
            'size' => 'nullable|array',
            'image_front' => 'nullable|array',
            'image_back' => 'nullable|array',
        ]);

        $variant->update([
            'size' => json_encode($validated['size'] ?? $variant->size),
            'image_front' => json_encode($validated['image_front'] ?? $variant->image_front),
            'image_back' => json_encode($validated['image_back'] ?? $variant->image_back),
        ]);

        return response()->json($variant);
    }

    // DELETE /api/variant/{id}
    public function destroy($id)
    {
        $variant = Variant::find($id);
        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        $variant->delete();
        return response()->json(['message' => 'Variant deleted']);
    }
}
