<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $stockState = (string) $request->query('stock_state', '');

        return view('master-products.index', [
            'products' => Product::query()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%")
                            ->orWhere('subcategory', 'like', "%{$search}%");
                    });
                })
                ->when($stockState === 'low', fn ($query) => $query->whereColumn('current_stock', '<=', 'minimum_stock'))
                ->when($stockState === 'active', fn ($query) => $query->where('is_active', true))
                ->when($stockState === 'inactive', fn ($query) => $query->where('is_active', false))
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            'filters' => compact('search', 'stockState'),
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
        $tenantId = TenantContext::id();

        if ($product) {
            $skuRule[] = Rule::unique('products', 'sku')
                ->ignore($product->id)
                ->where('venue_connection_id', $tenantId);
        } elseif (! $allowExistingSku) {
            $skuRule[] = Rule::unique('products', 'sku')->where('venue_connection_id', $tenantId);
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
