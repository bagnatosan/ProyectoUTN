<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Helpers\UnitConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if ($request->user()->role !== 'seller') {
                    abort(403);
                }
                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
        $businessProfileId = auth()->user()->businessProfile?->id ?? 1;

        $allowedSorts = ['name', 'price'];
        $sort = in_array($request->input('sort'), $allowedSorts) ? $request->input('sort') : 'name';
        $dir  = $request->input('dir') === 'desc' ? 'desc' : 'asc';

        $query = Product::where('business_profile_id', $businessProfileId)
            ->withCount('reservations')
            ->with('category')
            ->orderBy($sort, $dir);

        $products = $query->get();

        $totalProductos = $products->count();
        $activos        = $products->where('is_active', true)->count();
        $inactivos      = $products->where('is_active', false)->count();

        return view('products.index', compact('products', 'totalProductos', 'activos', 'inactivos', 'sort', 'dir'));
    }

    public function create(Request $request)
    {
        $businessProfileId = auth()->user()->businessProfile?->id;
        $categories = Category::where('business_profile_id', $businessProfileId)->orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        // Normaliza el precio: "5.000,50" → 5000.50 | "5000.50" → 5000.50
        $price = preg_replace('/\.(?=\d{3}(\D|$))/', '', $request->input('price', ''));
        $price = str_replace(',', '.', $price);
        $request->merge(['price' => $price]);

        // 1. Validar datos básicos
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // 2. ID del negocio del vendedor logueado
        $businessProfileId = auth()->user()->businessProfile?->id
            ?? \DB::table('business_profiles')->where('user_id', auth()->id())->value('id')
            ?? 1;

        // 3. Validar que la categoría pertenezca a este negocio
        $categoryId = \App\Models\Category::where('id', $request->category_id)
            ->where('business_profile_id', $businessProfileId)
            ->value('id');

        if (!$categoryId) {
            return back()
                ->withInput()
                ->withErrors(['category_id' => 'La categoría seleccionada no es válida.']);
        }

        // 4. Guardar imagen si viene en el request
        $imagePath = $request->hasFile('image') ? $this->ImagePath($request) : null;

        // 5. Crear el producto con todos los campos incluyendo la imagen
        $product = Product::create([
            'name'                => $request->name,
            'description'         => $request->description,
            'price'               => $request->price,
            'status'              => $request->status ?? 'active',
            'business_profile_id' => $businessProfileId,
            'category_id'         => $categoryId,
            'image'               => $imagePath,
        ]);

        return redirect('/recipes/' . $product->id . '/edit')
            ->with('success', '¡Producto creado con éxito! Ahora asignale sus ingredientes.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $businessProfileId = auth()->user()->businessProfile?->id;
        $categories = Category::where('business_profile_id', $businessProfileId)->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Normaliza el precio: "5.000,50" → 5000.50 | "5000.50" → 5000.50
        $price = preg_replace('/\.(?=\d{3}(\D|$))/', '', $request->input('price', ''));
        $price = str_replace(',', '.', $price);
        $request->merge(['price' => $price]);

        $request->validate($this->DataValidation());

        $data = [
            'name'          => $request->name,
            'description'   => $request->description,
            'category_id'   => $request->category_id,
            'price'         => $request->price,
            'is_active'     => $request->is_active,
            'custom_margin' => $request->filled('custom_margin') ? $request->custom_margin : null,
        ];

        if ($request->boolean('remove_image') && !$request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('r2')->delete($product->image);
            }
            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('r2')->delete($product->image);
            }
            $data['image'] = $this->ImagePath($request);
        }

        $product->update($data);

        $this->recalculateSuggestedPrice($product);

        return redirect()->route('products.index')->with('success', 'Producto actualizado con éxito.');
    }

    /**
     * Recalcula el precio sugerido usando el costo estimado ya guardado
     * y el margen vigente (personalizado del producto o general del negocio).
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
        if ($this->productBelongsToSeller($product)) {
            $product->delete();
        } else {
            abort(403);
        }

        return redirect()->route('products.index')->with('success', 'Producto eliminado con éxito.');
    }

    public function ChangeStatement(Request $request, Product $product)
    {
        if ($this->productBelongsToSeller($product)) {
            $product->is_active = !$product->is_active;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'mensaje' => 'Actualizado correctamente el estado del producto',
            'state'   => $product->is_active,
        ]);
    }

    public function ImagePath(Request $request)
    {
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'r2');
        }

        return $imagePath;
    }

    public function productBelongsToSeller(Product $product)
    {
        return $product->business_profile_id === Auth::user()->businessProfile->id;
    }

    public function DataValidation()
    {
        return [
            'name'         => 'required|string|max:100',
            'description'  => 'nullable|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'price'        => 'required|numeric|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active'    => 'required|boolean',
            'custom_margin'=> 'nullable|numeric|min:1|max:50',
            'remove_image' => 'nullable|boolean',
        ];
    }
}
