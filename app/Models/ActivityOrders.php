<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityOrders extends Model
{
    const STATUS_WCG=0;//未采购
    const STATUS_WCZ=1;//采购中
    const STATUS_CGW=2;//采购完，待分发

    const STATUS_FFZ=3;//分发中
    const STATUS_FFW=4;//分发完

    const STATUS_YCX=10;//已取消
    const STATUS_YJJ=11;//已拒绝

    protected $table = 'activity_orders';

    protected $fillable = ['uid','nickname', 'act_id', 'price','status','info','address','commission','distribution'];
    protected $casts = [
        'address' => 'array'
    ];
    public function wxuser(){
        return $this->hasOne(Wxuser::class,'uid','uid');
    }
    public function items(){
        return $this->hasMany(ActivityOrdersItems::class,'order_id','id');
    }
}
