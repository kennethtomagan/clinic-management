<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();
    
        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
               ->orWhere('barcode', 'like', "%{$search}%");
        }
    
        // Apply sorting
        $orderBy = $request->input('order_by', 'id');
        $orderDirection = $request->input('order_direction', 'asc');
        $orderDirection = in_array(strtolower($orderDirection), ['asc', 'desc']) ? $orderDirection : 'asc';
    
        $query->orderBy($orderBy, $orderDirection);
    
        // Handle pagination
        $perPage = $request->input('per_page', 10);
        $products = $query->paginate($perPage)->appends($request->except('page'));

        return ProductResource::collection($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = new Product();
        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->barcode = $request->barcode;
        $product->qty = $request->qty;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->is_visible = $request->is_visible ? 1 : 0;
        
        // Handle image upload if provided
        if ($request->hasFile('image')) {
            // Add the new image to the 'product-images' collection
            $product->clearMediaCollection('product-images'); // Clears old image if any

            $product->addMedia($request->file('image'))
                    ->toMediaCollection('product-images');
        }

        $product->save();

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return new ProductResource($product);
    }

    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->barcode = $request->barcode;
        $product->qty = $request->qty;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->is_visible = $request->is_visible ? 1 : 0;
        
        // Handle image upload if provided
        if ($request->hasFile('image')) {
            // Add the new image to the 'product-images' collection
            $product->clearMediaCollection('product-images'); // Clears old image if any

            $product->addMedia($request->file('image'))
                    ->toMediaCollection('product-images');
        }

        $product->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
