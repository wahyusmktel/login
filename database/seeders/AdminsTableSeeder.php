<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'username' => 'admin',
            'password' => Hash::make('password'),
            'email' => 'admin@example.com',
            'nama' => 'Super Admin',
            'photo' => null,
            'no_hp' => '08123456789',
            'ig' => null,
            'fb' => null,
            'yt' => null,
            'status' => true,
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);
    }
}
