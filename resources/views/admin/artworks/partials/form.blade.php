@php
    $isEdit = $artwork->exists;
    $preview = $artwork->previewUrl();
@endphp

<div class="form-grid">
    <div class="form-grid__full">
        <label class="form-label" for="title">Artwork title</label>
        <input class="form-control" type="text" id="title" name="title" value="{{ old('title', $artwork->title) }}" required maxlength="150" placeholder="e.g. Captain Valor">
    </div>

    <div>
        <label class="form-label" for="category_id">Category</label>
        <select class="form-select" id="category_id" name="category_id" required>
            <option value="">Choose a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $artwork->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="artist_name">Artist name</label>
        <input class="form-control" type="text" id="artist_name" name="artist_name" value="{{ old('artist_name', $artwork->artist_name) }}" required maxlength="120" placeholder="Who made this piece?">
    </div>

    <div class="form-grid__full">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control" id="description" name="description" rows="5" required minlength="20" maxlength="4000" placeholder="Describe the piece, its mood and what a buyer receives.">{{ old('description', $artwork->description) }}</textarea>
        <p class="form-text">Shown on the artwork details page. At least 20 characters.</p>
    </div>

    <div>
        <label class="form-label" for="price">Price</label>
        <div class="input-group">
            <span class="input-group-text">{{ config('artiva.currency_symbol', '$') }}</span>
            <input class="form-control" type="number" id="price" name="price" value="{{ old('price', $artwork->price) }}" required min="0" step="0.01" placeholder="0.00">
        </div>
    </div>

    <div>
        <label class="form-label" for="file_format">File format</label>
        <select class="form-select" id="file_format" name="file_format" required>
            @foreach (['PNG', 'JPG', 'SVG', 'PSD', 'PDF', 'ZIP'] as $format)
                <option value="{{ $format }}" @selected(old('file_format', $artwork->file_format) === $format)>{{ $format }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label" for="resolution">Resolution</label>
        <input class="form-control" type="text" id="resolution" name="resolution" value="{{ old('resolution', $artwork->resolution) }}" maxlength="40" placeholder="e.g. 3000 x 4000 px">
    </div>

    <div>
        <label class="form-label" for="file">Master file <span class="text-muted-soft" style="text-transform: none; letter-spacing: 0;">(private)</span></label>
        <input class="form-control" type="file" id="file" name="file" accept=".png,.jpg,.jpeg,.zip,.psd,.pdf,.svg,.tiff">
        <p class="form-text">
            @if ($isEdit && $artwork->file_path)
                Current file: <strong>{{ basename($artwork->file_path) }}</strong> ({{ $artwork->fileSizeForHumans() }}). Upload a new file to replace it.
            @else
                Stored on the private disk — never publicly accessible. Max 20 MB.
            @endif
        </p>
    </div>

    <div class="form-grid__full">
        <label class="form-label" for="preview">Preview image <span class="text-muted-soft" style="text-transform: none; letter-spacing: 0;">(public)</span></label>

        <div class="d-flex gap-4 flex-wrap align-items-start">
            <div class="upload-drop__preview" data-preview-target>
                @if ($preview)
                    <img src="{{ $preview }}" alt="Current preview for {{ $artwork->title }}">
                @else
                    <span>{{ $artwork->title ? mb_substr($artwork->title, 0, 1) : 'A' }}</span>
                @endif
            </div>

            <div style="flex: 1; min-width: 240px;">
                <div class="upload-drop">
                    <input class="form-control" type="file" id="preview" name="preview" accept="image/png,image/jpeg,image/webp" data-upload-input="[data-preview-target]">
                    <p class="form-text mb-0">JPG, PNG or WebP up to 4 MB. Shown across the storefront. If no preview is uploaded a styled placeholder is used automatically.</p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="switch-row">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $artwork->is_active ?? true))>
            <label for="is_active">
                Visible in the storefront
                <small>Inactive artwork stays in the admin but cannot be bought.</small>
            </label>
        </div>
    </div>

    <div>
        <div class="switch-row">
            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $artwork->is_featured))>
            <label for="is_featured">
                Feature on the home page
                <small>Featured pieces appear in the “Featured this season” section.</small>
            </label>
        </div>
    </div>
</div>

<div class="form-actions">
    <button class="btn btn-ink" type="submit">{{ $isEdit ? 'Save changes' : 'Add artwork' }}</button>
    <a class="btn btn-ghost" href="{{ route('admin.artworks.index') }}">Cancel</a>

    @if ($isEdit)
        <span class="form-actions__spacer"></span>
        <a class="btn btn-outline-ink btn-sm" href="{{ route('artworks.show', $artwork) }}" target="_blank" rel="noopener">
            <i class="bi bi-box-arrow-up-right"></i> View on store
        </a>
    @endif
</div>
