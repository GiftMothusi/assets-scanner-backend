<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'name' => 'IT',
                'description' => 'Information Technology Department',
                'location' => 'Floor 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HR',
                'description' => 'Human Resources Department',
                'location' => 'Floor 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance',
                'description' => 'Finance Department',
                'location' => 'Floor 4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operations',
                'description' => 'Operations Department',
                'location' => 'Floor 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marketing',
                'description' => 'Marketing Department',
                'location' => 'Floor 5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
