<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductCreateRequest;


class ProductController extends Controller
{
    public function create(ProductCreateRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $products = Product::query()->create([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'cost_price' => $validatedData['cost_price'],
            'stock' => $validatedData['stock'],
            'description' => $validatedData['description'] ?? null,
            'category_id' => $validatedData['category_id'],
            'sku' => $validatedData['sku'] ?? null,
            'barcode' => $validatedData['barcode'] ?? null,
            'image' => $validatedData['image'] ?? null,
            'slug' => Str::slug($validatedData['name']),
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $products,
        ], 201);
    }
}
