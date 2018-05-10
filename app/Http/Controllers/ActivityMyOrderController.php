<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityOrders;
use App\Models\ActivityOrdersItems;
use App\Models\Wxuser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class ActivityMyOrderController extends Controller
{
    public function store(){
        $post=Input::all();
        $where=[
            'uid'=>Auth::id(),
            'act_id'=>$post['act_id'],
        ];
        $actModel=ActivityOrders::where($where)->first();
        $where['commission']=$post['commission']*100;
        $where['distribution']=$post['distribution'];
        $where['address']=empty($post['address'])?"":$post['address'];
        $where['status']=ActivityOrdersItems::STATUS_WCG;
        $where['nickname']=Auth::user()->name;
        if(!$actModel){
            $actModel=ActivityOrders::create($where);
        }else{
            if(ActivityOrdersItems::STATUS_YJJ==$actModel->status
                || ActivityOrdersItems::STATUS_WCG==$actModel->status){
                $actModel->update($where);
            }
        }

        if(!empty($post['sub_type']) && $post['sub_type']=='orderSetting') return $actModel;
        ActivityOrdersItems::create([
            'act_id'=>$post['act_id'],
            'uid'=>Auth::id(),
            'nickname'=>Auth::user()->name,
            'order_id'=>$actModel->id,
            'name'=>$post['name'],
            'weight'=>$post['weight']*10,
            'weight_unit'=>$post['weight_unit'],
            'act_id'=>$post['act_id'],
            'status'=>ActivityOrdersItems::STATUS_WCG,
        ]);
        return $this->show($post['act_id']);
    }
    public function destroy($act_id){
        $itemMod=ActivityOrdersItems::find($act_id);
        if($itemMod->uid==Auth::id()){
            $act_id=$itemMod->act_id;
            $itemMod->delete();
            return $this->show($act_id);
        }
        throw new \Error("操作失败");
    }
    public function show($act_id){
        $model=Activity::with(['wxuser','orders'=>function($query){
            $query->where([
                'uid'=>Auth::id()
            ]);
        }])->find($act_id);
        foreach ($model->orders as &$order){
            $order->commission=$order->commission/100;
            $order->items=$order->items()->orderBy('id','desc')->get();
        }
        return $model;
    }
}
