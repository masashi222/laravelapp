<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class key_records_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'date' => '2020-12-19 00:00:00',
            'code' => '1234',
        ];
        DB::table('key_records')->insert($param);
    }
}
