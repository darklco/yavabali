<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::query();
        
        // Filter by type if provided
        if ($request->has('type')) {
            $query->whereJsonContains('types', $request->type);
        }
        
        // Search by title if provided
        // if ($request->has('search')) {
        //     $query->where('title', 'like', '%' . $request->search . '%');
        // }
        
        // Sort if provided
        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // Pagination
        $perPage = $request->input('per_page', 15);
        $products = $query->paginate($perPage);
        
        return new ProductCollection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        
        // Generate slug from title if not provided
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Create product
        $product = Product::create($validated);
        
        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        // Load variants with ecommerces
        $product->load(['variants', 'variants.ecommerces']);
        
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        
        // Update product
        $product->update($validated);
        
        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        
        return response()->json(['message' => 'Product deleted successfully']);
    }
    
    /**
     * Find product by slug.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function findBySlug($slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['variants', 'variants.ecommerces'])
            ->firstOrFail();
        
        return new ProductResource($product);
    }
    
    /**
     * Find products by type.
     *
     * @param  int  $typeId
     * @return \Illuminate\Http\Response
     */
    public function findByType($typeId)
    {
        $products = Product::whereJsonContains('types', $typeId)
            ->with(['variants'])
            ->paginate(15);
        
        return new ProductCollection($products);
    }
}