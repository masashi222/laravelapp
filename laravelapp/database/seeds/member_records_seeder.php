<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class member_records_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'id' => '100',
            'number' => null,
            'name' => '山田太郎',
            'pass' => '$2y$10$qP1T2t4KgDpxL.AhiHuQl.YiAUxahsJ0dt6O2ShuljjpZdXKIcgZq',
            'salary' => '0',
            'expense' => null,
            'business_no' => '1',
        ];
        DB::table('member_records')->insert($param);

        $param = [
            'id' => '101',
            'number' => '55',
            'name' => '田中花子',
            'pass' => '$2y$10$qP1T2t4KgDpxL.AhiHuQl.YiAUxahsJ0dt6O2ShuljjpZdXKIcgZq',
            'salary' => '900',
            'expense' => null,
            'business_no' => '3',
        ];
        DB::table('member_records')->insert($param);

        $param = [
            'id' => '102',
            'number' => '66',
            'name' => '佐藤隆',
            'pass' => '$2y$10$qP1T2t4KgDpxL.AhiHuQl.YiAUxahsJ0dt6O2ShuljjpZdXKIcgZq',
            'salary' => '900',
            'expense' => null,
            'business_no' => '3',
        ];
        DB::table('member_records')->insert($param);
    }
}
