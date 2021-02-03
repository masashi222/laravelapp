<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberRecord extends Model
{
    protected $table = 'member_records';
    protected $increment = false;
    protected $guarded = array('id');
    protected $primarykey = 'id';

    public function business(){
        return $this->belongsTo('App\Business','business_no','business_no');
    }

    public function getBusiness(){
        return $this->business->business;
    }
}
