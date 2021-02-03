<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class create_records_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //12月分
        $param = [
            'shift_createid' => '1',
            'memberid' => '101',
            'go_time' => '2020-12-21 18:00:00',
            'out_time' => '2020-12-21 22:30:00',
            'is_register' => '1',
            'is_post' => '1',
            'created_at' => '2020-11-29 18:49:21',
            'updated_at' => '2020-12-06 23:55:03',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '2',
            'memberid' => '101',
            'go_time' => '2020-12-16 18:00:00',
            'out_time' => '2020-12-16 22:30:00',
            'is_register' => '1',
            'is_post' => '1',
            'created_at' => '2020-11-29 18:49:21',
            'updated_at' => '2020-12-06 23:55:03',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '3',
            'memberid' => '101',
            'go_time' => '2020-12-17 18:00:00',
            'out_time' => '2020-12-17 22:30:00',
            'is_register' => '1',
            'is_post' => '1',
            'created_at' => '2020-11-29 18:49:21',
            'updated_at' => '2020-12-06 23:55:03',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '4',
            'memberid' => '101',
            'go_time' => '2020-12-23 18:00:00',
            'out_time' => '2020-12-23 22:30:00',
            'is_register' => '1',
            'is_post' => '1',
            'created_at' => '2020-11-29 18:49:21',
            'updated_at' => '2020-12-06 23:55:03',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '5',
            'memberid' => '101',
            'go_time' => '2020-12-24 18:00:00',
            'out_time' => '2020-12-24 22:30:00',
            'is_register' => '1',
            'is_post' => '1',
            'created_at' => '2020-11-29 18:49:21',
            'updated_at' => '2020-12-06 23:55:03',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '6',
            'memberid' => '101',
            'go_time' => '2020-12-25 18:00:00',
            'out_time' => '2020-12-25 22:30:00',
            'is_register' => '1',
            'is_post' => '1',
            'created_at' => '2020-11-29 18:49:21',
            'updated_at' => '2020-12-06 23:55:03',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '7',
            'memberid' => '102',
            'go_time' => '2020-12-16 18:00:00',
            'out_time' => '2020-12-17 05:00:00',
            'is_register' => '1',
            'is_post' => '1',
            'created_at' => '2020-11-29 18:49:21',
            'updated_at' => '2020-12-06 23:55:03',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '8',
            'memberid' => '102',
            'go_time' => '2020-12-18 17:00:00',
            'out_time' => '2020-12-19 05:00:00',
            'is_register' => '1',
            'is_post' => '1',
            'created_at' => '2020-11-29 18:49:21',
            'updated_at' => '2020-12-06 23:55:03',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '9',
            'memberid' => '102',
            'go_time' => '2020-12-23 19:00:00',
            'out_time' => '2020-12-14 05:00:00',
            'is_register' => '1',
            'is_post' => '1',
            'created_at' => '2020-11-29 18:49:21',
            'updated_at' => '2020-12-06 23:55:03',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '10',
            'memberid' => '102',
            'go_time' => '2020-12-27 18:00:00',
            'out_time' => '2020-12-28 05:00:00',
            'is_register' => '1',
            'is_post' => '1',
            'created_at' => '2020-11-29 18:49:21',
            'updated_at' => '2020-12-06 23:55:03',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '11',
            'memberid' => '102',
            'go_time' => '2020-12-28 18:30:00',
            'out_time' => '2020-12-29 05:00:00',
            'is_register' => '1',
            'is_post' => '1',
            'created_at' => '2020-11-29 18:49:21',
            'updated_at' => '2020-12-06 23:55:03',
        ];
        DB::table('create_records')->insert($param);

        //1月分
        $param = [
            'shift_createid' => '12',
            'memberid' => '101',
            'go_time' => '2021-01-08 18:00:00',
            'out_time' => '2021-01-09 00:00:00',
            'is_register' => null,
            'is_post' => '1',
            'created_at' => '2020-12-16 01:25:28',
            'updated_at' => '2020-12-16 01:26:41',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '13',
            'memberid' => '101',
            'go_time' => '2021-01-12 17:00:00',
            'out_time' => '2021-01-12 23:00:00',
            'is_register' => null,
            'is_post' => '1',
            'created_at' => '2020-12-16 01:25:28',
            'updated_at' => '2020-12-16 01:26:41',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '14',
            'memberid' => '101',
            'go_time' => '2021-01-13 18:00:00',
            'out_time' => '2021-01-13 23:00:00',
            'is_register' => null,
            'is_post' => '1',
            'created_at' => '2020-12-16 01:25:28',
            'updated_at' => '2020-12-16 01:26:41',
        ];
        DB::table('create_records')->insert($param);

        $param = [
            'shift_createid' => '15',
            'memberid' => '101',
            'go_time' => '2021-01-07 17:00:00',
            'out_time' => '2021-01-08 00:00:00',
            'is_register' => null,
            'is_post' => '1',
            'created_at' => '2020-12-16 01:25:28',
            'updated_at' => '2020-12-16 01:26:41',
        ];
        DB::table('create_records')->insert($param);
    }
}
