<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreateRecord extends Model
{
    protected $table = 'create_records';
    protected $increment = false;
    protected $guarded = array('shift_createid');
    protected $primarykey = 'shift_createid';

    public function member(){
        return $this->belongsTo('App\MemberRecord','memberid','id');
    }

    public function getName(){
        return $this->member->name;
    }
}
