<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@onflytest.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@onflytest.com',
                'password' => Hash::make('admin123'),
                'is_admin' => true, // Será 1 no banco
            ]
        );
    }
}
