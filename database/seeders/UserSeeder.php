<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@artiva.test'],
            [
                'name' => 'Artiva Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'phone' => '+1 555 0100',
            ],
        );

        $customers = [
            ['name' => 'Jp Reyes', 'email' => 'customer@artiva.test', 'phone' => '+1 555 0111'],
            ['name' => 'Mara Santos', 'email' => 'mara@artiva.test', 'phone' => '+1 555 0112'],
            ['name' => 'Liam Cruz', 'email' => 'liam@artiva.test', 'phone' => '+1 555 0113'],
            ['name' => 'Aya Nakamura', 'email' => 'aya@artiva.test', 'phone' => null],
        ];

        foreach ($customers as $customer) {
            User::updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_CUSTOMER,
                    'phone' => $customer['phone'],
                ],
            );
        }
    }
}
