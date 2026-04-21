<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('user_addresses')->insert([
            [
                'type' => 'home',
                'address1' => '123 Main Street',
                'address2' => 'Apt 4B',
                'city' => 'San Jose del Monte',
                'state' => 'Bulacan',
                'zipcode' => '3023',
                'isMain' => 1,
                'country_code' => 'PHL',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'office',
                'address1' => '456 Business Ave',
                'address2' => null,
                'city' => 'Quezon City',
                'state' => 'Metro Manila',
                'zipcode' => '1100',
                'isMain' => 0,
                'country_code' => 'PHL',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
