<div>
    <label class="mb-1 block text-sm font-medium">Nomi</label>
    <select name="name" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <option value="">Paket turini tanlang</option>
        @foreach($packageNameOptions as $packageNameOption)
            <option value="{{ $packageNameOption }}" @selected(old('name', $weddingPackage?->name) === $packageNameOption)>{{ $packageNameOption }}</option>
        @endforeach
    </select>
    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Bir kishilik narx</label>
    <input name="price_per_person" type="number" step="0.01" min="0" value="{{ old('price_per_person', $weddingPackage?->price_per_person) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('price_per_person')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Holat</label>
    <select name="status" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <option value="Faol" @selected(old('status', $weddingPackage?->status ?? 'Faol') === 'Faol')>Faol</option>
        <option value="Nofaol" @selected(old('status', $weddingPackage?->status) === 'Nofaol')>Nofaol</option>
    </select>
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Rasm</label>
    <input name="image" type="file" accept="image/*" class="w-full rounded-xl border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800">
    @if($weddingPackage?->image_url)
        <div class="mt-3 rounded-2xl border border-slate-200 p-3 dark:border-slate-700">
            <img src="{{ $weddingPackage->image_url }}" alt="Paket rasmi" class="h-32 w-full rounded-xl object-cover md:h-36">
            <label class="mt-3 flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                Mavjud rasmni o'chirish
            </label>
        </div>
    @endif
    <p class="mt-1 text-xs text-slate-500">Yangi rasm yuklasangiz eski rasm avtomatik almashtiriladi.</p>
    @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div class="md:col-span-2">
    <label class="mb-1 block text-sm font-medium">Paket rasmlari</label>
    <input name="gallery_images[]" type="file" accept="image/*" multiple class="w-full rounded-xl border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800">
    <p class="mt-1 text-xs text-slate-500">Bir nechta rasm tanlang. Ular paket kartasi pastida bir qatorda chiqadi.</p>
    @error('gallery_images')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    @error('gallery_images.*')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror

    @if($weddingPackage?->images?->isNotEmpty())
        <div class="mobile-wrap-strip mt-4 flex gap-3 overflow-x-auto pb-2">
            @foreach($weddingPackage->images as $galleryImage)
                <label class="block min-w-28 shrink-0 rounded-2xl border border-slate-200 p-2 dark:border-slate-700">
                    <img src="{{ $galleryImage->url() }}" alt="Paket rasmi" class="h-24 w-full rounded-xl object-cover">
                    <span class="mt-2 flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="remove_gallery_images[]" value="{{ $galleryImage->id }}" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                        O'chirish
                    </span>
                </label>
            @endforeach
        </div>
    @endif
</div>
<div class="md:col-span-2">
    <label class="mb-1 block text-sm font-medium">Tavsif</label>
    <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('description', $weddingPackage?->description) }}</textarea>
    @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

