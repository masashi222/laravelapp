<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class business_masters_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'business_no' => '1',
            'business' => 'オーナー',
        ];
        DB::table('business_masters')->insert($param);

        $param = [
            'business_no' => '2',
            'business' => '正社員',
        ];
        DB::table('business_masters')->insert($param);

        $param = [
            'business_no' => '3',
            'business' => 'アルバイト',
        ];
        DB::table('business_masters')->insert($param);
    }
}
