<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia; // <-- Importamos Inertia

class ProductController extends Controller
{
    /**
     * Muestra la lista de productos.
     */
    public function index(Request $request)
    {
        $products = Product::paginate(10); // Ajustamos la paginación

        return Inertia::render('Product/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     */
    public function create()
    {
        return Inertia::render('Product/Create');
    }

    /**
     * Almacena un nuevo producto.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return Redirect::route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Muestra un producto específico.
     */
    public function show(Product $product)
    {
        return Inertia::render('Product/Show', [
            'product' => $product,
        ]);
    }

    /**
     * Muestra el formulario para editar un producto.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Product/Edit', [
            'product' => $product,
        ]);
    }

    /**
     * Actualiza un producto.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return Redirect::route('products.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Elimina un producto.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return Redirect::route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
