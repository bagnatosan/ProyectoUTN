<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Helpers\UnitConverter;
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
    // 1. Traemos los productos reales del negocio actual
    $businessProfileId = auth()->user()->businessProfile?->id ?? 1;
    $products = Product::where('business_profile_id', $businessProfileId)->get();

    // 2. Calculamos los contadores dinámicos para las tarjetitas de arriba
    $totalProductos = $products->count();
    $activos = $products->where('is_active', true)->count();
    $inactivos = $products->where('is_active', false)->count();

    // 3. Pasamos todo a la vista de tu compañero (revisá cómo se llama su vista, ej: 'products.index')
    return view('products.index', compact('products', 'totalProductos', 'activos', 'inactivos'));
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
    // 1. Validamos los datos básicos que vienen del formulario
    $request->validate([
        'name'        => 'required|string|max:255',
        'description'=> 'nullable|string',
        'price'       => 'required|numeric|min:0',
        'category_id' => 'required', // Le exigimos que venga de la vista
    ]);

    // 2. RESCATE DE INTEGRIDAD 1: ID del negocio (el que ya arreglamos antes)
    $businessProfileId = auth()->user()->businessProfile?->id 
        ?? \DB::table('business_profiles')->where('user_id', auth()->id())->value('id') 
        ?? 1;

    // 3. RESCATE DE INTEGRIDAD 2: ID de la Categoría
    // Intentamos agarrar lo que el usuario seleccionó en el combo de la pantalla.
    // Si por alguna razón viaja vacío, buscamos la primera categoría real que exista en Laragon,
    // y si la tabla está virgen, le mandamos el ID 1 como último recurso.
    $categoryId = $request->category_id 
        ?? \DB::table('categories')->value('id') 
        ?? 1;

    // 4. Creamos el producto pasándole TODOS los campos obligatorios
    $product = Product::create([
        'name'                => $request->name,
        'description'         => $request->description,
        'price'               => $request->price,
        'status'              => $request->status ?? 'active',
        'business_profile_id' => $businessProfileId,
        'category_id'         => $categoryId, // <--- ¡Con esto matamos el último error!
    ]);

    // 5. Te mandamos directo a tu pantalla de costos premium
    return redirect('/recipes/' . $product->id . '/edit')->with('success', '¡Producto creado con éxito! Ahora asignale sus ingredientes.');
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
            'custom_margin' => $request->filled('custom_margin') ? $request->custom_margin : null,
        ];

        if($request->hasFile('image')) 
            $data['image'] = $this->ImagePath($request);

        $product->update($data);

        // El margen pudo haber cambiado: recalculamos el precio sugerido con el costo ya guardado
        $this->recalculateSuggestedPrice($product);

        return redirect()->route('products.index')->with('success', 'Producto actualizado con éxito.');
    }

    /**
     * Recalcula el precio sugerido de un producto usando su costo estimado ya guardado
     * y el margen vigente (personalizado del producto, o el general del negocio).
     */
    private function recalculateSuggestedPrice(Product $product)
    {
        $product->refresh()->loadMissing('businessProfile');

        $margin = $product->custom_margin
            ?? $product->businessProfile?->profit_margin
            ?? 3;

        $product->suggested_price = ($product->estimated_cost ?? 0) * $margin;
        $product->save();
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
        if($product->business_profile_id == Auth::user()->businessProfile->id)
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
            'custom_margin' => 'nullable|numeric|min:1|max:50',
        ];

        return $data;
    }

}
