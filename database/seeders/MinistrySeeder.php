<?php

namespace Database\Seeders;

use App\Models\Ministry;
use Illuminate\Database\Seeder;

class MinistrySeeder extends Seeder
{
    public function run(): void
    {
        $ministries = [
            ['name' => 'Men\'s Ministry',      'description' => 'Ministry for male members of the church'],
            ['name' => 'Women\'s Ministry',    'description' => 'Ministry for female members of the church'],
            ['name' => 'Youth Ministry',       'description' => 'Ministry for young adults and teens'],
            ['name' => 'Children\'s Ministry', 'description' => 'Ministry for children and juniors'],
        ];

        foreach ($ministries as $ministry) {
            Ministry::firstOrCreate(['name' => $ministry['name']], $ministry);
        }
    }
}
