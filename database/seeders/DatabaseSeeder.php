<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Executes the database seeders for the UserManagement, Organization, and RealEstate modules.
     *
     * Populates the application's database by invoking the primary seeders for each module in sequence.
     */
    public function run(): void
    {
        $this->call(\Modules\UserManagement\Database\Seeders\DatabaseSeeder::class);
        $this->call(\Modules\Organization\Database\Seeders\DatabaseSeeder::class);
        $this->call(\Modules\RealEstate\Database\Seeders\DatabaseSeeder::class);
    }
}
