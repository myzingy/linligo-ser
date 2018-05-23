<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WxuserFormid extends Model
{
    protected $table = 'wx_users_formid';

    public static function one($uid)
    {
        if(is_numeric($uid)){
            $where=['uid'=>$uid];
        }else{
            $where=['openid'=>$uid];
        }
        $one=self::where($where)->where("etime",">",time())
            ->orderBy('etime','asc')->first();
        $formid="";
        if($one){
            $formid=$one->formid;
            $one->delete();
        }
        return $formid;
    }
}
