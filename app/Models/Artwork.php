<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Artwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'artist_name',
        'description',
        'preview_path',
        'file_path',
        'price',
        'file_format',
        'resolution',
        'file_size_kb',
        'is_featured',
        'is_active',
        'downloads_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'downloads_count' => 'integer',
            'file_size_kb' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Artwork $artwork) {
            if (blank($artwork->slug)) {
                $artwork->slug = static::uniqueSlug($artwork->title, $artwork->id);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $like = '%'.trim($term).'%';

        return $query->where(function (Builder $query) use ($like) {
            $query->where('title', 'like', $like)
                ->orWhere('artist_name', 'like', $like)
                ->orWhere('description', 'like', $like);
        });
    }

    public function previewUrl(): ?string
    {
        return artiva_preview($this);
    }

    public function hasDownloadableFile(): bool
    {
        return filled($this->file_path)
            && Storage::disk('local')->exists($this->file_path);
    }

    public function fileSizeForHumans(): string
    {
        if (! $this->file_size_kb) {
            return '—';
        }

        if ($this->file_size_kb >= 1024) {
            return number_format($this->file_size_kb / 1024, 1).' MB';
        }

        return $this->file_size_kb.' KB';
    }

    public function downloadFileName(): string
    {
        $extension = Str::lower($this->file_format ?: 'png');

        return Str::slug($this->title).'-artiva.'.$extension;
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'artwork';
        $slug = $base;
        $counter = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
