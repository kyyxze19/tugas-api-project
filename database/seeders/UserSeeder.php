<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed user contoh:
     * - Andre (user biasa) -> role: product-list
     * - Admin -> role: product-store
     */
    public function run(): void
    {
        // User biasa: Andre
        $andre = User::create([
            'name' => 'Andre',
            'email' => 'andre@mail.com',
            'password' => Hash::make('password123'),
        ]);

        // User admin: Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password123'),
        ]);

        // Assign role product-list ke Andre
        $roleProductList = Role::where('role_name', 'product-list')->first();
        $andre->roles()->attach($roleProductList);

        // Assign role product-store ke Admin
        $roleProductStore = Role::where('role_name', 'product-store')->first();
        $admin->roles()->attach($roleProductStore);
    }
}
