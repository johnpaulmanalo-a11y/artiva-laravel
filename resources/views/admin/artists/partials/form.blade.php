@php
    $photoUrl = $artist->photoUrl();
@endphp

<div class="form-grid">
    <div class="form-grid__full">
        <label class="form-label" for="name">Full name</label>
        <input class="form-control" type="text" id="name" name="name" value="{{ old('name', $artist->name) }}" required maxlength="160" placeholder="Artist’s full name">
    </div>

    <div class="form-grid__full">
        <label class="form-label" for="specialization">Specialization</label>
        <input class="form-control" type="text" id="specialization" name="specialization" value="{{ old('specialization', $artist->specialization) }}" maxlength="160" placeholder="e.g. Character Design, Pixel Art">
    </div>

    <div class="form-grid__full">
        <label class="form-label" for="bio">Biography</label>
        <textarea class="form-control" id="bio" name="bio" rows="5" maxlength="5000" placeholder="Introduce the artist, their practice and experience.">{{ old('bio', $artist->bio) }}</textarea>
    </div>

    <div>
        <label class="form-label" for="portfolio_url">Portfolio URL</label>
        <input class="form-control" type="url" id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url', $artist->portfolio_url) }}" maxlength="2048" placeholder="https://">
    </div>

    <div>
        <label class="form-label" for="instagram_url">Instagram URL</label>
        <input class="form-control" type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $artist->instagram_url) }}" maxlength="2048" placeholder="https://instagram.com/">
    </div>

    <div>
        <label class="form-label" for="facebook_url">Facebook URL</label>
        <input class="form-control" type="url" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $artist->facebook_url) }}" maxlength="2048" placeholder="https://facebook.com/">
    </div>

    <div>
        <label class="form-label" for="artstation_url">ArtStation URL</label>
        <input class="form-control" type="url" id="artstation_url" name="artstation_url" value="{{ old('artstation_url', $artist->artstation_url) }}" maxlength="2048" placeholder="https://www.artstation.com/">
    </div>

    <div class="form-grid__full">
        <label class="form-label" for="photo">Profile photo</label>
        <div class="d-flex gap-4 flex-wrap align-items-start">
            <div class="artist-photo-preview">
                @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="Current photo of {{ $artist->name }}">
                @else
                    <span>{{ $artist->name ? mb_strtoupper(mb_substr($artist->name, 0, 1)) : 'A' }}</span>
                @endif
            </div>
            <div style="flex: 1; min-width: 240px;">
                <input class="form-control" type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                <p class="form-text">JPG, PNG or WebP; maximum 4 MB and 5000 × 5000 pixels. New uploads replace the existing photo.</p>
            </div>
        </div>
    </div>
</div>

<div class="form-actions">
    <button class="btn btn-ink" type="submit">{{ $artist->exists ? 'Save changes' : 'Create artist' }}</button>
    <a class="btn btn-ghost" href="{{ route('admin.artists.index') }}">Cancel</a>
</div>
