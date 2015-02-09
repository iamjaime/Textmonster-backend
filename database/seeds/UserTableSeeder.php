<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use \DB;

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        User::create(
        	array(
        		'name' => 'Jaime Bernal',
        		'email' => 'jaime@iamjaime.com',
        		'password' => bcrypt('testing123')
        		)
    	);
    }

}