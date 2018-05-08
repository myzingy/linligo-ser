<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityOrdersItems extends Model
{
    const STATUS_WCG=0;//未采购
    const STATUS_WCZ=1;//采购中
    const STATUS_CGW=2;//采购完，待分发

    const STATUS_FFZ=3;//分发中
    const STATUS_FFW=4;//分发完

    const STATUS_YCX=10;//已取消           //act,order,此状态用户不能再修改
    const STATUS_YJJ=11;//已拒绝，待用户修改 //order
    const STATUS_QUH=12;//缺货            //item

    protected $table = 'activity_orders_items';

    protected $fillable = ['order_id', 'act_id', 'price','status','name','weight'
        ,'weight_unit','actual_weight'];

    public function order(){
        return $this->hasOne(ActivityOrders::class,'id','order_id');
    }
}
