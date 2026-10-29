<?php

namespace Database\Seeders;

use App\Models\Artwork;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArtworkSeeder extends Seeder
{
    /**
     * Each seeded artwork gets a generated JPG preview on the public disk and
     * a matching master file on the private disk, so browsing, purchasing and
     * downloading all work end-to-end in the demo.
     */
    public function run(): void
    {
        $artworks = [
            ['Jp', 'Pixel Characters', 'John Paul Manalo', 400.00,
                'A portrait study in warm neutrals, built from layered brushwork and light.',
                'images/jep.jpg'],

            ['captain valor', 'Pixel Characters', 'John Paul Manalo', 400.00,
                'A decorated captain rendered in bold shapes and navy shadow.',
                'images/captain valor.jpg'],

            ['archery', 'Pixel Characters', 'John Paul Manalo', 400.00,
                'Focus, breath and release — a concept piece for a forest tournament.',
                'images/archery.jpg'],

            ['dan the boss', 'Pixel Characters', 'John Paul Manalo', 400.00,
                'The boss of the block, illustrated with hard light and brass accents.',
                'images/dan the boss.jpg'],

            ['kai', 'Pixel Characters', 'John Paul Manalo', 400.00,
                'The boss of the block, illustrated with hard light and brass accents.',
                'images/kai.jpg'],

            ['Sarah', 'Pixel Characters', 'Charlene France Sollestre', 400.00,
                'A portrait study featuring expressive features and warm colors.',
                'images/Sarah.png'],

            ['Arman', 'Pixel Characters', 'Charlene France Sollestre', 400.00,
                'A geometric character featuring glasses and a red vest.',
                'images/Arman.jpg'],

            ['Pablo', 'Pixel Characters', 'Charlene France Sollestre', 400.00,
                'A playful geometric character wearing a yellow shirt and blue pants.',
                'images/Pablo.jpg'],

            ['Nicole', 'Pixel Characters', 'Charlene France Sollestre', 400.00,
                'A colorful portrait featuring blonde hair and round glasses.',
                'images/Nicole.png'],

            ['Marites', 'Pixel Characters', 'Charlene France Sollestre', 400.00,
                'A unique character illustration with a distinctive personality.',
                'images/marites.jpg'],
            ['The Reporter', 'Digital Characters', 'Reema Andrea Sobrevega', 400.00,
    'A character illustration of The Reporter.',
    'images/the-reporter.jpg'],
            ['Virgo', '3D Characters', 'Mark John Paul Avendano', 400.00,
    'A character illustration of Virgo.',
    'images/Virgo.jpg'],
        ];

        $featured = [
            'Jp',
            'captain valor',
            'archery',
            'dan the boss',
            'kai',
            'Sarah',
            'Arman',
            'Pablo',
            'Nicole',
            'Marites',
            'The Reporter',
        ];

        foreach ($artworks as $index => [$title, $categoryName, $artist, $price, $description, $image]) {
            $category = Category::where('name', $categoryName)->firstOrFail();
            $slug = Str::slug($title);
            $palette = $this->palette($index);

            $sourcePath = public_path($image);

            if (!file_exists($sourcePath)) {
                throw new \RuntimeException("Artwork image not found: {$sourcePath}");
            }

            $imageContents = file_get_contents($sourcePath);

            if ($imageContents === false) {
                throw new \RuntimeException("Unable to read artwork image: {$sourcePath}");
            }

            // Display the actual image from the public folder.
            $previewPath = $image;

            // Save a private master copy for downloads.
            $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $masterPath = 'artworks/'.$slug.'.'.$extension;

            Storage::disk('local')->put($masterPath, $imageContents);

            Artwork::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'title' => $title,
                    'artist_name' => $artist,
                    'description' => $description.' Delivered as a high-resolution file with a personal-use licence.',
                    'preview_path' => $previewPath,
                    'file_path' => $masterPath,
                    'price' => $price,
                    'file_format' => strtoupper($extension),
                    'resolution' => '3000 x 4000 px',
                    'file_size_kb' => (int) ceil(strlen($imageContents) / 1024),
                    'is_featured' => in_array($title, $featured, true),
                    'is_active' => true,
                ],
            );
        }
    }

    /**
     * @return array{0: string, 1: string, 2: string, 3: string}
     */
    private function palette(int $index): array
    {
        $palettes = [
            ['#10264D', '#4C8ED5', '#D2CFE0', '#F2F3EE'],
            ['#17233B', '#4C8ED5', '#8FB8E0', '#F2F3EE'],
            ['#142653', '#D2CFE0', '#E9C46A', '#F2F3EE'],
            ['#10264D', '#6BA3D6', '#BFD3E6', '#F2F3EE'],
        ];

        return $palettes[$index % count($palettes)];
    }

    /**
     * Generate a deterministic abstract composition as a standalone JPG.
     *
     * @param  array{0: string, 1: string, 2: string, 3: string}  $palette
     */
    private function jpg(string $title, string $artist, array $palette, bool $withLabel): string
    {
        [$ink, $accent, $soft, $ivory] = $palette;
        $seed = crc32($title);
        $shapes = '';

        for ($i = 0; $i < 7; $i++) {
            $cx = 100 + (($seed >> ($i * 3)) % 800);
            $cy = 120 + (($seed >> ($i * 5)) % 1000);
            $r = 90 + (($seed >> ($i * 2)) % 220);
            $opacity = 0.10 + (($i % 4) * 0.06);
            $fill = $i % 2 === 0 ? $accent : $soft;
            $shapes .= sprintf(
                '<circle cx="%d" cy="%d" r="%d" fill="%s" opacity="%.2f"/>',
                $cx, $cy, $r, $fill, $opacity
            );
        }

        $arc = sprintf(
            '<path d="M0 820 C 220 640, 420 980, 660 760 S 980 560, 1000 700 L1000 1200 L0 1200 Z" fill="%s" opacity="0.85"/>',
            $accent
        );

        $label = $withLabel
            ? '<text x="64" y="1050" font-family="Playfair Display, Georgia, serif" font-size="58" fill="'.$ivory.'" opacity="0.95">'
                .e($title).'</text>'
                .'<text x="66" y="1102" font-family="Montserrat, Arial, sans-serif" font-size="24" letter-spacing="4" fill="'.$ivory.'" opacity="0.7">'
                .e(Str::upper($artist)).'</text>'
            : '';

        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1200" width="1000" height="1200" role="img" aria-label="'.e($title).'">'
            .'<defs><linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">'
            .'<stop offset="0%" stop-color="'.$ink.'"/><stop offset="100%" stop-color="'.$soft.'"/>'
            .'</linearGradient></defs>'
            .'<rect width="1000" height="1200" fill="url(#bg)"/>'
            .$shapes.$arc
            .'<rect x="40" y="40" width="920" height="1120" fill="none" stroke="'.$ivory.'" stroke-opacity="0.35" stroke-width="2"/>'
            .$label
            .'</svg>';
    }
}
