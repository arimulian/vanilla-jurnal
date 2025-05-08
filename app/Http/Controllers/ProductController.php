<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function create(ProductCreateRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        // Check if the product already exists
        $existingProduct = Product::where('name', $validatedData['name'])->first();
        if ($existingProduct) {
            return response()->json([
                'message' => 'Product already exists',
            ], 409);
        }
        $products = Product::query()->create([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'cost_price' => $validatedData['cost_price'],
            'stock' => $validatedData['stock'],
            'description' => $validatedData['description'] ?? null,
            'category_id' => $validatedData['category_id'],
            'sku' => $validatedData['sku'] ?? null,
            'barcode' => $validatedData['barcode'] ?? null,
        ]);
        //if image is present, save it
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $path = $file->storeAs('products', $filename, 'public');
            $products->image = $path;
            $products->save();
        }

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $products,
        ], 201);
    }

    public function get(): JsonResponse
    {
        $products = Product::all();
        return response()->json([
            'data' => $products,
        ], 200);
    }

    public function getBySlug(string $slug): JsonResponse
    {
        $product = Product::where('slug', '=', $slug)->first();
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }
        return response()->json([
            'data' => $product,
        ], 200);
    }

    public function update(ProductUpdateRequest $request, string $slug): JsonResponse
    {
        $validatedData = $request->validated();
        $product = Product::where('slug', '=', $slug)->first();
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        $product->update([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'cost_price' => $validatedData['cost_price'],
            'stock' => $validatedData['stock'],
            'description' => $validatedData['description'] ?? null,
            'category_id' => $validatedData['category_id'],
            'sku' => $validatedData['sku'] ?? null,
            'barcode' => $validatedData['barcode'] ?? null,
        ]);
        // Check if the image is present in the request
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Store the new image
            $file = $request->file('image');
            $filename = $file->hashName();
            $path = $file->storeAs('products', $filename, 'public');
            $product->image = $path;
            $product->save();
        }
        // Update the slug if the name has changed
        if ($product->wasChanged('name')) {
            $product->slug = Str::slug($validatedData['name']);
            $product->save();
        }

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ], 200);
    }

    public function delete(string $slug): JsonResponse
    {
        $product = Product::where('slug', '=', $slug)->first();
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }
        // Delete the image if it exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return response()->json([
            'data' => true
        ], 200);
    }
}
