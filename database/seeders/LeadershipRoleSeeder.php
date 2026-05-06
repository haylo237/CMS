<?php

namespace Database\Seeders;

use App\Models\LeadershipRole;
use Illuminate\Database\Seeder;

class LeadershipRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'General Overseer', 'rank' => 1,  'description' => 'Senior pastor and head of the church'],
            ['name' => 'First Lady',        'rank' => 2,  'description' => 'Spouse of the General Overseer'],
            ['name' => 'Associate Pastor',  'rank' => 3,  'description' => 'Associate pastoral role'],
            ['name' => 'Elder',             'rank' => 4,  'description' => 'Senior leadership elder'],
            ['name' => 'Deacon',            'rank' => 5,  'description' => 'Male servant leader'],
            ['name' => 'Deaconess',         'rank' => 6,  'description' => 'Female servant leader'],
            ['name' => 'Pastor',            'rank' => 7,  'description' => 'Pastoral care and ministry'],
        ];

        foreach ($roles as $role) {
            LeadershipRole::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
