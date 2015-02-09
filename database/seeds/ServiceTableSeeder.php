<?php

use Illuminate\Database\Seeder;
use App\Models\Service;
use \DB;

class ServiceTableSeeder extends Seeder {

    public function run()
    {
        DB::table('services')->delete();
        Service::create(
        	array(
        		'name' => 'Facebook',
                'price' => '499',
                'description' => 'Advertise your Facebook link'
                ));
        Service::create(
            array(
                'name' => 'YouTube',
                'price' => '499',
                'description' => 'Advertise your YouTube link'
                ));

        Service::create(
            array(
                'name' => 'Soundcloud',
                'price' => '499',
                'description' => 'Advertise your Soundcloud link'
                ));

        Service::create(
            array(
                'name' => 'Instagram',
                'price' => '499',
                'description' => 'Advertise your Instagram link'
                ));
        Service::create(
            array(
                'name' => 'Twitter',
                'price' => '499',
                'description' => 'Advertise your Twitter link'
                ));
        Service::create(
            array(
                'name' => 'Custom Website',
                'price' => '499',
                'description' => 'Advertise your custom website link'
                ));
        Service::create(
            array(
                'name' => 'Android App',
                'price' => '499',
                'description' => 'Advertise your android app link'
                ));
        Service::create(
            array(
                'name' => 'Apple App',
                'price' => '499',
                'description' => 'Advertise your apple app link'
                ));
    }

}