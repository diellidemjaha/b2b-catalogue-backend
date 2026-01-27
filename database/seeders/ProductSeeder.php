<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::truncate();

        Product::create([
            'id' => Str::uuid(),
            'name' => 'Premium Olive Oil 1L',
            'sku' => 'OLIVE-001',
            'price' => 8.50,
            'description' => 'Cold pressed premium olive oil.',
            'image_url' => 'https://via.placeholder.com/300',
            'is_active' => true,
        ]);

        Product::create([
            'id' => Str::uuid(),
            'name' => 'Italian Pasta 500g',
            'sku' => 'PASTA-500',
            'price' => 2.30,
            'description' => 'Authentic Italian durum wheat pasta.',
            'image_url' => 'https://via.placeholder.com/300',
            'is_active' => true,
        ]);
    }
}