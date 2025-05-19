<?php

namespace App\Http\Controllers;

use App\Models\Variant;
use Illuminate\Support\Str;
use App\Http\Requests\VariantRequest;
use App\Http\Resources\VariantResource;

class VariantController extends Controller
{
    // GET /api/variant
    public function index()
    {
        return VariantResource::collection(Variant::all());
    }

    // POST /api/variant
    public function store(VariantRequest $request)
    {
        $validated = $request->validated();

        $variant = Variant::create([
            'id' => Str::uuid(),
            'product_id' => $validated['product_id'],
            'size' => json_encode($validated['size'] ?? []),
            'image_front' => json_encode($validated['image_front'] ?? []),
            'image_back' => json_encode($validated['image_back'] ?? []),
        ]);

        return new VariantResource($variant);
    }

    // GET /api/variant/{id}
    public function show($id)
    {
        $variant = Variant::find($id);
        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        return new VariantResource($variant);
    }

    // PUT /api/variant/{id}
    public function update(VariantRequest $request, $id)
    {
        $variant = Variant::find($id);
        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        $validated = $request->validated();

        $variant->update([
            'size' => json_encode($validated['size'] ?? json_decode($variant->size)),
            'image_front' => json_encode($validated['image_front'] ?? json_decode($variant->image_front)),
            'image_back' => json_encode($validated['image_back'] ?? json_decode($variant->image_back)),
        ]);

        return new VariantResource($variant);
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
