<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *     title="Yavabali API",
 *     version="1.0",
 *     description="API untuk mengelola data bahan makanan"
 * )
 *
 * @OA\Tag(
 *     name="Ingredients",
 *     description="API terkait data bahan makanan"
 * )
 */
class IngredientController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ingredients",
     *     summary="Get all ingredients",
     *     tags={"Ingredients"},
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function index()
    {
        $ingredients = Ingredient::paginate(15);
      
        return response()->json([
            'status' => 'success',
            'data' => $ingredients->items(),
            'meta' => [
                'total' => $ingredients->total(),
                'per_page' => $ingredients->perPage(),
                'current_page' => $ingredients->currentPage(),
                'last_page' => $ingredients->lastPage(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/ingredients",
     *     summary="Create new ingredient",
     *     tags={"Ingredients"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"NAME"},
     *             @OA\Property(property="NAME", type="string"),
     *             @OA\Property(property="origin", type="string"),
     *             @OA\Property(property="image", type="string", format="binary"),
     *             @OA\Property(property="nutritional_info", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="properties", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ingredient created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NAME' => 'required|string|max:255',
            'origin' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nutritional_info' => 'nullable|array',
            'properties' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $ingredient = new Ingredient();
        $ingredient->NAME = $request->NAME;
        $ingredient->slug = Str::slug($request->NAME);
        $ingredient->origin = $request->origin;
        $ingredient->nutritional_info = $request->nutritional_info;
        $ingredient->properties = $request->properties;
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('ingredients', 'public');
            $ingredient->image = $path;
        }
        
        $ingredient->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ingredient created successfully',
            'data' => $ingredient
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ingredients/{slug}",
     *     summary="Show ingredient by slug",
     *     tags={"Ingredients"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ingredient not found"
     *     )
     * )
     */
    public function show($slug)
    {
        $ingredient = Ingredient::where('slug', $slug)->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'data' => $ingredient
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/ingredients/{id}",
     *     summary="Update ingredient",
     *     tags={"Ingredients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="NAME", type="string"),
     *             @OA\Property(property="origin", type="string"),
     *             @OA\Property(property="image", type="string", format="binary"),
     *             @OA\Property(property="nutritional_info", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="properties", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ingredient updated successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'NAME' => 'sometimes|string|max:255',
            'origin' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nutritional_info' => 'nullable|array',
            'properties' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('NAME')) {
            $ingredient->NAME = $request->NAME;
            $ingredient->slug = Str::slug($request->NAME);
        }
        
        if ($request->has('origin')) {
            $ingredient->origin = $request->origin;
        }
        
        if ($request->has('nutritional_info')) {
            $ingredient->nutritional_info = $request->nutritional_info;
        }
        
        if ($request->has('properties')) {
            $ingredient->properties = $request->properties;
        }
        
        if ($request->hasFile('image')) {
            if ($ingredient->image) {
                Storage::disk('public')->delete($ingredient->image);
            }
            
            $path = $request->file('image')->store('ingredients', 'public');
            $ingredient->image = $path;
        }
        
        $ingredient->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ingredient updated successfully',
            'data' => $ingredient
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/ingredients/{id}",
     *     summary="Delete ingredient",
     *     tags={"Ingredients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ingredient deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ingredient not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        
        if ($ingredient->image) {
            Storage::disk('public')->delete($ingredient->image);
        }
        
        $ingredient->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ingredient deleted successfully'
        ]);
    }
}
