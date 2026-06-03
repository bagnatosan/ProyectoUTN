<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // Aquí puedes definir tu middleware usando una función
            new Middleware(function ($request, $next) {
                if ($request->user()->role !== 'seller') {
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    

    public function index()
    {
        return view('products.index');
    }

    
    public function create(Request $request)
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));

    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->DataValidation());


        $product = Product::create([
            'business_profile_id' => Auth::user()->businessProfile->id,
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'image' => $this->ImagePath($request), //2mb
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('products.index')->with('success', 'Producto creado con éxito.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate($this->DataValidation());

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'image' => $this->ImagePath($request), //2mb
            'is_active' => $request->is_active,
        ];

        if($request->hasFile('image')) 
            $data['image'] = $this->ImagePath($request);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Producto actualizado con éxito.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        if($this->productBelongsToSeller($product)) //validar que el producto sea del vendedor logueado
            $product->delete();
        else 
            abort(403);


        return redirect()->route('products.index')->with('success', 'Producto eliminado con exito.');
    }

    public function ChangeStatement(Request $request, Product $product)
    {
        if($this->productBelongsToSeller($product)){
            if($product->is_active)
                $product->is_active = false;
            else
                $product->is_active = true;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'mensaje' => 'Actualizado correctamente el estado del producto',
            'state' => $product->is_active
        ]);
    }

    public function ImagePath(Request $request)
    {
        $imagePath = null;

        if($request->hasFile('image'))
            $imagePath = $request->file('image')->store('products' , 'public');

        return $imagePath;
    }

    public function productBelongsToSeller(Product $product)
    {
        if($product->business_profile_id === Auth::user()->businessProfile->id)
            return true;
        else 
            return false;
    }

    public function DataValidation()
    {
        $data = [
            'name' => 'required|string|max:30',
            'description' => 'nullable|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048', //2mb
            'is_active' => 'required|boolean',
        ];

        return $data;
    }

}
