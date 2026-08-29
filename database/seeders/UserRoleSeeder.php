<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@pengadilan.go.id'],
            [
                'name' => 'Panitera Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'Admin'
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'hakim@pengadilan.go.id'],
            [
                'name' => 'Hakim Ketua',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'Hakim'
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'masyarakat@gmail.com'],
            [
                'name' => 'Budi Pihak Perkara',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'User'
            ]
        );
    }
}
