<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // aquí os datos do primeiro usuario
        $user = [

            [

                'name'=>'admin',

                'email'=>'admin@gmail.com',

                'password'=> bcrypt('123456'),

            ],

            [

                'name'=>'user',

                'email'=>'user@gmail.com',

                'password'=> bcrypt('123456'),

            ],

        ];



        foreach ($user as $key => $value) {

            User::create($value);

        }

    }
}
