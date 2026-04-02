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
        return view('master-products.index', [
            'products' => Product::latest()->take(5)->get(),
        ]);
    }

    public function create(): View
    {
        return view('master-products.create', $this->formData(new Product()));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request, allowExistingSku: true);
        $existingProduct = Product::where('sku', $validated['sku'])->first();

        if ($existingProduct) {
            $existingProduct->update($this->prepareProductPayload($validated, $existingProduct));

            return redirect()->route('products.index')->with('success', 'Mahsulot yangilandi va olib kelingan miqdor qoldiqqa qo\'shildi.');
        }

        Product::create($this->prepareProductPayload($validated));

        return redirect()->route('products.index')->with('success', 'Mahsulot yaratildi.');
    }

    public function show(Product $product): RedirectResponse
    {
        return redirect()->route('products.edit', $product);
    }

    public function edit(Product $product): View
    {
        return view('master-products.edit', $this->formData($product));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product);
        $product->update($this->prepareProductPayload($validated, $product, true));

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

    private function validateProduct(Request $request, ?Product $product = null, bool $allowExistingSku = false): array
    {
        $skuRule = ['required', 'string', 'max:100'];

        if ($product) {
            $skuRule[] = 'unique:products,sku,'.$product->id;
        } elseif (! $allowExistingSku) {
            $skuRule[] = 'unique:products,sku';
        }

        return $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'subcategory' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'in:'.implode(',', Product::UNIT_OPTIONS)],
            'sku' => $skuRule,
            'received_quantity' => ['nullable', 'numeric', 'min:0'],
            'minimum_stock' => ['nullable', 'numeric', 'min:0'],
            'current_stock' => ['nullable', 'numeric', 'min:0'],
            'last_purchase_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    private function prepareProductPayload(array $validated, ?Product $existingProduct = null, bool $isUpdate = false): array
    {
        $receivedQuantity = (float) ($validated['received_quantity'] ?? 0);
        $baseStock = $existingProduct ? (float) $existingProduct->current_stock : (float) ($validated['current_stock'] ?? 0);

        if ($isUpdate) {
            $baseStock = (float) ($validated['current_stock'] ?? $baseStock);
        }

        $validated['current_stock'] = $baseStock + $receivedQuantity;
        $validated['received_quantity'] = $receivedQuantity;

        return $validated;
    }

    private function formData(Product $product): array
    {
        return [
            'product' => $product,
            'catalogProducts' => Product::orderBy('sku')->get(['sku', 'name', 'category', 'subcategory', 'unit']),
            'categories' => Product::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'subcategories' => Product::query()->whereNotNull('subcategory')->distinct()->orderBy('subcategory')->pluck('subcategory'),
            'unitOptions' => Product::UNIT_OPTIONS,
        ];
    }
}
