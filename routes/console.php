<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('artiva:summary', function () {
    $this->info('Artiva artworks: '.\App\Models\Artwork::count());
    $this->info('Artiva orders: '.\App\Models\Order::count());
    $this->info('Artiva customers: '.\App\Models\User::where('role', 'customer')->count());
})->purpose('Print a short Artiva data summary');
