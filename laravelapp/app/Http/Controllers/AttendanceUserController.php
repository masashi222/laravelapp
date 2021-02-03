<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\MemberRecord;
use App\StampRecord;
use App\TimeRecord;
use Illuminate\Database\Eloquant\Scope;
use Illuminate\Database\Eloquant\Builder;
use Illuminate\Support\Facades\DB;

class AttendanceUserController extends Controller
{
    //勤怠一覧画面
    public function record(Request $request) {
        //日時の取得
        date_default_timezone_set('Asia/Tokyo');
        $todate = date("j");
        $this21th = date("Y-m-21 00:00:00");
        $this25th = date("Y-m-25 23:59:59");
        $next20th = date("Y-m-20 23:59:59", strtotime("+1 month"));
        $last21th = date("Y-m-21 00:00:00", strtotime("-1 month"));
        $this20th = date("Y-m-20 23:59:59");
        //メンバー情報の取得
        if(Auth::check()){
            //認証済
            $id = Auth::id();
            $member = MemberRecord::where('id',$id)->first();
            $memberid = $member->id;
            $name = $member->name;
        }else{
            return redirect ('/login');
        }
        if( $todate > 25){
            //今月の21日から来月の20日の表示
            $stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$this21th,$next20th])
            ->orderBy('go_time')->get();
            $is_stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$this21th,$next20th])
            ->exists();
            $display_month = date("Y-m") . ',' . date("Y-m",strtotime(date("Y-m-1") . "+1 month"));
            //登録ボタンの表示
            $display_register = 1;

        }else{
            //先月の21日から今月の20日の表示
            $stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$last21th,$this20th])
            ->orderBy('go_time')->get();
            $is_stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$last21th,$this20th])
            ->exists();
            $display_month = date("Y-m",strtotime(date("Y-m-1") . "-1 month")) . ',' . date("Y-m");
            //登録ボタンの表示
            $timerecord = TimeRecord::where('memberid',$memberid)->whereBetween('created_at',[$this21th,$this25th])->exists();
            if( $timerecord == true){
                $display_register = 0;
            }else if( $timerecord == false){
                $display_register = 1;
            }
        }
        if( $is_stamps == false){
            $data[] = [
                '-',
                '-',
                '--:--',
                '--:--',
                '-',
                '-',
                0,
            ];
            $info = [
                $display_month,
                '-',
                '-',
                '-',
                $name,
                $memberid,
                $display_register,
            ];
        }
        $rewards = 0;
        $expenses = 0;
        foreach( $stamps as $stamp){
            //時給・分給
            $salary = $stamp->getSalary();
            $min = round($salary/60,5);
            $salary_mid = floor(strval($salary*1.25));
            $min_mid = round($salary_mid/60,5);
            //交通費
            if($stamp->getExpense() !== null){
                $expense = $stamp->getExpense();
            }else{
                $expense = $stamp->expense;
            }
            //日付・曜日
            $date = $stamp->go_time;
            $date = substr( $date, 8 , 2);
            $timestamp = strtotime($stamp->go_time);
            $day = date('w', $timestamp);
            $week = [
                '日', //0
                '月', //1
                '火', //2
                '水', //3
                '木', //4
                '金', //5
                '土', //6
            ];
            $day = $week[$day];
            //打刻時間
            $go_time = $stamp->go_time;
            $go_diff = strtotime($go_time);
            $ten_oclock = substr( $go_time, 0, 11) . '22:00:00';
            $ten_diff = strtotime($ten_oclock);
            $out_time = $stamp->out_time;
            $out_diff = strtotime($out_time);
            $break_in = $stamp->break_in;
            $in_diff = strtotime($break_in);
            $break_out = $stamp->break_out;
            $fin_diff = strtotime($break_out);
            //フラグ
            $in_flg = $stamp->in_flg;
            if($in_flg == '1'){
                //給与計算
                if(($go_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$fin_diff+$in_diff-$go_diff)/60))*$min_mid;
                    $reward = floor(strval($reward));
                }else if(($in_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$fin_diff+$in_diff-$ten_diff)/60))*$min_mid + floor(($ten_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($fin_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$fin_diff)/60))*$min_mid + floor(($in_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($out_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$ten_diff)/60))*$min_mid + floor(($ten_diff-$fin_diff+$in_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($ten_diff - $out_diff) >= 0){
                    $reward = floor(strval(($out_diff-$fin_diff+$in_diff-$go_diff)/60))*$min;
                    $reward = floor(strval($reward));
                }
            }else if($in_flg == '0'){
                if(($go_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$go_diff)/60))*$min_mid;
                    $reward = floor(strval($reward));
                }else if(($out_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$ten_diff)/60))*$min_mid + floor(($ten_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($ten_diff - $out_diff) >= 0){
                    $reward = floor(strval(($out_diff-$go_diff)/60))*$min;
                    $reward = floor(strval($reward));
                }
            }
            $go_time_edit = substr( $go_time, 11, 5 );
            $out_time_edit = substr( $out_time, 11, 5 );
            $time_stampid = $stamp->time_stampid;
            $data[] = [
                $date,
                $day,
                $go_time_edit,
                $out_time_edit,
                $reward,
                $expense,
                $time_stampid,
            ];
            $info = [
                $display_month,
                $expenses += $expense,
                $rewards += $reward,
                $total = $rewards + $expenses*0.7,
                $name,
                $memberid,
                $display_register,
            ];
        }
        return view('staff.attendance-record',['data' => $data,'info'=>$info]);
    }
    //先月の勤怠一覧画面
    public function recordLast(Request $request) {
        //日時の取得
        date_default_timezone_set('Asia/Tokyo');
        $todate = date("j");
        $last21th = date("Y-m-21 00:00:00", strtotime("-1 month"));
        $this20th = date("Y-m-20 23:59:59");
        $before_last21th = date("Y-m-21 00:00:00", strtotime("-2 month"));
        $last20th = date("Y-m-20 23:59:59", strtotime("-1 month"));
        //メンバー情報の取得
        if(Auth::check()){
            //認証済
            $id = Auth::id();
            $member = MemberRecord::where('id',$id)->first();
            $memberid = $member->id;
            $name = $member->name;
        }else{
            return redirect ('/login');
        }
        //登録ボタンの表示
        $display_register = 0;
        if( $todate > 25){
            //今月の21日から来月の20日の表示
            $stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$last21th,$this20th])
            ->orderBy('go_time')->get();
            $is_stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$last21th,$this20th])
            ->exists();
            $display_month = date("Y-m",strtotime(date("Y-m-1") . "-1 month")) . ',' . date("Y-m");
        }else{
            //先月の21日から今月の20日の表示
            $stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$before_last21th,$last20th])
            ->orderBy('go_time')->get();
            $is_stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$before_last21th,$last20th])
            ->exists();
            $display_month = date("Y-m",strtotime(date("Y-m-1") . "-2 month")) . ',' . date("Y-m",strtotime(date("Y-m-1") . "-1 month"));
        }
        if( $is_stamps == false){
            $data[] = [
                '-',
                '-',
                '--:--',
                '--:--',
                '-',
                '-',
                0,
            ];
            $info = [
                $display_month,
                '-',
                '-',
                '-',
                $name,
                $memberid,
                $display_register,
            ];
        }
        $rewards = 0;
        $expenses = 0;
        foreach( $stamps as $stamp){
            //時給・分給
            $salary = $stamp->getSalary();
            $min = round($salary/60,5);
            $salary_mid = floor(strval($salary*1.25));
            $min_mid = round($salary_mid/60,5);
            //交通費
            if($stamp->getExpense() !== null){
                $expense = $stamp->getExpense();
            }else{
                $expense = $stamp->expense;
            }
            //日付・曜日
            $date = $stamp->go_time;
            $date = substr( $date, 8 , 2);
            $timestamp = strtotime($stamp->go_time);
            $day = date('w', $timestamp);
            $week = [
                '日', //0
                '月', //1
                '火', //2
                '水', //3
                '木', //4
                '金', //5
                '土', //6
            ];
            $day = $week[$day];
            //打刻時間
            $go_time = $stamp->go_time;
            $go_diff = strtotime($go_time);
            $ten_oclock = substr( $go_time, 0, 11) . '22:00:00';
            $ten_diff = strtotime($ten_oclock);
            $out_time = $stamp->out_time;
            $out_diff = strtotime($out_time);
            $break_in = $stamp->break_in;
            $in_diff = strtotime($break_in);
            $break_out = $stamp->break_out;
            $fin_diff = strtotime($break_out);
            //フラグ
            $in_flg = $stamp->in_flg;
            if($in_flg == '1'){
                //給与計算
                if(($go_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$fin_diff+$in_diff-$go_diff)/60))*$min_mid;
                    $reward = floor(strval($reward));
                }else if(($in_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$fin_diff+$in_diff-$ten_diff)/60))*$min_mid + floor(($ten_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($fin_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$fin_diff)/60))*$min_mid + floor(($in_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($out_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$ten_diff)/60))*$min_mid + floor(($ten_diff-$fin_diff+$in_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($ten_diff - $out_diff) >= 0){
                    $reward = floor(strval(($out_diff-$fin_diff+$in_diff-$go_diff)/60))*$min;
                    $reward = floor(strval($reward));
                }
            }else if($in_flg == '0'){
                if(($go_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$go_diff)/60))*$min_mid;
                    $reward = floor(strval($reward));
                }else if(($out_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$ten_diff)/60))*$min_mid + floor(($ten_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($ten_diff - $out_diff) >= 0){
                    $reward = floor(strval(($out_diff-$go_diff)/60))*$min;
                    $reward = floor(strval($reward));
                }
            }
            $go_time_edit = substr( $go_time, 11, 5 );
            $out_time_edit = substr( $out_time, 11, 5 );
            $time_stampid = $stamp->time_stampid;
            $data[] = [
                $date,
                $day,
                $go_time_edit,
                $out_time_edit,
                $reward,
                $expense,
                $time_stampid,
            ];
            $info = [
                $display_month,
                $expenses += $expense,
                $rewards += $reward,
                $total = $rewards + $expenses*0.7,
                $name,
                $memberid,
                $display_register,
            ];
        }
        return view('staff.attendance-record-last',['data' => $data,'info'=>$info]);
    }
    //来月の勤怠一覧画面
    public function recordNext(Request $request) {
        //日時の取得
        date_default_timezone_set('Asia/Tokyo');
        $todate = date("j");
        $this21th = date("Y-m-21 00:00:00");
        $next20th = date("Y-m-20 23:59:59", strtotime("+1 month"));
        $next21th = date("Y-m-21 00:00:00", strtotime("+1 month"));
        $after_next20th = date("Y-m-20 23:59:59", strtotime("+2 month"));
        //メンバー情報の取得
        if(Auth::check()){
            //認証済
            $id = Auth::id();
            $member = MemberRecord::where('id',$id)->first();
            $memberid = $member->id;
            $name = $member->name;
        }else{
            return redirect ('/login');
        }
        //登録ボタン表示
        $display_register = 1;
        if( $todate > 25){
            //今月の21日から来月の20日の表示
            $stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$next21th,$after_next20th])
            ->orderBy('go_time')->get();
            $is_stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$next21th,$after_next20th])
            ->exists();
            $display_month = date("Y-m",strtotime(date("Y-m-1") . "+1 month")) . ',' . date("Y-m",strtotime(date("Y-m-1") . "+2 month"));
        }else{
            //先月の21日から今月の20日の表示
            $stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$this21th,$next20th])
            ->orderBy('go_time')->get();
            $is_stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$this21th,$next20th])
            ->exists();
            $display_month =  date("Y-m") . ',' . date("Y-m",strtotime(date("Y-m-1") . "+1 month"));
        }
        if( $is_stamps == false){
            $data[] = [
                '-',
                '-',
                '--:--',
                '--:--',
                '-',
                '-',
                0,
            ];
            $info = [
                $display_month,
                '-',
                '-',
                '-',
                $name,
                $memberid,
                $display_register,
            ];
        }
        $rewards = 0;
        $expenses = 0;
        foreach( $stamps as $stamp){
            //時給・分給
            $salary = $stamp->getSalary();
            $min = round($salary/60,5);
            $salary_mid = floor(strval($salary*1.25));
            $min_mid = round($salary_mid/60,5);
            //交通費
            if($stamp->getExpense() !== null){
                $expense = $stamp->getExpense();
            }else{
                $expense = $stamp->expense;
            }
            //日付・曜日
            $date = $stamp->go_time;
            $date = substr( $date, 8 , 2);
            $timestamp = strtotime($stamp->go_time);
            $day = date('w', $timestamp);
            $week = [
                '日', //0
                '月', //1
                '火', //2
                '水', //3
                '木', //4
                '金', //5
                '土', //6
            ];
            $day = $week[$day];
            //打刻時間
            $go_time = $stamp->go_time;
            $go_diff = strtotime($go_time);
            $ten_oclock = substr( $go_time, 0, 11) . '22:00:00';
            $ten_diff = strtotime($ten_oclock);
            $out_time = $stamp->out_time;
            $out_diff = strtotime($out_time);
            $break_in = $stamp->break_in;
            $in_diff = strtotime($break_in);
            $break_out = $stamp->break_out;
            $fin_diff = strtotime($break_out);
            //フラグ
            $in_flg = $stamp->in_flg;
            if($in_flg == '1'){
                //給与計算
                if(($go_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$fin_diff+$in_diff-$go_diff)/60))*$min_mid;
                    $reward = floor(strval($reward));
                }else if(($in_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$fin_diff+$in_diff-$ten_diff)/60))*$min_mid + floor(($ten_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($fin_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$fin_diff)/60))*$min_mid + floor(($in_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($out_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$ten_diff)/60))*$min_mid + floor(($ten_diff-$fin_diff+$in_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($ten_diff - $out_diff) >= 0){
                    $reward = floor(strval(($out_diff-$fin_diff+$in_diff-$go_diff)/60))*$min;
                    $reward = floor(strval($reward));
                }
            }else if($in_flg == '0'){
                if(($go_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$go_diff)/60))*$min_mid;
                    $reward = floor(strval($reward));
                }else if(($out_diff - $ten_diff) > 0){
                    $reward = floor(strval(($out_diff-$ten_diff)/60))*$min_mid + floor(($ten_diff-$go_diff)/60)*$min;
                    $reward = floor(strval($reward));
                }else if(($ten_diff - $out_diff) >= 0){
                    $reward = floor(strval(($out_diff-$go_diff)/60))*$min;
                    $reward = floor(strval($reward));
                }
            }
            $go_time_edit = substr( $go_time, 11, 5 );
            $out_time_edit = substr( $out_time, 11, 5 );
            $time_stampid = $stamp->time_stampid;
            $data[] = [
                $date,
                $day,
                $go_time_edit,
                $out_time_edit,
                $reward,
                $expense,
                $time_stampid,
            ];
            $info = [
                $display_month,
                $expenses += $expense,
                $rewards += $reward,
                $total = $rewards + $expenses*0.7,
                $name,
                $memberid,
                $display_register,
            ];
        }
        return view('staff.attendance-record-next',['data' => $data,'info'=>$info]);
    }
}
