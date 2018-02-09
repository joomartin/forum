<?php

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
        factory(\App\User::class)->create([
            'name'      => 'Joó Martin',
            'email'     => 'm4rt1n.j00@gmail.com',
            'password'  => '$2y$10$O7PlCXCl8phTECT7xjanNuAMcIZ.3HRN8p2GqILk8SQy.IyLFwfBq'
        ]);
    }
}
