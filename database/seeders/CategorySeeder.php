<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pixel Characters',
                'description' => 'Characters created using pixel art techniques.',
            ],
            [
                'name' => 'Abstract Characters',
                'description' => 'Creative characters made from shapes, colors, and abstract forms.',
            ],
            [
                'name' => '3D Characters',
                'description' => 'Three-dimensional character models and digital sculptures.',
            ],
            [
                'name' => 'Digital Characters',
                'description' => 'Original characters created using digital illustration tools.',
            ],
        ];

        $allowedSlugs = collect($categories)
            ->map(fn ($category) => Str::slug($category['name']))
            ->all();

        // Deactivate old categories instead of deleting them.
        Category::whereNotIn('slug', $allowedSlugs)
            ->update(['is_active' => false]);

        // Create or update the four desired categories.
        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
