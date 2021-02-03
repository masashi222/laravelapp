<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(business_masters_seeder::class);
        $this->call(member_records_seeder::class);
        $this->call(time_records_seeder::class);
        $this->call(stamp_records_seeder::class);
        $this->call(create_records_seeder::class);
        $this->call(shift_records_seeder::class);
        $this->call(key_records_seeder::class);
    }
}
