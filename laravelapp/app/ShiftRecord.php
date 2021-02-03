<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShiftRecord extends Model
{
    protected $table = 'shift_records';
    protected $increment = false;
    protected $guarded = array('shift_recordid');
    protected $primarykey = 'shift_recordid';

    public function member(){
        return $this->belongsTo('App\MemberRecord','memberid','id');
    }

    public function getName(){
        return $this->member->name;
    }

}
