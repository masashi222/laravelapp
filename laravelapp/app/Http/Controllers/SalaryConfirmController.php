<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MemberRecord;
use App\StampRecord;
use App\TimeRecord;

class SalaryConfirmController extends Controller
{
    //給与確定
    public function create(Request $request,$id){
        $btn_info = $request->all();
        if( isset($btn_info['confirm-btn']) ){
            //日時の取得
            date_default_timezone_set('Asia/Tokyo');
            $this20th = date("Y-m-20 23:59:59");
            $last21th = date("Y-m-21 00:00:00", strtotime("-1 month"));
            $this21th = date("Y-m-21 00:00:00");
            $this25th = date("Y-m-25 23:59:59");
            //メンバー情報の取得
            $member = MemberRecord::where('id',$id)->first();
            $memberid = $member->id;
            //先月の21日から今月の20日の表示
            $stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$last21th,$this20th])
            ->get();
            $info = [
                $memberid,
                '00:00',
                '00:00',
                '0',
                '0',
                '0',
                '0',
                '0',
            ];
            $rewards = 0;
            $expenses = 0;
            $normaly_worktimes = 0;
            $midnight_worktimes = 0;
            $normaly_salaries = 0;
            $midnight_salaries = 0;
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
                        $normaly_worktime = 0;
                        $normaly_salary = 0;
                        $midnight_worktime = floor(strval(($out_diff-$fin_diff+$in_diff-$go_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $midnight_salary;
                        $reward = floor(strval($reward));
                    }else if(($in_diff - $ten_diff) > 0){
                        $normaly_worktime = floor(strval(($ten_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = floor(strval(($out_diff-$fin_diff+$in_diff-$ten_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $normaly_salary + $midnight_salary;
                        $reward = floor(strval($reward));
                    }else if(($fin_diff - $ten_diff) > 0){
                        $normaly_worktime = floor(strval(($in_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = floor(strval(($out_diff-$fin_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $midnight_salary + $normaly_salary;
                        $reward = floor(strval($reward));
                    }else if(($out_diff - $ten_diff) > 0){
                        $normaly_worktime = floor(strval(($ten_diff-$fin_diff+$in_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = floor(strval(($out_diff-$ten_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $midnight_salary + $normaly_salary;
                        $reward = floor(strval($reward));
                    }else if(($ten_diff - $out_diff) >= 0){
                        $normaly_worktime = floor(strval(($out_diff-$fin_diff+$in_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = 0;
                        $midnight_salary = 0;
                        $reward = $normaly_salary;
                        $reward = floor(strval($reward));
                    }
                }else if($in_flg == '0'){
                    //給与計算
                    if(($go_diff - $ten_diff) > 0){
                        $normaly_worktime = 0;
                        $normaly_salary = 0;
                        $midnight_worktime = floor(strval(($out_diff-$go_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $midnight_salary;
                        $reward = floor(strval($reward));
                    }else if(($out_diff - $ten_diff) > 0){
                        $normaly_worktime = floor(strval(($ten_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = floor(strval(($out_diff-$ten_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $normaly_salary + $midnight_salary;
                        $reward = floor(strval($reward));
                    }else if(($ten_diff - $out_diff) >= 0){
                        $normaly_worktime = floor(strval(($out_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = 0;
                        $midnight_salary = 0;
                        $reward = $normaly_salary;
                        $reward = floor(strval($reward));
                    }
                }
                $normaly_worktimes += $normaly_worktime;
                $normaly_workhours = floor(strval($normaly_worktimes/60));
                $normaly_workminutes = '0' . $normaly_worktimes % 60;
                $normaly_workminutes = substr( $normaly_workminutes, -2);
                $normaly_worktimes_edit = $normaly_workhours . ':' . $normaly_workminutes;

                $midnight_worktimes += $midnight_worktime;
                $midnight_workhours = floor(strval($midnight_worktimes/60));
                $midnight_workminutes = '0' . $midnight_worktimes % 60;
                $midnight_workminutes = substr( $midnight_workminutes, -2);
                $midnight_worktimes_edit = $midnight_workhours . ':' . $midnight_workminutes;

                $normaly_salaries += $normaly_salary;
                $midnight_salaries += $midnight_salary;

                $info = [
                    $memberid,
                    $normaly_worktimes_edit,
                    $midnight_worktimes_edit,
                    floor(strval($normaly_salaries)),
                    floor(strval($midnight_salaries)),
                    $expenses += $expense,
                    $rewards += $reward,
                    $total = $rewards + $expenses*0.7,
                ];
            }
            //レコードの挿入
            $is_timerecord = TimeRecord::where('memberid',$memberid)->whereBetween('created_at',[$this21th,$this25th])->exists();
            if( $is_timerecord == false ){
                $timerecord = TimeRecord::where('memberid',$memberid)->delete();
                $timerecord = new TimeRecord;
                $timerecord->memberid = $info[0];
                $timerecord->go_days = count($stamps);
                $timerecord->normaly_worktime = $info[1];
                $timerecord->midnight_worktime = $info[2];
                $timerecord->normaly_salary = $info[3];
                $timerecord->midnight_salary = $info[4];
                $timerecord->expense = $info[5];
                $timerecord->salary = $info[6];
                $timerecord->fixed_salary = $info[7];
                $timerecord->save();
            }
        }else if( isset($btn_info['cancel-btn']) ){
            //メンバー情報の取得
            $id = $request->route()->parameter('id');
            $member = MemberRecord::where('id',$id)->first();
            $memberid = $member->id;
            //レコードの削除
            $timerecord = TimeRecord::where('memberid',$memberid)->delete();
        }
        return redirect ('/admin/attendance-staff-record');
    }

    public function confirm(){
        //日時の取得
        date_default_timezone_set('Asia/Tokyo');
        $this20th = date("Y-m-20 23:59:59");
        $last21th = date("Y-m-21 00:00:00", strtotime("-1 month"));
        $this21th = date("Y-m-21 00:00:00");
        $this25th = date("Y-m-25 23:59:59");
        //メンバー情報の取得
        $members = MemberRecord::where(function($query){
            $query->orWhere('business_no', '2')->orWhere('business_no', '3');
        })->get();
        foreach( $members as $member){
            $memberid = $member->id;
            //先月の21日から今月の20日の表示
            $stamps = StampRecord::where('memberid',$memberid)->where('out_flg','1')->whereBetween('go_time',[$last21th,$this20th])
            ->get();
            $info = [
                $memberid,
                '00:00',
                '00:00',
                '0',
                '0',
                '0',
                '0',
                '0',
            ];
            $rewards = 0;
            $expenses = 0;
            $normaly_worktimes = 0;
            $midnight_worktimes = 0;
            $normaly_salaries = 0;
            $midnight_salaries = 0;
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
                        $normaly_worktime = 0;
                        $normaly_salary = 0;
                        $midnight_worktime = floor(strval(($out_diff-$fin_diff+$in_diff-$go_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $midnight_salary;
                        $reward = floor(strval($reward));
                    }else if(($in_diff - $ten_diff) > 0){
                        $normaly_worktime = floor(strval(($ten_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = floor(strval(($out_diff-$fin_diff+$in_diff-$ten_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $normaly_salary + $midnight_salary;
                        $reward = floor(strval($reward));
                    }else if(($fin_diff - $ten_diff) > 0){
                        $normaly_worktime = floor(strval(($in_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = floor(strval(($out_diff-$fin_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $midnight_salary + $normaly_salary;
                        $reward = floor(strval($reward));
                    }else if(($out_diff - $ten_diff) > 0){
                        $normaly_worktime = floor(strval(($ten_diff-$fin_diff+$in_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = floor(strval(($out_diff-$ten_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $midnight_salary + $normaly_salary;
                        $reward = floor(strval($reward));
                    }else if(($ten_diff - $out_diff) >= 0){
                        $normaly_worktime = floor(strval(($out_diff-$fin_diff+$in_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = 0;
                        $midnight_salary = 0;
                        $reward = $normaly_salary;
                        $reward = floor(strval($reward));
                    }
                }else if($in_flg == '0'){
                    //給与計算
                    if(($go_diff - $ten_diff) > 0){
                        $normaly_worktime = 0;
                        $normaly_salary = 0;
                        $midnight_worktime = floor(strval(($out_diff-$go_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $midnight_salary;
                        $reward = floor(strval($reward));
                    }else if(($out_diff - $ten_diff) > 0){
                        $normaly_worktime = floor(strval(($ten_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = floor(strval(($out_diff-$ten_diff)/60));
                        $midnight_salary = $midnight_worktime*$min_mid;
                        $reward = $normaly_salary + $midnight_salary;
                        $reward = floor(strval($reward));
                    }else if(($ten_diff - $out_diff) >= 0){
                        $normaly_worktime = floor(strval(($out_diff-$go_diff)/60));
                        $normaly_salary = $normaly_worktime*$min;
                        $midnight_worktime = 0;
                        $midnight_salary = 0;
                        $reward = $normaly_salary;
                        $reward = floor(strval($reward));
                    }
                }
                $normaly_worktimes += $normaly_worktime;
                $normaly_workhours = floor(strval($normaly_worktimes/60));
                $normaly_workminutes = '0' . $normaly_worktimes % 60;
                $normaly_workminutes = substr( $normaly_workminutes, -2);
                $normaly_worktimes_edit = $normaly_workhours . ':' . $normaly_workminutes;

                $midnight_worktimes += $midnight_worktime;
                $midnight_workhours = floor(strval($midnight_worktimes/60));
                $midnight_workminutes = '0' . $midnight_worktimes % 60;
                $midnight_workminutes = substr( $midnight_workminutes, -2);
                $midnight_worktimes_edit = $midnight_workhours . ':' . $midnight_workminutes;

                $normaly_salaries += $normaly_salary;
                $midnight_salaries += $midnight_salary;

                $info = [
                    $memberid,
                    $normaly_worktimes_edit,
                    $midnight_worktimes_edit,
                    floor(strval($normaly_salaries)),
                    floor(strval($midnight_salaries)),
                    $expenses += $expense,
                    $rewards += $reward,
                    $total = $rewards + $expenses*0.7,
                ];
            }
            //レコードの挿入
            $is_timerecord = TimeRecord::where('memberid',$memberid)->whereBetween('created_at',[$this21th,$this25th])->exists();
            if( $is_timerecord == false ){
                $timerecord = TimeRecord::where('memberid',$memberid)->delete();
                $timerecord = new TimeRecord;
                $timerecord->memberid = $info[0];
                $timerecord->go_days = count($stamps);
                $timerecord->normaly_worktime = $info[1];
                $timerecord->midnight_worktime = $info[2];
                $timerecord->normaly_salary = $info[3];
                $timerecord->midnight_salary = $info[4];
                $timerecord->expense = $info[5];
                $timerecord->salary = $info[6];
                $timerecord->fixed_salary = $info[7];
                $timerecord->save();
            }
        }
    }
}
