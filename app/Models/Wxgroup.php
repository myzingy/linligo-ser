<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wxgroup extends Model
{
    protected $table = 'wx_groups';
    protected $fillable = ['groupid','name','openid','uid'];
}
