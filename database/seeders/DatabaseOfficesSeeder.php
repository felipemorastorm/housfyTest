<?php

namespace Database\Seeders;

use App\Models\Offices;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class DatabaseOfficesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for($i=1;$i<=50;$i++) {
            Offices::create(array('name' => $faker->name(), 'address' => $faker->address()));
        }
        $this->command->info('User table seeded!');
    }
}
