<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KeyRecord;
use Illuminate\Support\Facades\DB;

class KeyGenerateController extends Controller
{
    public function create(){
        //打刻キーの生成
        date_default_timezone_set('Asia/Tokyo');
        $date = date('Y-m-d');
        $length = 4;
        $max = pow(10, $length) - 1;
        $rand = random_int(0, $max);
        $code = sprintf('%0'. $length. 'd', $rand);
        DB::table('key_records')->delete();
        $key = new KeyRecord();
        $key->fill([
            'date' => $date,
            'code' => $code,
        ])->save();
    }

    public function show(){
        //打刻キーの表示
        date_default_timezone_set('Asia/Tokyo');
//         $date = date('Y-m-d');
        $key = KeyRecord::first();
        if( isset($key)){
            $code = $key->code;
        }else{
            $code = '本日の打刻キーは未発行です';
        }
        return view ('admin.stamp-pass',['code'=>$code]);
    }
}
