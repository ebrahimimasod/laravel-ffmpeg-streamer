<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        User::query()->truncate();

        User::factory()->create([
            'name' => 'Mehdi Moradi',
            'email' => 'Mehdi.1372@hotmail.com',
            'password'=>bcrypt('123456')
        ]);
    }
}
