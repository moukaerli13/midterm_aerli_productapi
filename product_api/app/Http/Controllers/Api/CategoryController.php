<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /** GET /api/categories */
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('category_name')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /** POST /api/categories */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'image' => 'required|string|max:255',
        ]);

        $category = Category::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    /** GET /api/categories/{id} */
    public function show(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $category]);
    }

    /** PUT/PATCH /api/categories/{id} */
    public function update(Request $request, string $id): JsonResponse
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $data = $request->validate([
            'category_name' => 'sometimes|required|string|max:50',
            'description' => 'nullable|string|max:255',
            'image' => 'sometimes|required|string|max:255',
        ]);

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    /** DELETE /api/categories/{id} */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        if ($category->products()->where('isDeleted', 0)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a category that still has products',
            ], 409);
        }

        // Remove soft-deleted products first so the foreign key allows the delete
        $category->products()->delete();
        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
    }
}
