<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed role default: product-list dan product-store.
     */
    public function run(): void
    {
        Role::create(['role_name' => 'product-list']);
        Role::create(['role_name' => 'product-store']);
    }
}
