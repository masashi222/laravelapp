<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StaffRegisterRequest;
use App\MemberRecord;
use App\TimeRecord;
use App\StampRecord;
use App\CreateRecord;
use App\ShiftRecord;
use App\Business;
use Illuminate\Database\Eloquant\Scope;
use Illuminate\Database\Eloquant\Builder;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function record(){
        $members = MemberRecord::all();
        return view('admin.staff-record',['members' => $members]);
    }

    public function show($id='1'){
        $member = MemberRecord::where('id',$id)->first();
        return view('admin.staff-fix',['member' => $member]);
    }

    public function update(StaffRegisterRequest $request,$id='1'){
        $form = $request->all();
        if(isset($form['update'])) {
            $member = MemberRecord::where('id',$request->id);
            $form['pass']= Hash::make($form['pass']);
            $member->update([
                'number' => $form['number'],
                'name' => $form['name'],
                'pass' => $form['pass'],
                'expense' => $form['expense'],
                'salary' => $form['salary'],
                'business_no' => $form['business_no'],
            ]);
            return redirect ('/admin/staff-record');
        } else if(isset($form['delete'])) {
            StampRecord::where('memberid',$request->id)->delete();
            TimeRecord::where('memberid',$request->id)->delete();
            CreateRecord::where('memberid',$request->id)->delete();
            ShiftRecord::where('memberid',$request->id)->delete();
            MemberRecord::where('id',$request->id)->delete();
            return redirect ('/admin/staff-record');
        }
    }

    public function add(){
        return view('admin.staff-register');
    }

    public function create(StaffRegisterRequest $request){
        $form = $request->all();
        unset($form['_token']);
        $form['pass']=Hash::make($form['pass']);
        $member = new MemberRecord;
        $member->fill($form)->save();
        return redirect ('/admin/staff-record');
    }
}
