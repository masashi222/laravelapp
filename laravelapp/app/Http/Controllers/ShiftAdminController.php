<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CreateRecord;
use App\ShiftRecord;
use App\MemberRecord;

class ShiftAdminController extends Controller
{
    public function record(){
        date_default_timezone_set('Asia/Tokyo');
        $todate = date('j');
        $sixm_ago = date("Y-m-01 00:00:00",strtotime("-6 month"));
        $sixm_later = date("Y-m-t 23:59:59",strtotime("+6 month"));
        $is_shifts = ShiftRecord::whereBetween('go_time',[$sixm_ago,$sixm_later])->exists();
        if( $is_shifts == true){
            $shifts = ShiftRecord::whereBetween('go_time',[$sixm_ago,$sixm_later])->get();
            foreach($shifts as $shift){
                $date = substr($shift->go_time, 0, 10);
                $go_time = substr($shift->go_time, 11, 5);
                $out_time = substr($shift->out_time, 11, 5);
                $data[] = [
                    $shift->shift_createid,
                    $shift->memberid,
                    $shift->getName(),
                    $date,
                    $go_time,
                    $out_time,
                ];
            }
        }else{
            $data = null;
        }
        return view('admin.shift-record',['data'=>$data]);
    }

    public function show(){
        date_default_timezone_set('Asia/Tokyo');
        $todate = date('j');
        //当月の16日と末日
        $this16th = date("Y-m-16 00:00:00");
        $this31th = date("Y-m-t 23:59:59");
        $next1th = date("Y-m-1 00:00:00",strtotime("+1 month"));
        $next15th = date("Y-m-15 23:59:59",strtotime("+1 month"));
        //メンバー情報の取得
        $members = MemberRecord::where(function($query){
            $query->orWhere('business_no', '2')->orWhere('business_no', '3');
        })->get();
        if( $todate < 16){
            //3日から15日の間はcreate_recordsから当月の16日から末日をとってくる
            //シフトデータの初期値
            $data = null;
            $info = null;
            foreach( $members as $member){
                $memberid = $member->id;
                $name = $member->name;
                $members_data[] = [
                    $memberid,
                    $name,
                ];
                $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$this16th,$this31th])
                ->where(function($query){
                    $query->orWhere('is_post', '1')->orWhere('is_post', '2');
                })->exists();
                if( $is_createrecords == true){
                    //その期間のシフトデータの取得
                    $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$this16th,$this31th])
                    ->where('is_post','1')->exists();
                    if($is_createrecords == true){
                        //シフト提出済の人
                        $createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$this16th,$this31th])
                        ->where('is_post','1')->get();
                        foreach( $createrecords as $createrecord){
                            $date = substr($createrecord->go_time, 0, 10);
                            $go_time = substr($createrecord->go_time, 11);
                            $out_time = substr($createrecord->out_time, 11);
                            $data[] = [
                                $createrecord->shift_createid,
                                $createrecord->memberid,
                                $createrecord->getName(),
                                $date,
                                $go_time,
                                $out_time,
                                $createrecord->is_register,
                            ];
                        }
                    }
                }else if( $is_createrecords == false){
                    //シフト未提出のお知らせ
                    $info[] = $member->name;
                }
            }
            //確定ボタンの表示
            $is_createrecords = CreateRecord::whereBetween('go_time',[$this16th,$this31th])->where('is_register','1')
            ->orderBy('shift_createid','asc')->exists();
            $createrecords = CreateRecord::whereBetween('go_time',[$this16th,$this31th])->where('is_register','1')
            ->orderBy('shift_createid','asc')->get();
            $is_shifts = ShiftRecord::whereBetween('go_time',[$this16th,$this31th])->orderBy('shift_createid','asc')
            ->exists();
            $shifts = ShiftRecord::whereBetween('go_time',[$this16th,$this31th])->orderBy('shift_createid','asc')
            ->get();
            if($is_createrecords == true && $is_shifts == true && count($createrecords) == count($shifts)){
                $display = 2;
                for($i=0;$i<count($createrecords);$i++){
                    if($createrecords[$i]->memberid == $shifts[$i]->memberid && $createrecords[$i]->go_time == $shifts[$i]->go_time
                        && $createrecords[$i]->out_time == $shifts[$i]->out_time ){
                        continue;
                    }else{
                        $display = 1;
                        break;
                    }
                }
            }else if($is_createrecords == false && $is_shifts == false){
                $display = 0;
            }else{
                $display = 1;
            }
        }else{
            //18日から末日の間はcreate_recordsから翌月の1日から15日の分をとってくる
            //シフトデータの初期値
            $data = null;
            $info = null;
            foreach( $members as $member){
                $memberid = $member->id;
                $name = $member->name;
                $members_data[] = [
                    $memberid,
                    $name,
                ];
                $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next1th,$next15th])
                ->where(function($query){
                    $query->orWhere('is_post', '1')->orWhere('is_post', '2');
                })->exists();
                if( $is_createrecords == true){
                    //その期間のシフトデータの取得
                    $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next1th,$next15th])
                    ->where('is_post','1')->exists();
                    if($is_createrecords == true){
                        //シフト提出済の人
                        $createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next1th,$next15th])
                        ->where('is_post','1')->get();
                        foreach( $createrecords as $createrecord){
                            $date = substr($createrecord->go_time, 0, 10);
                            $go_time = substr($createrecord->go_time, 11);
                            $out_time = substr($createrecord->out_time, 11);
                            $data[] = [
                                $createrecord->shift_createid,
                                $createrecord->memberid,
                                $createrecord->getName(),
                                $date,
                                $go_time,
                                $out_time,
                                $createrecord->is_register,
                            ];
                        }
                    }
                }else if( $is_createrecords == false){
                    //シフト未提出のお知らせ
                    $info[] = $member->name;
                }
            }
            //確定ボタンの表示
            $is_createrecords = CreateRecord::whereBetween('go_time',[$next1th,$next15th])->where('is_register','1')
            ->orderBy('shift_createid','asc')->exists();
            $createrecords = CreateRecord::whereBetween('go_time',[$next1th,$next15th])->where('is_register','1')
            ->orderBy('shift_createid','asc')->get();
            $is_shifts = ShiftRecord::whereBetween('go_time',[$next1th,$next15th])->orderBy('shift_createid','asc')
            ->exists();
            $shifts = ShiftRecord::whereBetween('go_time',[$next1th,$next15th])->orderBy('shift_createid','asc')
            ->get();
            if($is_createrecords == true && $is_shifts == true && count($createrecords) == count($shifts)){
                $display = 2;
                for($i=0;$i<count($createrecords);$i++){
                    if($createrecords[$i]->memberid == $shifts[$i]->memberid && $createrecords[$i]->go_time == $shifts[$i]->go_time
                        && $createrecords[$i]->out_time == $shifts[$i]->out_time ){
                            continue;
                    }else{
                        $display = 1;
                        break;
                    }
                }
            }else if($is_createrecords == false && $is_shifts == false){
                $display = 0;
            }else{
                $display = 1;
            }
        }
        return view('admin.shift-create',['data'=>$data,'info'=>$info,'members_data'=>$members_data,'display'=>$display]);
    }

    public function show2(){
        date_default_timezone_set('Asia/Tokyo');
        $todate = date('j');
        //当月の16日と末日
        $this16th = date("Y-m-16 00:00:00");
        $this31th = date("Y-m-t 23:59:59");
        $next1th = date("Y-m-1 00:00:00",strtotime("+1 month"));
        $next15th = date("Y-m-15 23:59:59",strtotime("+1 month"));
        //メンバー情報の取得
        $members = MemberRecord::where(function($query){
            $query->orWhere('business_no', '2')->orWhere('business_no', '3');
        })->get();
        if($todate < 16){
            //3日から15日の間はcreate_recordsから当月の16日から末日をとってくる
            //シフトデータの初期値
            $data = null;
            $info = null;
            foreach( $members as $member){
                $memberid = $member->id;
                $name = $member->name;
                $members_data[] = [
                    $memberid,
                    $name,
                ];
                $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$this16th,$this31th])
                ->where(function($query){
                    $query->orWhere('is_post', '1')->orWhere('is_post', '2');
                })->exists();
                if( $is_createrecords == true){
                    //その期間のシフトデータの取得
                    $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$this16th,$this31th])
                    ->where('is_post','1')->exists();
                    if($is_createrecords == true){
                        //シフト提出済の人
                        $createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$this16th,$this31th])
                        ->where('is_post','1')->get();
                        foreach( $createrecords as $createrecord){
                            $date = substr($createrecord->go_time, 0, 10);
                            $go_time = substr($createrecord->go_time, 11);
                            $out_time = substr($createrecord->out_time, 11);
                            $data[] = [
                                $createrecord->shift_createid,
                                $createrecord->memberid,
                                $createrecord->getName(),
                                $date,
                                $go_time,
                                $out_time,
                                $createrecord->is_register,
                            ];
                        }
                    }
                }else if( $is_createrecords == false){
                    //シフト未提出のお知らせ
                    $info[] = $member->name;
                }
            }
            //確定ボタンの表示
            $is_createrecords = CreateRecord::whereBetween('go_time',[$this16th,$this31th])->where('is_register','1')
            ->orderBy('shift_createid','asc')->exists();
            $createrecords = CreateRecord::whereBetween('go_time',[$this16th,$this31th])->where('is_register','1')
            ->orderBy('shift_createid','asc')->get();
            $is_shifts = ShiftRecord::whereBetween('go_time',[$this16th,$this31th])->orderBy('shift_createid','asc')
            ->exists();
            $shifts = ShiftRecord::whereBetween('go_time',[$this16th,$this31th])->orderBy('shift_createid','asc')
            ->get();
            if($is_createrecords == true && $is_shifts == true && count($createrecords) == count($shifts)){
                $display = 2;
                for($i=0;$i<count($createrecords);$i++){
                    if($createrecords[$i]->memberid == $shifts[$i]->memberid && $createrecords[$i]->go_time == $shifts[$i]->go_time
                        && $createrecords[$i]->out_time == $shifts[$i]->out_time ){
                            continue;
                    }else{
                        $display = 1;
                        break;
                    }
                }
            }else if($is_createrecords == false && $is_shifts == false){
                $display = 0;
            }else{
                $display = 1;
            }
        }else{
            //18日から末日の間はcreate_recordsから翌月の1日から15日の分をとってくる
            //シフトデータの初期値
            $data = null;
            $info = null;
            foreach( $members as $member){
                $memberid = $member->id;
                $name = $member->name;
                $members_data[] = [
                    $memberid,
                    $name,
                ];
                $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next1th,$next15th])
                ->where(function($query){
                    $query->orWhere('is_post', '1')->orWhere('is_post', '2');
                })->exists();
                if( $is_createrecords == true){
                    //その期間のシフトデータの取得
                    $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next1th,$next15th])
                    ->where('is_post','1')->exists();
                    if($is_createrecords == true){
                        //シフト提出済の人
                        $createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next1th,$next15th])
                        ->where('is_post','1')->get();
                        foreach( $createrecords as $createrecord){
                            $date = substr($createrecord->go_time, 0, 10);
                            $go_time = substr($createrecord->go_time, 11);
                            $out_time = substr($createrecord->out_time, 11);
                            $data[] = [
                                $createrecord->shift_createid,
                                $createrecord->memberid,
                                $createrecord->getName(),
                                $date,
                                $go_time,
                                $out_time,
                                $createrecord->is_register,
                            ];
                        }
                    }
                }else if( $is_createrecords == false){
                    //シフト未提出のお知らせ
                    $info[] = $member->name;
                }
            }
            //確定ボタンの表示
            $is_createrecords = CreateRecord::whereBetween('go_time',[$next1th,$next15th])->where('is_register','1')
            ->orderBy('shift_createid','asc')->exists();
            $createrecords = CreateRecord::whereBetween('go_time',[$next1th,$next15th])->where('is_register','1')
            ->orderBy('shift_createid','asc')->get();
            $is_shifts = ShiftRecord::whereBetween('go_time',[$next1th,$next15th])->orderBy('shift_createid','asc')
            ->exists();
            $shifts = ShiftRecord::whereBetween('go_time',[$next1th,$next15th])->orderBy('shift_createid','asc')
            ->get();
            if($is_createrecords == true && $is_shifts == true && count($createrecords) == count($shifts)){
                $display = 2;
                for($i=0;$i<count($createrecords);$i++){
                    if($createrecords[$i]->memberid == $shifts[$i]->memberid && $createrecords[$i]->go_time == $shifts[$i]->go_time
                        && $createrecords[$i]->out_time == $shifts[$i]->out_time ){
                            continue;
                    }else{
                        $display = 1;
                        break;
                    }
                }
            }else if($is_createrecords == false && $is_shifts == false){
                $display = 0;
            }else{
                $display = 1;
            }
        }
        return view('admin.shift-create2',['data'=>$data,'info'=>$info,'members_data'=>$members_data,'display'=>$display]);
    }

    public function create(Request $request){
        date_default_timezone_set('Asia/Tokyo');
        $todate = date('j');
        $this16th = date("Y-m-16 00:00:00");
        $this31th = date("Y-m-t 23:59:59");
        $next1th = date("Y-m-1 00:00:00",strtotime("+1 month"));
        $next15th = date("Y-m-15 23:59:59",strtotime("+1 month"));
        $form = $request->all();
        unset($form['_token']);
        if( isset($form['register-btn'])){
            //登録ボタン押下
            if(isset($form['selectid'])){
                //選択
                for( $i=0; $i<count($form['shift_createid']); $i++){
                    if(in_array($form['shift_createid'][$i], $form['selectid'])){
                        //登録
                        $go_time = $form['date-info'][$i] . "\n" . $form['go_time'][$i];
                        $out_time = $form['date-info'][$i] . "\n" . $form['out_time'][$i];
                        $go_timestamp = strtotime($go_time);
                        $out_timestamp = strtotime($out_time);
                        if($out_timestamp - $go_timestamp <= 0){
                            $date_info = date("Y-m-d",strtotime(date($form['date-info'][$i]) . "+1 day"));
                            $out_time = substr_replace($out_time, $date_info, 0, 10);
                        }

                        CreateRecord::where('shift_createid',$form['shift_createid'][$i])->update([
                            'go_time' => $go_time,
                            'out_time' => $out_time,
                            'is_register' => '1',
                        ]);
                    }else{
                        //未登録
                        CreateRecord::where('shift_createid',$form['shift_createid'][$i])->update([
                            'is_register' => '0',
                        ]);
                    }
                }
            }else{
                //未選択
                for( $i=0; $i<count($form['shift_createid']); $i++){
                    //未登録
                    CreateRecord::where('shift_createid',$form['shift_createid'][$i])->update([
                        'is_register' => '0',
                    ]);
                }
            }
        }else if( isset($form['add-btn'])){
            //追加ボタン押下
            $go_time = $form['date-info'] . "\n" . '12:00:00';
            $out_time = $form['date-info'] . "\n" . '20:30:00';
            $is_post = '1';
            for($i=0; $i<count($form['staffid']); $i++){
                $memberid[$i] = $form['staffid'][$i];
                $createrecord = new CreateRecord;
                $createrecord->fill([
                    'memberid' => $memberid[$i],
                    'go_time' => $go_time,
                    'out_time' => $out_time,
                    'is_post' => $is_post,
                ])->save();
            }
        }else if( isset($form['confirm'])){
            if($todate <16){
                //当月の16~末日のデータをとってくる
                ShiftRecord::whereBetween('go_time',[$this16th,$this31th])->delete();
                $is_createrecords = CreateRecord::whereBetween('go_time',[$this16th,$this31th])->where('is_register','1')->exists();
                if($is_createrecords == true){
                    $createrecords = CreateRecord::whereBetween('go_time',[$this16th,$this31th])->where('is_register','1')->get();
                    foreach( $createrecords as $createrecord){
                        $shift = new ShiftRecord;
                        $shift->fill([
                            'shift_createid' => $createrecord->shift_createid,
                            'memberid' => $createrecord->memberid,
                            'go_time' => $createrecord->go_time,
                            'out_time' => $createrecord->out_time,
                        ])->save();
                    }
                }
            }else{
                //来月の1~15のデータをとってくる
                ShiftRecord::whereBetween('go_time',[$next1th,$next15th])->delete();
                $is_createrecords = CreateRecord::whereBetween('go_time',[$next1th,$next15th])->where('is_register','1')->exists();
                if($is_createrecords == true){
                    $createrecords = CreateRecord::whereBetween('go_time',[$next1th,$next15th])->where('is_register','1')->get();
                    foreach( $createrecords as $createrecord){
                        $shift = new ShiftRecord;
                        $shift->fill([
                            'shift_createid' => $createrecord->shift_createid,
                            'memberid' => $createrecord->memberid,
                            'go_time' => $createrecord->go_time,
                            'out_time' => $createrecord->out_time,
                        ])->save();
                    }
                }
            }
        }
        return redirect('/admin/shift-create');
    }

    public function create2(Request $request){
        date_default_timezone_set('Asia/Tokyo');
        $todate = date('j');
        $this16th = date("Y-m-16 00:00:00");
        $this31th = date("Y-m-t 23:59:59");
        $next1th = date("Y-m-1 00:00:00",strtotime("+1 month"));
        $next15th = date("Y-m-15 23:59:59",strtotime("+1 month"));
        $form = $request->all();
        unset($form['_token']);
        if( isset($form['register-btn'])){
            //登録ボタン押下
            if(isset($form['selectid'])){
                //選択
                for( $i=0; $i<count($form['shift_createid']); $i++){
                    if(in_array($form['shift_createid'][$i], $form['selectid'])){
                        //登録
                        $go_time = $form['date-info'][$i] . "\n" . $form['go_time'][$i];
                        $out_time = $form['date-info'][$i] . "\n" . $form['out_time'][$i];
                        $go_timestamp = strtotime($go_time);
                        $out_timestamp = strtotime($out_time);
                        if($out_timestamp - $go_timestamp <= 0){
                            $date_info = date("Y-m-d",strtotime(date($form['date-info'][$i]) . "+1 day"));
                            $out_time = substr_replace($out_time, $date_info, 0, 10);
                        }

                        CreateRecord::where('shift_createid',$form['shift_createid'][$i])->update([
                            'go_time' => $go_time,
                            'out_time' => $out_time,
                            'is_register' => '1',
                        ]);
                    }else{
                        //未登録
                        CreateRecord::where('shift_createid',$form['shift_createid'][$i])->update([
                            'is_register' => '0',
                        ]);
                    }
                }
            }else{
                //未選択
                for( $i=0; $i<count($form['shift_createid']); $i++){
                    //未登録
                    CreateRecord::where('shift_createid',$form['shift_createid'][$i])->update([
                        'is_register' => '0',
                    ]);
                }
            }
        }else if( isset($form['add-btn'])){
            //追加ボタン押下
            $go_time = $form['date-info'] . "\n" . '12:00:00';
            $out_time = $form['date-info'] . "\n" . '20:30:00';
            $is_post = '1';
            for($i=0; $i<count($form['staffid']); $i++){
                $memberid[$i] = $form['staffid'][$i];
                $createrecord = new CreateRecord;
                $createrecord->fill([
                    'memberid' => $memberid[$i],
                    'go_time' => $go_time,
                    'out_time' => $out_time,
                    'is_post' => $is_post,
                ])->save();
            }
        }else if( isset($form['confirm'])){
            if($todate <16){
                //当月の16~末日のデータをとってくる
                ShiftRecord::whereBetween('go_time',[$this16th,$this31th])->delete();
                $is_createrecords = CreateRecord::whereBetween('go_time',[$this16th,$this31th])->where('is_register','1')->exists();
                if($is_createrecords == true){
                    $createrecords = CreateRecord::whereBetween('go_time',[$this16th,$this31th])->where('is_register','1')->get();
                    foreach( $createrecords as $createrecord){
                        $shift = new ShiftRecord;
                        $shift->fill([
                            'shift_createid' => $createrecord->shift_createid,
                            'memberid' => $createrecord->memberid,
                            'go_time' => $createrecord->go_time,
                            'out_time' => $createrecord->out_time,
                        ])->save();
                    }
                }
            }else{
                //来月の1~15のデータをとってくる
                ShiftRecord::whereBetween('go_time',[$next1th,$next15th])->delete();
                $is_createrecords = CreateRecord::whereBetween('go_time',[$next1th,$next15th])->where('is_register','1')->exists();
                if($is_createrecords == true){
                    $createrecords = CreateRecord::whereBetween('go_time',[$next1th,$next15th])->where('is_register','1')->get();
                    foreach( $createrecords as $createrecord){
                        $shift = new ShiftRecord;
                        $shift->fill([
                            'shift_createid' => $createrecord->shift_createid,
                            'memberid' => $createrecord->memberid,
                            'go_time' => $createrecord->go_time,
                            'out_time' => $createrecord->out_time,
                        ])->save();
                    }
                }
            }
        }
        return redirect('/admin/shift-create2');
    }
}
