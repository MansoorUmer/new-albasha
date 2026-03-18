<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate([
            'email' => 'admin@gmail.com'
        ], [
            'first_name' => 'Admin',
            'last_name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        User::updateOrCreate([
            'email' => 'cashier@gmail.com'
        ], [
            'first_name' => 'Cashier',
            'last_name' => 'User',
            'email' => 'cashier@gmail.com',
            'password' => bcrypt('cashier123'),
            'role' => 'cashier',
        ]);

        User::updateOrCreate([
            'email' => 'kitchen@gmail.com'
        ], [
            'first_name' => 'Kitchen',
            'last_name' => 'User',
            'email' => 'kitchen@gmail.com',
            'password' => bcrypt('kitchen123'),
            'role' => 'kitchen',
        ]);
    }
}
