<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@lab.com',
                'role' => 'admin',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Purchasing Staff',
                'email' => 'purchasing@lab.com',
                'role' => 'purchasing',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Incoming Staff',
                'email' => 'incoming@lab.com',
                'role' => 'incoming',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Lab Technician',
                'email' => 'lab@lab.com',
                'role' => 'lab',
                'password' => bcrypt('password'),
            ],
        ];

        foreach ($users as $user) {
            \App\Models\User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
