<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class stamp_records_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'memberid' => '101',
            'go_time' => '2020-12-18 18:19:33',
            'break_in' => null,
            'break_out' => null,
            'out_time' => '2020-12-19 01:01:41',
            'expense' => '100',
            'created_at' => '2020-12-18 18:19:35',
            'updated_at' => '2020-12-19 01:01:42',
            'go_flg' => '1',
            'in_flg' => '0',
            'fin_flg' => '0',
            'out_flg' => '1',
        ];
        DB::table('stamp_records')->insert($param);

        $param = [
            'memberid' => '101',
            'go_time' => '2020-12-17 18:18:17',
            'break_in' => null,
            'break_out' => null,
            'out_time' => '2020-12-17 23:00:10',
            'expense' => '100',
            'created_at' => '2020-12-17 18:18:19',
            'updated_at' => '2020-12-17 23:00:11',
            'go_flg' => '1',
            'in_flg' => '0',
            'fin_flg' => '0',
            'out_flg' => '1',
        ];
        DB::table('stamp_records')->insert($param);
    }
}
