<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed 10 data product menggunakan factory.
     */
    public function run(): void
    {
        Product::factory(10)->create();
    }
}
