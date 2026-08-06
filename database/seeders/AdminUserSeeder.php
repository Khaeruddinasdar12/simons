<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@fshi.local',
                'role' => UserRole::Superadmin,
            ],
            [
                'name' => 'Akademik',
                'email' => 'akademik@fshi.local',
                'role' => UserRole::Akademik,
            ],
            [
                'name' => 'Kabag',
                'email' => 'kabag@fshi.local',
                'role' => UserRole::Kabag,
            ],
            [
                'name' => 'Wakil Dekan 1',
                'email' => 'wadek1@fshi.local',
                'role' => UserRole::Wadek1,
            ],
            [
                'name' => 'Dekan',
                'email' => 'dekan@fshi.local',
                'role' => UserRole::Dekan,
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'role' => $admin['role'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
