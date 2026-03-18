<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::query()->select('products.*');
        
        // Eager load variants for total quantity calculation
        $products = $products->with('variants');

        if ($request->search) {
            $products = $products->where(function($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('barcode', 'LIKE', "%{$request->search}%");
            });
        }
        if ($request->category) {
            $products = $products->where('category', $request->category);
        }
        
        // Sorting logic
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validating sort column and direction
        $allowedColumns = ['name', 'price', 'quantity', 'created_at'];
        if (!in_array($sortColumn, $allowedColumns)) $sortColumn = 'created_at';
        if (!in_array($sortDirection, ['asc', 'desc'])) $sortDirection = 'desc';

        // Add a subquery for the total quantity to allow sorting by the calculated sum
        $products = $products->addSelect(['total_stock' => Product::selectRaw('COALESCE(SUM(quantity), 0)')
            ->where(function($query) {
                $query->whereColumn('parent_id', 'products.id')
                      ->orWhereColumn('id', 'products.id');
            })
        ]);

        // Only show active products in the POS (JSON requests)
        if (request()->wantsJson()) {
            $products = $products->where('status', 1)
                                ->whereNull('parent_id')
                                ->orderBy('name', 'asc'); // Alphabetical order is better for POS
        } else {
            // In Admin list, show Parents first, then handle the user's sort
            $orderByColumn = ($sortColumn === 'quantity') ? 'total_stock' : $sortColumn;
            $products = $products->orderByRaw('parent_id IS NULL DESC')
                                ->orderBy($orderByColumn, $sortDirection);
        }
        
        // Use a larger limit for the POS/JSON requests
        $limit = request()->wantsJson() ? 100 : 10;
        $products = $products->paginate($limit);
        
        if (request()->wantsJson()) {
            return ProductResource::collection($products);
        }
        return view('products.index')->with('products', $products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parents = Product::whereNull('parent_id')->get();
        return view('products.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
        $image_path = '';

        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'variant_name' => $request->variant_name,
            'description' => $request->description,
            'image' => $image_path,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'status' => $request->status,
            'category' => $request->category
        ]);

        if (!$product) {
            return redirect()->back()->with('error', __('product.error_creating'));
        }
        return redirect()->route('products.index')->with('success', __('product.success_creating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $parents = Product::whereNull('parent_id')->where('id', '!=', $product->id)->get();
        return view('products.edit', compact('product', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product->parent_id = $request->parent_id;
        $product->name = $request->name;
        $product->variant_name = $request->variant_name;
        $product->description = $request->description;
        $product->barcode = $request->barcode;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->status = $request->status;
        $product->category = $request->category;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::delete($product->image);
            }
            // Store image
            $image_path = $request->file('image')->store('products', 'public');
            // Save to Database
            $product->image = $image_path;
        }

        if (!$product->save()) {
            return redirect()->back()->with('error', __('product.error_updating'));
        }
        return redirect()->route('products.index')->with('success', __('product.success_updating'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::delete($product->image);
        }
        $product->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
