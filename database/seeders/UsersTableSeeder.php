<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'email' => 'tester+admin@gmail.com',
            'firstname' => "TestAdmin",
            'lastname' => "Admin",
            'role' => 'admin',
            'email_verified_at' => '2021-11-03',
            'application_name' => 'admin',
            'password' => bcrypt('admin')
        ]);
    }
}
