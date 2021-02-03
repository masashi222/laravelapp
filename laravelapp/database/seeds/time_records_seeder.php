<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class time_records_seeder extends Seeder
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
            'go_days' => '6',
            'normaly_worktime' => '25:08:00',
            'midnight_worktime' => '10:41:00',
            'normaly_salary' => '22620',
            'midnight_salary' => '12018',
            'salary' => '34636',
            'expense' => '700',
            'fixed_salary' => '35126',
            'created_at' => '2020-11-24 17:00:00',
            'updated_at' => '2020-11-24 17:00:00',
        ];
        DB::table('time_records')->insert($param);

        $param = [
            'memberid' => '102',
            'go_days' => '3',
            'normaly_worktime' => '11:18:00',
            'midnight_worktime' => '07:29:00',
            'normaly_salary' => '10170',
            'midnight_salary' => '8418',
            'salary' => '18588',
            'expense' => '400',
            'fixed_salary' => '18868',
            'created_at' => '2020-11-24 17:10:00',
            'updated_at' => '2020-11-24 17:10:00',
        ];
        DB::table('time_records')->insert($param);
    }
}
