<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryUpdateRequest;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function create(CategoryCreateRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        if (Category::query()->where('name', $validatedData['name'])->exists()) {
            return response()->json([
                'message' => 'Category already exists',
            ], 422);
        }
        $category = Category::query()->create([
            'name' => $validatedData['name'],
            'category_type' => $validatedData['category_type'],
            'slug' => Str::slug($validatedData['name']),
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    public function update(CategoryUpdateRequest $request, Category $category): JsonResponse
    {
        $validatedData = $request->validated();
        $category->update([
            'name' => $validatedData['name'],
            'category_type' => $validatedData['category_type'],
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category,
        ]);
    }

    public function get(): JsonResponse
    {
        $categories = Category::query()->get();
        return response()->json([
            'data' => $categories,
        ]);
    }

    public function delete(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Category cannot be deleted because it has products',
            ], 422);
        }
        $category->query()->where('slug', $category->slug)->delete();
        return response()->json([

            'data' => true,
        ]);
    }
}
