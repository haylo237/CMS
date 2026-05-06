<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Media',     'type' => 'operations', 'description' => 'Handles audio, video, and broadcast operations'],
            ['name' => 'Music',     'type' => 'operations', 'description' => 'Worship team and choir ministry'],
            ['name' => 'Ushering',  'type' => 'operations', 'description' => 'Guest reception and seating coordination'],
            ['name' => 'Protocol',  'type' => 'operations', 'description' => 'Official ceremonies and VIP management'],
            ['name' => 'Technical', 'type' => 'operations', 'description' => 'Technical infrastructure and IT support'],
            ['name' => 'Welfare',   'type' => 'operations', 'description' => 'Member welfare and support services'],
            ['name' => 'Evangelism','type' => 'operations', 'description' => 'Outreach and evangelism programs'],
            ['name' => 'Prayer',    'type' => 'operations', 'description' => 'Intercession and prayer coordination'],
            ['name' => 'Sanctuary', 'type' => 'operations', 'description' => 'Sanctuary and venue management'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept['name']], $dept);
        }
    }
}
