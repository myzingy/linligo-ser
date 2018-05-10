<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    const STATUS_WCG=0;//未采购
    const STATUS_WCZ=1;//采购中
    const STATUS_CGW=2;//采购完，待分发

    const STATUS_FFZ=3;//分发中
    const STATUS_FFW=4;//分发完

    const STATUS_YCX=10;//已取消


    protected $table = 'activity';
    protected $fillable = ['uid', 'province', 'city','region','stime','etime'
        ,'goto','distribution','types','commission','status','slogan','latitude','longitude','address'];
    protected $casts = [
        'distribution' => 'array',
        'types'=>'array',
        'address'=>'array',
    ];
    public function wxuser(){
        return $this->hasOne(Wxuser::class,'uid','uid');
    }
    public function orders(){
        return $this->hasMany(ActivityOrders::class,'act_id','id');
    }
    public function items(){
        return $this->hasMany(ActivityOrdersItems::class,'act_id','id');
    }
    public function ordersCount($where=[]){
        return $this->hasMany(ActivityOrders::class,'act_id','id')
            ->where($where)
            ->count();
    }
    public function itemsCount($where=[]){
        return $this->hasMany(ActivityOrdersItems::class,'act_id','id')
            ->where($where)
            ->count();
    }
}
