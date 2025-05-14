<?php

namespace App\Http\Controllers;

use App\Models\ProductEcommerce;
use App\Http\Requests\ProductEcommerceRequest;
use App\Http\Resources\ProductEcommerceResource;
use Illuminate\Http\Response;

class ProductEcommerceController extends Controller
{
    public function index()
    {
        $productEcommerces = ProductEcommerce::paginate(10);
        return ProductEcommerceResource::collection($productEcommerces);
    }

    public function store(ProductEcommerceRequest $request)
    {
        $validated = $request->validated();
        $productEcommerce = ProductEcommerce::create($validated);

        return (new ProductEcommerceResource($productEcommerce))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ProductEcommerce $productEcommerce)
    {
        return new ProductEcommerceResource($productEcommerce);
    }

    public function update(ProductEcommerceRequest $request, ProductEcommerce $productEcommerce)
    {
        $productEcommerce->update($request->validated());

        return new ProductEcommerceResource($productEcommerce);
    }

    public function destroy(ProductEcommerce $productEcommerce)
    {
        $productEcommerce->delete();

        return response()->noContent();
    }
}
