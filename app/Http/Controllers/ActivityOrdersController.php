<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityOrders;
use App\Models\ActivityOrdersItems;
use App\Models\Wxuser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ActivityOrdersController extends Controller
{
    public function show($act_id){
        $model=ActivityOrders::with(['wxuser','items'])
            ->where(['act_id'=>$act_id])
            ->where('status','<>',ActivityOrdersItems::STATUS_YJJ)
            ->get();
        return $model;
    }
    public function update($order_id){
        $model=ActivityOrders::find($order_id);
        if($model){
            DB::beginTransaction();
            try{
                $model->status=Input::get('status');
                $model->info=Input::get('info','');
                $model->save();
                ActivityOrdersItems::where(['order_id'=>$order_id,'status'=>ActivityOrdersItems::STATUS_CGW])
                    ->update(['status'=>Input::get('status')]);
                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
                throw new \Error('操作失败，请重试');
            }

        }
        return $this->show($model->act_id);
    }
}
