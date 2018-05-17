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

class ActivityItemsController extends Controller
{
    public function update($item_id){
        $actual_weight=Input::get('actual_weight')*10;
        $model=ActivityOrdersItems::find($item_id);
        if($model && $actual_weight>0){
            $model->actual_weight=$actual_weight;
            $model->save();
            return 'ok';
        }
        throw new \Error('操作失败，请重试');
    }
}
