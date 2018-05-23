<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WxgroupsAct extends Model
{
    protected $table = 'wx_groups_acts';

    public $timestamps = false;

    protected $fillable = ['groupid','act_id'];

}
