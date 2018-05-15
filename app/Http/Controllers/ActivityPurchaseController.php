<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityOrdersItems;
use App\Models\ActivityPurchase;
use App\Models\Wxuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ActivityPurchaseController extends Controller
{
    public function store()
    {
        $act_id = Input::get('act_id');
        $apModel=ActivityPurchase::where(['act_id'=>$act_id])->count();
        if($apModel>0){
            return ['act_id'=>$act_id];
        }
        $actModel=Activity::where([
            'uid'=>Auth::id(),
            'id'=>$act_id
        ])->with(['items'])->first();
        if(!$actModel) throw new \Error('参数异常');
        $data=[];
        $items=[];
        foreach ($actModel->items as $item){
            $name=trim($item->name);
            if(empty($data[$name])){
                $data[$name]=[
                    'act_id'=>$act_id,
                    'name'=>$name,
                    'status'=>ActivityOrdersItems::STATUS_WCG,
                    'items'=>[],
                    'created_at'=>date('Y-m-d H:i:s',time()),
                    'updated_at'=>date('Y-m-d H:i:s',time()),
                ];
            }
            if(empty($items[$name]['unit'][$item->weight_unit])){
                $items[$name]['unit'][$item->weight_unit]=0;
            }
            if(empty($items[$name]['user'][$item->uid])){
                $items[$name]['user'][$item->uid]=[];
            }
            $items[$name]['unit'][$item->weight_unit]+=$item->weight;
            $items[$name]['ids'][]=$item->id;
            array_push($items[$name]['user'][$item->uid],[
                'uid'=>$item->uid,
                'nickname'=>$item->nickname,
                'weight_unit'=>$item->weight_unit,
                'weight'=>$item->weight,
            ]);

            $data[$name]['items']=json_encode($items[$name]);
        }
        DB::table('activity_purchase')->insert($data);
        return ['act_id'=>$act_id,'data'=>$data];
    }
    public function show($act_id){
        return Activity::with(['purchases'=>function($query){
            $query->orderBy('name','asc');
        }])->find($act_id);
    }
    public function update(){
        $params=Input::all();
        $APM=ActivityPurchase::find($params['id']);
        if($APM->status!=ActivityOrdersItems::STATUS_WCG){
            throw new \Error('参数错误');
        }
        $APM->status=$params['status'];
        if($params['status']==ActivityOrdersItems::STATUS_CGW){
            $APM->weight=$params['weight']*10;
            $APM->price=$params['price']*100;
        }
        $APM->save();
        return $this->show($APM->act_id);
    }
}
