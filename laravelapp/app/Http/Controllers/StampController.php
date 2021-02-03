<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StampRequest;
use App\MemberRecord;
use App\StampRecord;
use App\KeyRecord;
use Illuminate\Database\Eloquant\Scope;
use Illuminate\Database\Eloquant\Builder;
use Illuminate\Support\Facades\DB;

class StampController extends Controller
{
    public function show(){
        if(Auth::check()){
            $id = Auth::id();
            $member = MemberRecord::where('id',$id)->first();
            $memberid = $member->id;
            $stamp_2 = StampRecord::where('memberid',$memberid)->where('go_flg','1')->where('in_flg','0')->where('fin_flg','0')
            ->where('out_flg','0')->first();//打刻開始
            $stamp_3 = StampRecord::where('memberid',$memberid)->where('go_flg','1')->where('in_flg','1')->where('fin_flg','0')
            ->where('out_flg','0')->first();//休憩開始
            $stamp_4 = StampRecord::where('memberid',$memberid)->where('go_flg','1')->where('in_flg','1')->where('fin_flg','1')
            ->where('out_flg','0')->first();//休憩終了
            $stamp_1 = StampRecord::where('memberid',$memberid)->where('out_flg','0')->first();//打刻終了

            if(!isset($stamp_1)){
                $display = 1;
                $expense = null;
            }else if(isset($stamp_2)){
                $display = 2;
                $expense = $stamp_2->expense;
            }else if(isset($stamp_3)){
                $display = 3;
                $expense = $stamp_3->expense;
            }else if(isset($stamp_4)){
                $display = 4;
                $expense = $stamp_4->expense;
            }

            return view('staff.stamp',['member' => $member,'display' => $display,'expense' => $expense]);
        }else{
            return redirect ('/login');
        }
    }

    public function stamp(StampRequest $request){
        date_default_timezone_set('Asia/Tokyo');
//         $date = date("Y-m-d");
        $form = $request->all();
        if(isset($form['go_btn'])){
            //出勤押下
            $key = KeyRecord::first();
            if( isset($key)){
                if($form['key'] == $key->code){
                    $stamp = new StampRecord;
                    unset($form['_token']);
                    unset($form['go_btn']);
                    unset($form['key']);
                    if( $form['expense'] == null){
                        $form['expense'] = '0';
                    }
                    $stamp->fill($form)->save();
                }else{
                    return redirect ('/staff/stamp')->with('flash_message', '打刻キーが不正です');
                }
            }else{
                return redirect ('/staff/stamp')->with('flash_message', '本日の打刻キーは未発行です');
            }
        }else if(isset($form['breakIn_btn'])){
            //休憩入り押下
            $stamp = StampRecord::where('memberid',$request->memberid)->where('go_flg','1')->where('in_flg','0')->
            where('fin_flg','0')->where('out_flg','0');
            if( $form['expense'] == null){
                $form['expense'] = $stamp->first()->expense;
            }
            $stamp->update([
                'break_in' => $form['break_in'],
                'in_flg' => $form['in_flg'],
                'expense' => $form['expense'],
            ]);
        }else if(isset($form['breakOut_btn'])){
            //休憩終わり押下
            $stamp = StampRecord::where('memberid',$request->memberid)->where('go_flg','1')->where('in_flg','1')->
            where('fin_flg','0')->where('out_flg','0');
            if( $form['expense'] == null){
                $form['expense'] = $stamp->first()->expense;
            }
            $stamp->update([
                'break_out' => $form['break_out'],
                'fin_flg' => $form['fin_flg'],
                'expense' => $form['expense'],
            ]);
        }else if(isset($form['out_btn'])){
            //退勤押下
            $stamp_1 = StampRecord::where('memberid',$request->memberid)->where('go_flg','1')->where('in_flg','0')->
            where('fin_flg','0')->where('out_flg','0');//出勤→退勤
            $stamp_2 = StampRecord::where('memberid',$request->memberid)->where('go_flg','1')->where('in_flg','1')->
            where('fin_flg','1')->where('out_flg','0');//休憩→退勤
            $is_stamp_1 = $stamp_1->exists();
            $is_stamp_2 = $stamp_2->exists();
            if($is_stamp_1 == true){
                if( $form['expense'] == null){
                    $form['expense'] = $stamp_1->first()->expense;
                }
                $stamp_1->update([
                    'out_time' => $form['out_time'],
                    'out_flg' => $form['out_flg'],
                    'expense' => $form['expense'],
                ]);
            }else if($is_stamp_2 == true){
                if( $form['expense'] == null){
                    $form['expense'] = $stamp_2->first()->expense;
                }
                $stamp_2->update([
                    'out_time' => $form['out_time'],
                    'out_flg' => $form['out_flg'],
                    'expense' => $form['expense'],
                ]);
            }
        }
        return redirect ('/staff/stamp');
    }
}
