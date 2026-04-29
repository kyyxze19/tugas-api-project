<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan semua data product.
     * Membutuhkan token dengan ability: product-list
     */
    public function index(): JsonResponse
    {
        $products = Product::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar product berhasil diambil.',
            'data' => $products,
        ], 200);
    }

    /**
     * Menyimpan data product baru.
     * Membutuhkan token dengan ability: product-store
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product berhasil ditambahkan.',
            'data' => $product,
        ], 201);
    }
}
