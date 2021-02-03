<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StampRecord extends Model
{
    protected $table = 'stamp_records';
    protected $increment = false;
    protected $guarded = array('time_stampid');
    protected $primarykey = 'time_stampid';

    public function member(){
        return $this->belongsTo('App\MemberRecord','memberid','id');
    }

    public function getSalary(){
        return $this->member->salary;
    }

    public function getExpense(){
        return $this->member->expense;
    }

    public function getName(){
        return $this->member->name;
    }
}
