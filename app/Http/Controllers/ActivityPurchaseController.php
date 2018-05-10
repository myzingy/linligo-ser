<?php

namespace App\Http\Controllers;

use App\Models\Activity;
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
        $actModel=Activity::where([
            'uid'=>Auth::id(),
            'id'=>$act_id
        ])->with(['items'])->first();
        if(!$actModel) throw new \Error('参数异常');
        dd($actModel);

    }
}
