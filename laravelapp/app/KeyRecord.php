<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeyRecord extends Model
{
    protected $table = 'key_records';
    protected $increment = false;
    protected $guarded = array('keyid');
    protected $primarykey = 'keyid';
}
