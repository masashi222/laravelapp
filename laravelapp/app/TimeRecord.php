<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeRecord extends Model
{
    protected $table = 'time_records';
    protected $increment = false;
    protected $guarded = array('time_recordid');
    protected $primarykey = 'time_recordid';

    public function member(){
        return $this->belongsTo('App\MemberRecord','memberid','id');
    }

    public function getNumber(){
        return $this->member->number;
    }

    public function getName(){
        return $this->member->name;
    }
}
