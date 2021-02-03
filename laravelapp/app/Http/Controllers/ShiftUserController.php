<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\CreateRecord;
use App\ShiftRecord;

class ShiftUserController extends Controller
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
        return view('staff.shift-record',['data'=>$data]);
    }

    public function add(){
        date_default_timezone_set('Asia/Tokyo');
        $todate = date('j');
        //取得するデータの期間
        $sixm_ago = date("Y-m-01 00:00:00",strtotime("-6 month"));
        $sixm_later = date("Y-m-t 23:59:59",strtotime("+6 month"));
        if(Auth::check()){
            $memberid = Auth::id();
            //全データの取得
            $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$sixm_ago,$sixm_later])
            ->where(function($query){
                $query->orWhere('is_post', '0')->orWhere('is_post', '1');
            })->exists();
            if( $is_createrecords == true){
                $createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$sixm_ago,$sixm_later])
                ->where(function($query){
                    $query->orWhere('is_post', '0')->orWhere('is_post', '1');
                })->get();
                foreach( $createrecords as $createrecord){
                    $date = substr($createrecord->go_time, 0, 10);
                    $data[] = [
                        $date,
                        $createrecord->go_time,
                        $createrecord->out_time,
                        $createrecord->shift_createid,
                    ];
                }
            }else if( $is_createrecords == false){
                $data = null;
            }
            if( $todate < 18 && $todate > 2 ){
                //来月の1日から15日までのデータの提出フラグに関する情報
                $next1th = date("Y-m-01 00:00:00",strtotime("+1 month"));
                $next15th = date("Y-m-15 23:59:59",strtotime("+1 month"));
                $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next1th,$next15th])
                ->exists();
                if( $is_createrecords == true){
                    $createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next1th,$next15th])
                    ->get();
                    foreach( $createrecords as $createrecord){
                        $info[] = $createrecord->is_post;
                    }
                    $info = array_product($info);
                }else if( $is_createrecords == false ){
                    $info = null;
                }
            }else if( $todate > 17){
                //来月の16日から末日までのデータの提出フラグに関する情報
                $next16th = date("Y-m-16 00:00:00",strtotime("+1 month"));
                $next31th = date("Y-m-t 23:59:59",strtotime("+1 month"));
                $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next16th,$next31th])
                ->exists();
                if( $is_createrecords == true){
                    $createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next16th,$next31th])
                    ->get();
                    foreach( $createrecords as $createrecord){
                        $info[] = $createrecord->is_post;
                    }
                    $info = array_product($info);
                }else if( $is_createrecords == false ){
                    $info = null;
                }
            }else if($todate < 3){
                $this16th = date("Y-m-16 00:00:00");
                $this31th = date("Y-m-t 23:59:59");
                $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$this16th,$this31th])
                ->exists();
                if( $is_createrecords == true){
                    $createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$this16th,$this31th])
                    ->get();
                    foreach( $createrecords as $createrecord){
                        $info[] = $createrecord->is_post;
                    }
                    $info = array_product($info);
                }else if( $is_createrecords == false ){
                    $info = null;
                }
            }
            return view('staff.shift-create',['memberid'=>$memberid,'data'=>$data,'info'=>$info]);
        }else{
            return redirect ('/login');
        }
    }

    public function create(Request $request){
        date_default_timezone_set('Asia/Tokyo');
        $todate = date('j');
        $form = $request->all();
        if(isset($form['create-btn']) && $form['create-btn'] == "登録"){
            //登録ボタン押下
            unset($form['_token']);
            unset($form['create-btn']);
            unset($form['shift_createid']);
            $form = array_merge( $form, array('is_post' => 0));
            $form['go_time'] = $form['date-info'] . "\n" . $form['go_time'];
            $form['out_time'] = $form['date-info'] . "\n" . $form['out_time'];
            $go_timestamp = strtotime($form['go_time']);
            $out_timestamp = strtotime($form['out_time']);
            if($out_timestamp - $go_timestamp <= 0){
                $date_info = date("Y-m-d",strtotime(date($form['date-info']) . "+1 day"));
                $form['out_time'] = substr_replace($form['out_time'], $date_info, 0, 10);
            }
            unset($form['date-info']);
            $createrecord = new CreateRecord;
            $createrecord->fill($form)->save();
        }else if(isset($form['create-btn']) && $form['create-btn'] == "解除"){
            //解除ボタン押下
            $createrecord = CreateRecord::where('shift_createid',$form['shift_createid'])->delete();
        }else if(isset($form['post-btn']) && $form['post-btn'] == "提出"){
            //提出ボタン押下
            if(Auth::check()){
                $memberid = Auth::id();
                if($todate < 18 && $todate >2){
                    //来月の1日から15日までのデータをとってくる
                    $next1th = date("Y-m-01 00:00:00",strtotime("+1 month"));
                    $next15th = date("Y-m-15 23:59:59",strtotime("+1 month"));
                    $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next1th,$next15th])
                    ->where('is_post','0')->exists();
                    if( $is_createrecords == true){
                        //シフトを出すとき
                        CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next1th,$next15th])->where('is_post','2')
                        ->delete();
                        CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next1th,$next15th])->where('is_post','0')
                        ->update([
                            'is_post' => '1',
                        ]);
                    }else if( $is_createrecords == false){
                        //シフトを出さないとき
                        $createrecord = new CreateRecord;
                        $createrecord->memberid = $memberid;
                        $createrecord->go_time = date("Y-m-d H:i:s",strtotime(date("Y-m-01 12:00:00") . "+1 month"));
                        $createrecord->out_time = date("Y-m-d H:i:s",strtotime(date("Y-m-01 20:30:00") . "+1 month"));
                        $createrecord->is_register = null;
                        $createrecord->is_post = '2';
                        $createrecord->save();
                    }
                }else if( $todate > 17 ){
                    //来月の16日から末日までのデータをとってくる
                    $next16th = date("Y-m-16 00:00:00",strtotime("+1 month"));
                    $next31th = date("Y-m-t 23:59:59",strtotime("+1 month"));
                    $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next16th,$next31th])
                    ->where('is_post','0')->exists();
                    if( $is_createrecords == true){
                        //シフトを出すとき
                        CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next16th,$next31th])->where('is_post','2')
                        ->delete();
                        CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$next16th,$next31th])->where('is_post','0')
                        ->update([
                            'is_post' => '1',
                        ]);
                    }else if( $is_createrecords == false){
                        //シフトを出さないとき
                        $createrecord = new CreateRecord;
                        $createrecord->memberid = $memberid;
                        $createrecord->go_time = date("Y-m-d H:i:s",strtotime(date("Y-m-16 12:00:00") . "+1 month"));
                        $createrecord->out_time = date("Y-m-d H:i:s",strtotime(date("Y-m-16 20:30:00") . "+1 month"));
                        $createrecord->is_register = null;
                        $createrecord->is_post = '2';
                        $createrecord->save();
                    }
                }else{
                    //当月の16日から末日までのデータをとってくる
                    $this16th = date("Y-m-16 00:00:00");
                    $this31th = date("Y-m-t 23:59:59");
                    $is_createrecords = CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$this16th,$this31th])
                    ->where('is_post','0')->exists();
                    if( $is_createrecords == true){
                        //シフトを出すとき
                        CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$this16th,$this31th])->where('is_post','2')
                        ->delete();
                        CreateRecord::where('memberid',$memberid)->whereBetween('go_time',[$this16th,$this31th])->where('is_post','0')
                        ->update([
                            'is_post' => '1',
                        ]);
                    }else if( $is_createrecords == false){
                        //シフトを出さないとき
                        $createrecord = new CreateRecord;
                        $createrecord->memberid = $memberid;
                        $createrecord->go_time = date("Y-m-16 12:00:00");
                        $createrecord->out_time = date("Y-m-16 20:30:00");
                        $createrecord->is_register = null;
                        $createrecord->is_post = '2';
                        $createrecord->save();
                    }
                }
            }else{
                return redirect ('/login');
            }
        }
        return redirect('/staff/shift-create');
    }
}