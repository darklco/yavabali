<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use App\Http\Requests\IngredientRequest;
use Illuminate\Support\Str;

class IngredientController extends Controller
{
    /**
     * Menampilkan semua data ingredients.
     */
    public function index()
    {
        $ingredients = Ingredient::all();
        return response()->json([
            'status' => true,
            'data' => $ingredients
        ]);
    }

    /**
     * Menyimpan data baru ke tabel ingredients.
     */
    public function store(IngredientRequest $request)
    {
        $ingredient = Ingredient::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'image' => $request->image,
            'origin' => $request->origin,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Ingredient berhasil ditambahkan.',
            'data' => $ingredient
        ], 201);
    }

    /**
     * Menampilkan data berdasarkan ID.
     */
    public function show($id)
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return response()->json([
                'status' => false,
                'message' => 'Ingredient tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $ingredient
        ]);
    }

    /**
     * Mengupdate data berdasarkan ID.
     */
    public function update(IngredientRequest $request, $id)
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return response()->json([
                'status' => false,
                'message' => 'Ingredient tidak ditemukan.'
            ], 404);
        }

        $ingredient->update($request->only(['name', 'image', 'origin']));

        return response()->json([
            'status' => true,
            'message' => 'Ingredient berhasil diperbarui.',
            'data' => $ingredient
        ]);
    }

    /**
     * Menghapus data berdasarkan ID.
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return response()->json([
                'status' => false,
                'message' => 'Ingredient tidak ditemukan.'
            ], 404);
        }

        $ingredient->delete();

        return response()->json([
            'status' => true,
            'message' => 'Ingredient berhasil dihapus.'
        ]);
    }
}
