<?php

use Illuminate\Database\Seeder;
use App\User;

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
            'name' => 'Mohit Panjwani',
            'email' => 'mohit.panjvani@gmail.com',
            'password' => bcrypt('mohit@123')
        ]);
    }
}
