<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(\Modules\UserManagement\Database\Seeders\DatabaseSeeder::class);
        $this->call(\Modules\Organization\Database\Seeders\DatabaseSeeder::class);
        $this->call(\Modules\RealEstate\Database\Seeders\DatabaseSeeder::class);
    }
}
