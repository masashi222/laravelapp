<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRegisterRequest;
use App\MemberRecord;
use App\StampRecord;
use App\TimeRecord;
use Illuminate\Database\Eloquant\Scope;
use Illuminate\Database\Eloquant\Builder;
use Illuminate\Support\Facades\DB;

class AttendanceAdminController extends Controller
{
    //勤怠管理画面
    public function show(Request $request){
        //表示月
        date_default_timezone_set('Asia/Tokyo');
        $todate = date("j");
        if( $todate >25 ){
            $display[] = date("Y-m") . ',' . date("Y-m",strtotime(date("Y-m-1") . "+1 month"));
        }else{
            $display[] = date("Y-m",strtotime(date("Y-m-1") . "-1 month")) . ',' . date("Y-m");
        }
        $members = MemberRecord::where(function($query){
            $query->orWhere('business_no', '2')->orWhere('business_no', '3');
        })->get();
        foreach( $members as $member){
            //日時の取得
            date_default_timezone_set('Asia/Tokyo');
            $todate = date("j");
            $this21th = date("Y-m-21 00:00:00");
            $next20th = date("Y-m-21 23:59:59", strtotime("+1 month"));
            $last21th = date("Y-m-21 00:00:00", strtotime("-1 month"));
            $this20th = date("Y-m-20 23:59:59");
            $this25th = date("Y-m-25 23:59:59");
            //メンバー情報の取得
            $name = mb_substr($member->name . '　　',0,3);
            $memberid = $member->id;
            //〜25日で勤怠管理画面の確定給与を表示できるかどうか
            $is_timerecord = TimeRecord::where('memberid',$memberid)->whereBetween('created_at',[$this21th,$this25th])->exists();
            if( $todate > 25){
                //今月の21日から来月の20日の表示
                $stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$this21th,$next20th])->get();
                $info = [
                    '-',
                    '-',
                    '未確定',
                    '--',
                    '--',
                    $name,
                    $memberid,
                ];
                $rewards = 0;
                $expenses = 0;
                $work_times = 0;
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
                        //就業時間計算
                        $work_time = floor(strval(($out_diff - $fin_diff + $in_diff - $go_diff)/60));
                    }else if($in_flg == '0'){
                        //給与計算
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
                        //就業時間計算
                        $work_time = floor(strval(($out_diff - $go_diff)/60));
                    }
                    $work_times += $work_time;
                    $work_hours = floor(strval($work_times/60));
                    $work_minutes = '0' . $work_times % 60;
                    $work_minutes = substr( $work_minutes, -2);
                    $info = [
                        $expenses += $expense,
                        $rewards += $reward,
                        '未確定',
                        $work_hours,
                        $work_minutes,
                        $name,
                        $memberid,
                    ];
                }
            }else{
                //先月の21日から今月の20日の表示
                $stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$last21th,$this20th])->get();
                if ( $is_timerecord == true){
                    $info = [
                        '-',
                        '-',
                        0,
                        '--',
                        '--',
                        $name,
                        $memberid,
                    ];
                }else if( $is_timerecord == false){
                    $info = [
                        '-',
                        '-',
                        '未確定',
                        '--',
                        '--',
                        $name,
                        $memberid,
                    ];
                }
                $rewards = 0;
                $expenses = 0;
                $work_times = 0;
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
                        //就業時間計算
                        $work_time = floor(strval(($out_diff - $fin_diff + $in_diff - $go_diff)/60));
                    }else if($in_flg == '0'){
                        //給与計算
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
                        //就業時間計算
                        $work_time = floor(strval(($out_diff - $go_diff)/60));
                    }
                    $work_times += $work_time;
                    $work_hours = floor($work_times/60);
                    $work_minutes = '0' . $work_times % 60;
                    $work_minutes = substr( $work_minutes, -2);

                    if( $is_timerecord == true){
                        $info = [
                            $expenses += $expense,
                            $rewards += $reward,
                            $total = $rewards + $expenses*0.7,
                            $work_hours,
                            $work_minutes,
                            $name,
                            $memberid,
                        ];
                    }else if( $is_timerecord == false){
                        $info = [
                            $expenses += $expense,
                            $rewards += $reward,
                            '未確定',
                            $work_hours,
                            $work_minutes,
                            $name,
                            $memberid,
                        ];
                    }
                }
            }
            $contents[] = $info;
        }
        //給与計算書
        $timerecords = Timerecord::exists();
        if( $timerecords == true){
            $display[] = 1;
        }else if( $timerecords == false){
            $display[] = 0;
        }
        return view('admin.attendance-staff-record',['contents'=>$contents,'display'=>$display]);
    }
    //今月分の勤怠一覧
    public function record(Request $request,$id){
        //日時の取得
        date_default_timezone_set('Asia/Tokyo');
        $todate = date("j");
        $this21th = date("Y-m-21 00:00:00");
        $this25th = date("Y-m-25 23:59:59");
        $next20th = date("Y-m-20 23:59:59", strtotime("+1 month"));
        $last21th = date("Y-m-21 00:00:00", strtotime("-1 month"));
        $this20th = date("Y-m-20 23:59:59");
        //メンバー情報の取得
        $member = MemberRecord::where('id',$id)->first();
        $memberid = $member->id;
        $name = $member->name;
        if($todate > 25){
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
            $is_timerecord = TimeRecord::where('memberid',$memberid)->where('created_at',[$this21th,$this25th])->exists();
            if( $is_timerecord == true){
                $display_register = 0;
            }else if( $is_timerecord == false){
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
        //給与確定ボタンの表示
        date_default_timezone_set('Asia/Tokyo');
        $today_str = strtotime(date("Y-m-d H:i:s"));
        $this21th = date("Y-m-21 00:00:00");
        $this25th = date("Y-m-25 23:59:59");
        $this21th_str = strtotime(date("Y-m-21 00:00:00"));
        $this25th_str = strtotime(date("Y-m-25 23:59:59"));
        $is_timerecord = TimeRecord::where('memberid',$id)->whereBetween('created_at',[$this21th,$this25th])->exists();
        if($is_timerecord == true && $today_str >= $this21th_str && $today_str <= $this25th_str){
            $display = 0;
        }else if($is_timerecord == false && $today_str >= $this21th_str && $today_str <= $this25th_str){
            $display = 1;
        }else{
            $display = 2;
        }
        return view('admin.attendance-record',['data'=>$data,'info'=>$info,'display'=>$display]);
    }
    //先月分の勤怠一覧
    public function recordLast(Request $request,$id){
        //日時の取得
        date_default_timezone_set('Asia/Tokyo');
        $todate = date("j");
        $last21th = date("Y-m-21 00:00:00", strtotime("-1 month"));
        $this20th = date("Y-m-20 23:59:59");
        $before_last21th = date("Y-m-21 00:00:00", strtotime("-2 month"));
        $last20th = date("Y-m-20 23:59:59", strtotime("-1 month"));
        //メンバー情報の取得
        $member = MemberRecord::where('id',$id)->first();
        $memberid = $member->id;
        $name = $member->name;
        //登録ボタンの表示
        $display_register = 0;
        if($todate > 25){
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
        return view('admin.attendance-record-last',['data'=>$data,'info'=>$info]);
    }
    //来月分の勤怠一覧
    public function recordNext(Request $request,$id){
        //日時の取得
        date_default_timezone_set('Asia/Tokyo');
        $todate = date("j");
        $this21th = date("Y-m-21 00:00:00");
        $next20th = date("Y-m-20 23:59:59", strtotime("+1 month"));
        $next21th = date("Y-m-21 00:00:00", strtotime("+1 month"));
        $after_next20th = date("Y-m-20 23:59:59", strtotime("+2 month"));
        //メンバー情報の取得
        $member = MemberRecord::where('id',$id)->first();
        $memberid = $member->id;
        $name = $member->name;
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
        return view('admin.attendance-record-next',['data'=>$data,'info'=>$info]);
    }
        //勤怠修正画面
    public function update($id,$stampid){
        $stamp = StampRecord::where('time_stampid',$stampid)->first();
        $go_time = substr(substr_replace($stamp->go_time,'T',10,1),0,16);
        $out_time = substr(substr_replace($stamp->out_time,'T',10,1),0,16);
        $date = substr($stamp->go_time, 0, 10);
        if( $stamp->break_in !== null){
            $break_in = substr(substr_replace($stamp->break_in,'T',10,1),0,16);
        }else{
            $break_in = null;
        }
        if( $stamp->break_out !== null){
            $break_out = substr(substr_replace($stamp->break_out,'T',10,1),0,16);
        }else{
            $break_out = null;
        }
        $data =[
            $stamp->memberid,
            $stamp->getName(),
            $date,
            $go_time,
            $break_in,
            $break_out,
            $out_time,
            $stamp->expense,
            $stamp->time_stampid,
        ];
        return view('admin.attendance-update',['data'=>$data]);
    }

    public function fix(AttendanceRegisterRequest $request,$id,$stampid){
        date_default_timezone_set('Asia/Tokyo');
        $stampid = $request->route()->parameter('stampid');
        $form = $request->all();
        if( isset($form['update-btn'])){
            //変更ボタン押下
            $date = StampRecord::where('time_stampid',$stampid)->first()->go_time;
            $date = substr_replace($date,'00:00:00',11,8);
            $date_timestamp = strtotime($date);
            $date_timestamp_1 = strtotime(date("Y-m-d",$date_timestamp) . "+1 day");
            $go_time = substr_replace($form['go_time'],"\n",10,1);
            $go_timestamp = strtotime($go_time);
            $out_time = substr_replace($form['out_time'],"\n",10,1);
            $out_timestamp = strtotime($out_time);
            if( $form['break_in'] !== null){
                $break_in = substr_replace($form['break_in'],"\n",10,1);
                $in_timestamp = strtotime($break_in);
            }else{
                $break_in = null;
            }
            if( $form['break_out'] !== null){
                $break_out = substr_replace($form['break_out'],"\n",10,1);
                $fin_timestamp = strtotime($break_out);
            }else{
                $break_out = null;
            }
            $expense = $form['expense'];
            if($go_timestamp >= $date_timestamp && $go_timestamp < $date_timestamp_1 && isset($go_time) && !isset($break_in) && !isset($break_out) && isset($out_time) &&
                isset($expense) && $go_timestamp <= $out_timestamp){
                    //休憩なしの挿入処理
                    StampRecord::where('time_stampid',$stampid)->update([
                        'go_time' => $go_time,
                        'break_in' => $break_in,
                        'break_out' => $break_out,
                        'out_time' => $out_time,
                        'expense' => $expense,
                    ]);
            }else if($go_timestamp >= $date_timestamp && $go_timestamp < $date_timestamp_1 && isset($go_time) && isset($break_in) && isset($break_out) && isset($out_time) &&
                isset($expense) && $go_timestamp <= $in_timestamp && $in_timestamp <= $fin_timestamp && $fin_timestamp <= $out_timestamp){
                    //休憩ありの挿入処理
                    StampRecord::where('time_stampid',$stampid)->update([
                        'go_time' => $go_time,
                        'break_in' => $break_in,
                        'break_out' => $break_out,
                        'out_time' => $out_time,
                        'expense' => $expense,
                    ]);
            }else{
                return redirect()->route('attendance.update',['id'=>$id,'stampid'=>$stampid])->with('flash_message', '日時の設定が不正です');
            }
        }else if( isset($form['delete-btn'])){
            //削除ボタン押下
            StampRecord::where('time_stampid',$stampid)->delete();
        }
        return redirect()->route('attendance.record',['id'=>$id]);
    }
    //勤怠登録画面
    public function add($id){
        $member = MemberRecord::where('id',$id)->first();
        $is_expense = $member->expense;
        if( isset($is_expense) ){
            $expense = $member->expense;
        }else if( !isset($is_expense)){
            $expense = null;
        }
        $data = [
            $member->id,
            $member->name,
            $expense,
        ];
        return view('admin.attendance-register',['data'=>$data]);
    }

    public function create(AttendanceRegisterRequest $request,$id){
        date_default_timezone_set('Asia/Tokyo');
        $form = $request->all();
        $form['go_time'] = substr_replace($form['go_time'],"\n",10,1);
        $go_timestamp = strtotime($form['go_time']);
        $form['out_time'] = substr_replace($form['out_time'],"\n",10,1);
        $out_timestamp = strtotime($form['out_time']);
        unset($form['_token']);
        if( $form['break_in'] !== null){
            $form['break_in']  = substr_replace($form['break_in'],"\n",10,1);
            $in_timestamp = strtotime($form['break_in']);
        }else{
            $form['break_in'] = null;
        }
        if( $form['break_out'] !== null){
            $form['break_out'] = substr_replace($form['break_out'],"\n",10,1);
            $fin_timestamp = strtotime($form['break_out']);
        }else{
            $form['break_out'] = null;
        }
        $memberid = $id;
        $form = array_merge( $form, array('memberid' => $memberid));
        $form = array_merge( $form, array('out_flg' => 1));
        $today_timestamp = strtotime(date("Y-m-d H:i:s"));
        $is_timerecord = TimeRecord::where('memberid',$memberid)->exists();
        if($is_timerecord == true){
            $timerecord = TimeRecord::where('memberid',$memberid)->first();
            $created_timestamp = strtotime(substr_replace($timerecord->created_at, '21 00:00:00',8));
        }else if($is_timerecord == false){
            $created_timestamp = null;
        }

        if( $today_timestamp >= $go_timestamp && $today_timestamp >= $out_timestamp && ($created_timestamp <= $go_timestamp || $created_timestamp == null)){
            //登録できる
            if(isset($form['go_time']) && !isset($form['break_in']) && !isset($form['break_out']) && isset($form['out_time']) &&
                isset($form['expense']) && $go_timestamp <= $out_timestamp){
                    //出→退
                    $stamp = new StampRecord;
                    $stamp->fill($form)->save();
            }else if(isset($form['go_time']) && isset($form['break_in']) && isset($form['break_out']) && isset($form['out_time']) &&
                isset($form['expense']) && $go_timestamp <= $in_timestamp && $in_timestamp <= $fin_timestamp && $fin_timestamp <= $out_timestamp){
                    //出→入→終→退
                    $stamp = new StampRecord;
                    $stamp->fill($form)->save();
            }else{
                return redirect()->route('attendance.register',['id'=>$id])->with('flash_message', '日時の設定が不正です');
            }
        }else{
            //登録できない
            return redirect()->route('attendance.register',['id'=>$id])->with('flash_message', '日時の設定が不正です');
        }
        return redirect()->route('attendance.record',['id'=>$id]);
    }

    public function payroll(){
        $members =MemberRecord::where(function($query){
            $query->orWhere('business_no', '2')->orWhere('business_no', '3');
        })->orderBy('number','asc')->get();
        foreach( $members as $member){
            $memberid = $member->id;
            $is_timerecord = TimeRecord::where('memberid',$memberid)->exists();
            if( $is_timerecord == true ){
                $timerecord = TimeRecord::where('memberid',$memberid)->first();
            }else if( $is_timerecord == false ){
                continue;
            }

            $deadline = substr(substr_replace($timerecord->created_at, '20', 8, 2), 0, 10);
            $deadline_fix = substr_replace($deadline, '21', 8, 2);
            $startline = date("Y-m-d",strtotime(date($deadline_fix) . "-1 month"));
            $data[] = [
                $startline,
                $deadline,
                $timerecord->getNumber(),
                $timerecord->memberid,
                $timerecord->getName(),
                $timerecord->go_days,
                substr($timerecord->normaly_worktime,0,5),
                substr($timerecord->midnight_worktime,0,5),
                number_format($timerecord->normaly_salary),
                number_format($timerecord->midnight_salary),
                number_format($timerecord->salary),
                number_format($timerecord->expense),
                number_format($timerecord->fixed_salary),
                number_format(($timerecord->expense)*0.7),
            ];
        }
        return view('admin.payroll',['data'=>$data]);
    }
}
