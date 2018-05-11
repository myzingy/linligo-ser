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

class ActivityOrdersController extends Controller
{
    public function show($act_id){
        $model=ActivityOrders::with(['wxuser','items'])->where(['act_id'=>$act_id])->get();
        return $model;
    }
}
