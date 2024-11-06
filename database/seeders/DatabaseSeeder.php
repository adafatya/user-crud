<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('membership_statuses')->insert([
            'name' => "Bronze",
            'description' => "Perunggu",
        ]);

        DB::table('membership_statuses')->insert([
            'name' => "Silver",
            'description' => "Perak",
        ]);

        DB::table('membership_statuses')->insert([
            'name' => "Gold",
            'description' => "Emas",
        ]);
        
        DB::table('users')->insert([
            'name' => "Admin",
            'email' => "admin@email.com",
            'password' => Hash::make('password'),
            'age' => 23,
            'membership_status_id' => 3,
            'role' => 'admin'
        ]);
    }
}
