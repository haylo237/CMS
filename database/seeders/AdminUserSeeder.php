<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $member = Member::firstOrCreate(
            ['email' => 'admin@church.org'],
            [
                'first_name' => 'Church',
                'last_name'  => 'Administrator',
                'gender'     => 'male',
                'status'     => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@church.org'],
            [
                'member_id' => $member->id,
                'password'  => Hash::make('Admin@1234'),
                'role'      => 'super_admin',
            ]
        );
    }
}
