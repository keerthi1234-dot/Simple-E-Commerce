<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'name' => 'Laptop',
            'price' => 55000,
            'stock' => 10
        ]);

        Product::create([
            'name' => 'Smartphone',
            'price' => 25000,
            'stock' => 20
        ]);

        Product::create([
            'name' => 'Headphones',
            'price' => 1500,
            'stock' => 50
        ]);
    }
}
