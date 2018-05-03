<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Wxuser extends Model
{
    protected $table = 'wx_users';
    protected $primaryKey = 'openid';
    public $incrementing = false; //非自增
    protected $fillable = ['openid', 'unionid', 'userinfo','uid','source','session_key'];
    protected $casts = [
        'userinfo' => 'array',
    ];
    public function user(){
        return $this->hasOne(User::class,'id','uid');
    }
}
