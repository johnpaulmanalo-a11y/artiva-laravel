<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('artist_name');
            $table->text('description');
            $table->string('preview_path')->nullable();
            $table->string('file_path')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('file_format', 20)->default('PNG');
            $table->string('resolution', 40)->nullable();
            $table->unsignedInteger('file_size_kb')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('downloads_count')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};
