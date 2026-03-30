<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('products.index', [
            'products' => Product::latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validateProduct($request));

        return redirect()->route('products.index')->with('success', 'Mahsulot yaratildi.');
    }

    public function show(Product $product): RedirectResponse
    {
        return redirect()->route('products.edit', $product);
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validateProduct($request, $product));

        return redirect()->route('products.index')->with('success', 'Mahsulot yangilandi.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->purchaseItems()->exists()) {
            return back()->withErrors([
                'delete' => 'Bog\'langan kirimlar bor mahsulotni o\'chirib bo\'lmaydi.',
            ]);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Mahsulot o\'chirildi.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $skuRule = ['nullable', 'string', 'max:100'];

        if ($product) {
            $skuRule[] = 'unique:products,sku,'.$product->id;
        } else {
            $skuRule[] = 'unique:products,sku';
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:30'],
            'sku' => $skuRule,
            'minimum_stock' => ['nullable', 'numeric', 'min:0'],
            'current_stock' => ['nullable', 'numeric', 'min:0'],
            'last_purchase_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
