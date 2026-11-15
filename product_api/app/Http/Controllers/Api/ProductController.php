<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    private const IMAGE_DIR = 'products';

    /** GET /api/products  (optional ?search=...&category_id=...) */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category')->where('isDeleted', 0);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ProductName', 'like', "%{$search}%")
                    ->orWhere('Barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->query('category_id')) {
            $query->where('CategoryID', $categoryId);
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderByDesc('ProductID')->get(),
        ]);
    }

    /**
     * POST /api/products
     * Send as multipart/form-data; the picture goes in the "image" file field.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        unset($data['image'], $data['remove_image']);

        if ($request->hasFile('image')) {
            $data['ProductImage'] = $request->file('image')->store(self::IMAGE_DIR, 'public');
        }

        $product = Product::create($data)->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /** GET /api/products/{id} */
    public function show(string $id): JsonResponse
    {
        $product = $this->findActive($id);

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $product->load('category')]);
    }

    /**
     * PUT/PATCH /api/products/{id}
     * For file upload send POST with field _method=PUT (PHP can't read multipart PUT).
     * Send remove_image=1 to delete the current picture.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = $this->findActive($id);

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $data = $request->validate($this->rules($product->ProductID, partial: true));
        unset($data['image'], $data['remove_image']);

        if ($request->hasFile('image')) {
            $this->deleteImage($product->ProductImage);
            $data['ProductImage'] = $request->file('image')->store(self::IMAGE_DIR, 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($product->ProductImage);
            $data['ProductImage'] = null;
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->fresh('category'),
        ]);
    }

    /** DELETE /api/products/{id}  (soft delete: isDeleted = 1) */
    public function destroy(string $id): JsonResponse
    {
        $product = $this->findActive($id);

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $product->update(['isDeleted' => 1]);

        return response()->json(['success' => true, 'message' => 'Product deleted successfully']);
    }

    /** GET /api/product-images/{filename}  – returns the stored image file */
    public function image(string $filename): StreamedResponse
    {
        $path = self::IMAGE_DIR.'/'.$filename;

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, null, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function findActive(string $id): ?Product
    {
        return Product::where('ProductID', $id)->where('isDeleted', 0)->first();
    }

    private function deleteImage(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }

    private function rules(?int $ignoreId = null, bool $partial = false): array
    {
        $req = $partial ? 'sometimes|required' : 'required';

        return [
            'ProductName' => "$req|string|max:100",
            'Description' => 'nullable|string|max:255',
            'CategoryID' => "$req|integer|exists:categories,category_id",
            'Barcode' => [
                'nullable', 'string', 'max:255',
                Rule::unique('products', 'Barcode')->ignore($ignoreId, 'ProductID'),
            ],
            'Expiredate' => 'nullable|date',
            'Qty' => 'sometimes|integer|min:0',
            'UnitPriceIn' => 'sometimes|numeric|min:0',
            'UnitPriceOut' => 'sometimes|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120', // 5 MB
            'remove_image' => 'sometimes|boolean',
        ];
    }
}
