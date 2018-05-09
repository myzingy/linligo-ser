<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Wxuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ActivityController extends Controller
{
    public function store(){
        $post=Input::all();
        $post['uid']=Auth::id();
        $date=date('Y-m-d ',time()+($post['dateIndex']==1?86400:0));
        $post['stime']=$date.$post['stime'].':00';
        $post['etime']=$date.$post['etime'].':00';
        $post['latitude']=$post['location']['latitude'];
        $post['longitude']=$post['location']['longitude'];
        if($post['latitude']){
            $url=getMapUri([
                'location'=>$post['latitude'].','.$post['longitude'],
                'get_poi'=>0
            ]);
            $jsonStr=http($url);
            if($jsonStr){
                $json=json_decode($jsonStr,true);
                if(!empty($json['result']['address_component'])){
                    $addr=$json['result']['address_component'];
                    $post['province']=$addr['province'];
                    $post['city']=$addr['city'];
                }
            }
        }
        return Activity::create($post);
    }
    public function show($id){
        $model=Activity::with(['wxuser'])->find($id);
        $model['hasMyItems']=$model->itemsCount([
            'uid'=>Auth::id()
        ]);
        return $model;
    }
    public function index(){
        $type=Input::get('type');
        if($type){
            $uid=Auth::id();
            if($type==1){
                $list=Activity::where(['uid'=>$uid])->orderBy('id','desc')->limit(20)->get();
                return $list;
            }else{
                $list=Activity::where(['AOI.uid'=>$uid])
                    ->select(DB::raw('distinct activity.id,activity.*'))
                    ->join('activity_orders_items as AOI','AOI.act_id','=','activity.id')
                    ->orderBy('activity.id','desc')
                    ->limit(20)->get();
                return $list;
            }
        }
        throw new \Error('参数异常');
    }
}
