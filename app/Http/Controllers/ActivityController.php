<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Wxuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $model=Activity::with(['wxuser','orders'])->find($id);
        return $model;
    }
}
