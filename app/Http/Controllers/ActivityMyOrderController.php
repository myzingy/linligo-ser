<?php

namespace App\Http\Controllers;

use App\Helper\Wxlite;
use App\Models\Activity;
use App\Models\ActivityOrders;
use App\Models\ActivityOrdersItems;
use App\Models\Wxuser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        $where['distribution']=$post['distribution']=='到他家取'?'来我家取':$post['distribution'];
        $where['address']=empty($post['address'])?"":$post['address'];
        $where['status']=ActivityOrdersItems::STATUS_WCG;
        $where['nickname']=Auth::user()->name;
        $newOrderFlag=false;
        if(!$actModel){
            $actModel=ActivityOrders::create($where);
            $newOrderFlag=true;
        }else{
            if(ActivityOrdersItems::STATUS_YJJ==$actModel->status
                || ActivityOrdersItems::STATUS_WCG==$actModel->status){
                $actModel->update($where);
                ActivityOrdersItems::where(['order_id'=>$actModel->id])->update([
                    'status'=>ActivityOrdersItems::STATUS_WCG
                ]);
            }
        }

        if(!empty($post['sub_type']) && $post['sub_type']=='orderSetting'){
            return $this->show($post['act_id']);
        }

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

        $noticeFlag=$this->_isCanNotice($actModel->id);
        if($noticeFlag){
            $wxlib=new Wxlite();
            $am=Activity::with('wxuser')->find($post['act_id']);
            $noticeData=[
                'keyword1'=>[
                    'value'=>$where['nickname'].'参加了'.$am->slogan,
                ],
                'keyword2'=>['value'=>$post['name']." ".$post['weight'].$post['weight_unit'].'...'],
                'keyword3'=>['value'=>$where['nickname']],
                'keyword4'=>['value'=>''],
                'keyword5'=>['value'=>$where['distribution']],
                'keyword6'=>['value'=>''],
            ];
            $page=str_replace('{id}',$post['act_id'],Wxlite::TPL_ACTIVITY_JOIN_PAGE);
            $wxlib->notice($am->wxuser->openid,Wxlite::TPL_ACTIVITY_JOIN
                ,$page,$noticeData);
        }
        return $this->show($post['act_id']);
    }
    private function _isCanNotice($id){
        $key='ActivityOrders-'.$id;
        $val=Cache::get($key);
        if($val){
            return false;
        }
        Cache::put($key, time(),15);
        return true;
    }
    public function destroy($act_id){
        $itemMod=ActivityOrdersItems::find($act_id);
        if($itemMod->status!=ActivityOrdersItems::STATUS_WCG){
            throw new \Error('活动当前状态不允许操作，请返回');
        }
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
    public function batchSet(){
        $uid=Auth::id();
        $nickname=Auth::user()->name;
        $where=[
            'id'=>Input::get('act_id'),
        ];
        $actModel=Activity::where($where)->first();
        if($actModel->status!=ActivityOrdersItems::STATUS_WCG){
            throw new \Error('活动当前状态不允许操作，请返回');
        }
        $base_query=[
            'uid'=>Auth::id(),
            'act_id'=>Input::get('act_id'),
            'order_id'=>Input::get('order_id'),
        ];
        DB::beginTransaction();
        try{
            ActivityOrdersItems::where($base_query)->delete();
            $ctime=date('Y-m-d H:i:s',time());
            $items=Input::get('items');
            $data=[];
            if($items){
                foreach ($items as $item){
                    array_push($data,array_merge($base_query,[
                        'nickname'=>$nickname,
                        'name'=>$item['name'],
                        'weight'=>$item['weight']*10,
                        'weight_unit'=>$item['weight_unit'],
                        'status'=>ActivityOrdersItems::STATUS_WCG,
                        'created_at'=>$ctime,
                        'updated_at'=>$ctime,
                    ]));
                }
                DB::table('activity_orders_items')->insert($data);
            }
            DB::commit();
        }catch (\Exception $e){
            throw new \Error($e->getMessage());
            DB::rollBack();
        }
    }
}
