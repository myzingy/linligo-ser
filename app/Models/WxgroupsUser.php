<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WxgroupsUser extends Model
{
    protected $table = 'wx_groups_users';

    public $timestamps = false;

    protected $fillable = ['groupid','openid','uid'];

}
