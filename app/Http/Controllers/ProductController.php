<?php

namespace App\Http\Controllers;

use App\Models\Product;


use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
       
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products');
            $validated['image'] = $path;
        }

        return Product::create($validated);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products');
            $validated['image'] = $path;
        }

        $product->update($validated);
        return $product;
    }

    public function destroy(Product $product)
    {
       
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return response()->json(['message' => 'Product restored']);
    }
}
