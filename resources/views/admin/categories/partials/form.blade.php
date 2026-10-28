@php
    $isEdit = $category->exists;
@endphp

<div class="form-grid">
    <div class="form-grid__full">
        <label class="form-label" for="name">Category name</label>
        <input class="form-control" type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="80" placeholder="e.g. Character Art">
        <p class="form-text">The URL slug is generated automatically from the name.</p>
    </div>

    <div class="form-grid__full">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control" id="description" name="description" rows="4" maxlength="1000" placeholder="A short introduction shown at the top of the category page.">{{ old('description', $category->description) }}</textarea>
    </div>

    <div class="form-grid__full">
        <label class="form-label" for="image">Category image <span class="text-muted-soft" style="text-transform: none; letter-spacing: 0;">(optional)</span></label>

        <div class="d-flex gap-4 flex-wrap align-items-start">
            <div class="upload-drop__preview" style="max-width: 160px;" data-category-preview>
                @if ($category->image)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($category->image) }}" alt="{{ $category->name }}">
                @else
                    <span>{{ $category->name ? mb_substr($category->name, 0, 1) : 'A' }}</span>
                @endif
            </div>

            <div style="flex: 1; min-width: 240px;">
                <div class="upload-drop">
                    <input class="form-control" type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp" data-upload-input="[data-category-preview]">
                    <p class="form-text mb-0">JPG, PNG or WebP up to 2 MB.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="form-grid__full">
        <div class="switch-row">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))>
            <label for="is_active">
                Visible in the storefront
                <small>Hidden categories are removed from navigation and return a 404 page.</small>
            </label>
        </div>
    </div>
</div>

<div class="form-actions">
    <button class="btn btn-ink" type="submit">{{ $isEdit ? 'Save changes' : 'Create category' }}</button>
    <a class="btn btn-ghost" href="{{ route('admin.categories.index') }}">Cancel</a>
</div>
