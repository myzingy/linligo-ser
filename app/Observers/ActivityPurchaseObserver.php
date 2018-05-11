<?php

/**
 * Created by PhpStorm.
 * User: goto9
 * Date: 2018/5/2
 * Time: 19:04
 */
namespace App\Observers;

use App\Models\Activity;
use App\Models\ActivityOrders;
use App\Models\ActivityOrdersItems;
use App\Models\ActivityPurchase;
use App\Models\ItemNames;

class ActivityPurchaseObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(ActivityPurchase $aoi)
    {
        //
        $update=[];
        $item_ids=[];
        if($aoi->status==ActivityOrdersItems::STATUS_CGW){//采购完
            $update=[
                'status'=>ActivityOrdersItems::STATUS_CGW,
                'price'=>($aoi->price/($aoi->weight/10))
            ];
            $item_ids=$aoi->items['ids'];
        }
        if($aoi->status==ActivityOrdersItems::STATUS_QUH){//缺货
            $update=[
                'status'=>ActivityOrdersItems::STATUS_QUH,
                'price'=>0
            ];
            $item_ids=$aoi->items['ids'];
        }
        if($update && $item_ids){
            ActivityOrdersItems::whereIn('id',$item_ids)->update($update);
            $cc=ActivityPurchase::where(['act_id'=>$aoi->act_id])
                ->whereNotIn('status',[ActivityOrdersItems::STATUS_CGW,ActivityOrdersItems::STATUS_QUH])
                ->count();
            if($cc==0){
                Activity::where(['id'=>$aoi->act_id])->update(['status'=>ActivityOrdersItems::STATUS_CGW]);
                ActivityOrders::where(['act_id'=>$aoi->act_id])
                    ->update(['status'=>ActivityOrdersItems::STATUS_CGW]);
            }
        }
    }
}