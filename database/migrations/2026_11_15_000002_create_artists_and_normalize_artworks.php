<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('photo_path')->nullable();
            $table->text('bio')->nullable();
            $table->string('specialization', 160)->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('artstation_url')->nullable();
            $table->timestamps();
        });

        Schema::table('artworks', function (Blueprint $table) {
            $table->foreignId('artist_id')->nullable()->constrained()->restrictOnDelete();
        });

        $artistIds = [];

        foreach (DB::table('artworks')->select('id', 'artist_name')->orderBy('id')->get() as $artwork) {
            $name = trim((string) $artwork->artist_name) ?: 'Unknown Artist';
            $key = mb_strtolower($name);

            if (! isset($artistIds[$key])) {
                $baseSlug = Str::slug($name) ?: 'artist';
                $slug = $baseSlug;
                $suffix = 2;

                while (DB::table('artists')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$suffix++;
                }

                $artistIds[$key] = DB::table('artists')->insertGetId([
                    'name' => $name,
                    'slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('artworks')
                ->where('id', $artwork->id)
                ->update(['artist_id' => $artistIds[$key]]);
        }

        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn('artist_name');
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->string('artist_name')->nullable();
        });

        foreach (DB::table('artworks')
            ->leftJoin('artists', 'artists.id', '=', 'artworks.artist_id')
            ->select('artworks.id', 'artists.name')
            ->orderBy('artworks.id')
            ->get() as $artwork) {
            DB::table('artworks')
                ->where('id', $artwork->id)
                ->update(['artist_name' => $artwork->name ?: 'Unknown Artist']);
        }

        Schema::table('artworks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('artist_id');
        });

        Schema::dropIfExists('artists');
    }
};
