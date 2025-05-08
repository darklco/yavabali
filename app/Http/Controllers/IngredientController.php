<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IngredientController extends Controller
{
    /**
     * Display a listing of ingredients
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Store a newly created ingredient
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
     * Display the specified ingredient
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
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
     * Update the specified ingredient
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
            // Delete old image if exists
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
     * Remove the specified ingredient
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        
        // Delete image if exists
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