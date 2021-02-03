<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class shift_records_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'shift_createid' => '1',
            'memberid' => '101',
            'go_time' => '2020-12-21 18:00:00',
            'out_time' => '2020-12-21 22:30:00',
            'created_at' => '2020-12-06 23:59:59',
            'updated_at' => '2020-12-06 23:59:59',
        ];
        DB::table('shift_records')->insert($param);

        $param = [
            'shift_createid' => '2',
            'memberid' => '101',
            'go_time' => '2020-12-16 18:00:00',
            'out_time' => '2020-12-16 22:30:00',
            'created_at' => '2020-12-06 23:59:59',
            'updated_at' => '2020-12-06 23:59:59',
        ];
        DB::table('shift_records')->insert($param);

        $param = [
            'shift_createid' => '3',
            'memberid' => '101',
            'go_time' => '2020-12-17 18:00:00',
            'out_time' => '2020-12-17 22:30:00',
            'created_at' => '2020-12-06 23:59:59',
            'updated_at' => '2020-12-06 23:59:59',
        ];
        DB::table('shift_records')->insert($param);

        $param = [
            'shift_createid' => '4',
            'memberid' => '101',
            'go_time' => '2020-12-23 18:00:00',
            'out_time' => '2020-12-23 22:30:00',
            'created_at' => '2020-12-06 23:59:59',
            'updated_at' => '2020-12-06 23:59:59',
        ];
        DB::table('shift_records')->insert($param);

        $param = [
            'shift_createid' => '5',
            'memberid' => '101',
            'go_time' => '2020-12-24 18:00:00',
            'out_time' => '2020-12-24 22:30:00',
            'created_at' => '2020-12-06 23:59:59',
            'updated_at' => '2020-12-06 23:59:59',
        ];
        DB::table('shift_records')->insert($param);

        $param = [
            'shift_createid' => '6',
            'memberid' => '101',
            'go_time' => '2020-12-25 18:00:00',
            'out_time' => '2020-12-25 22:30:00',
            'created_at' => '2020-12-06 23:59:59',
            'updated_at' => '2020-12-06 23:59:59',
        ];
        DB::table('shift_records')->insert($param);

        $param = [
            'shift_createid' => '7',
            'memberid' => '102',
            'go_time' => '2020-12-16 18:00:00',
            'out_time' => '2020-12-17 05:00:00',
            'created_at' => '2020-12-06 23:59:59',
            'updated_at' => '2020-12-06 23:59:59',
        ];
        DB::table('shift_records')->insert($param);

        $param = [
            'shift_createid' => '8',
            'memberid' => '102',
            'go_time' => '2020-12-18 17:00:00',
            'out_time' => '2020-12-19 05:00:00',
            'created_at' => '2020-12-06 23:59:59',
            'updated_at' => '2020-12-06 23:59:59',
        ];
        DB::table('shift_records')->insert($param);

        $param = [
            'shift_createid' => '9',
            'memberid' => '102',
            'go_time' => '2020-12-23 19:00:00',
            'out_time' => '2020-12-14 05:00:00',
            'created_at' => '2020-12-06 23:59:59',
            'updated_at' => '2020-12-06 23:59:59',
        ];
        DB::table('shift_records')->insert($param);

        $param = [
            'shift_createid' => '10',
            'memberid' => '102',
            'go_time' => '2020-12-27 18:00:00',
            'out_time' => '2020-12-28 05:00:00',
            'created_at' => '2020-12-06 23:59:59',
            'updated_at' => '2020-12-06 23:59:59',
        ];
        DB::table('shift_records')->insert($param);

        $param = [
            'shift_createid' => '11',
            'memberid' => '102',
            'go_time' => '2020-12-28 18:30:00',
            'out_time' => '2020-12-29 05:00:00',
            'created_at' => '2020-12-06 23:59:59',
            'updated_at' => '2020-12-06 23:59:59',
        ];
        DB::table('shift_records')->insert($param);
    }
}
