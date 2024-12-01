<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Perbaiki huruf "a" menjadi "A"
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'), 
        ]);
        $admin->assignRole('admin');

    }
}
