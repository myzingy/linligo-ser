<?php

namespace App\Helper;

use App\Models\WxuserFormid;
use Illuminate\Support\Facades\Cache;

class Wxlite{
    const TOKEN_TIMEOUT=7200/60;
    const TOKEN_CACHE_NAME='wechat.access_token';

    const TPL_ACTIVITY_JOIN='-6aoS7QEeHnCNAW6uKFMqvSn6ZkDEgZsfwvaLv8aSlQ';         //加入活动
    const TPL_ACTIVITY_JOIN_PAGE='pages/purchase/info?id={id}';                   //加入活动

    const TPL_ACTIVITY_REFUSE='pvHB9-FWjtx59AEBIAeGIKHVaddZTWA08BI7FSIyzAY';       //拒绝加入
    const TPL_ACTIVITY_REFUSE_PAGE='pages/purchase/info?id={id}';                 //拒绝加入

    function __construct()
    {
        $this->appid=config('wechat.appid');
        $this->secret=config('wechat.secret');
        $this->access_token=$this->token();
    }
    function token(){
        $token=Cache::get(self::TOKEN_CACHE_NAME);
        if($token) return $token;
        $html=http("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".
            $this->appid."&secret=".$this->secret);
        if($html){
            $res=@json_decode($html,true);
            if(!empty($res['access_token'])){
                Cache::put(self::TOKEN_CACHE_NAME, $res['access_token'], self::TOKEN_TIMEOUT);
                return $res['access_token'];
            }
            return $this->token();
        }

    }

    /**
     * touser: '触发帐号的opened',
        template_id: '模版id',
        page: '点击模版卡片的跳转页面',
        form_id: 'form_id或者prepay_id',
        data: {
        keyword1:{
        value: '小程序测试模版',
        color: '#173177'
        },
        keyword2:{
        value: '2017年3月24日',
        color: '#173177'
        },
        keyword3:{
        value: 'iHleath',
        color: '#173177'
        }
        },
        //需要放大的关键字
        emphasis_keyword: 'keyword1.DATA'
     */
    function notice($touser,$template_id,$page,$data=[],$emphasis_keyword=''){
        $url="https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$this->access_token;
        $form_id=WxuserFormid::one($touser);
        if(!$form_id) return;
        $post=[
            'touser'=>$touser,
            'template_id'=>$template_id,
            'page'=>$page,
            'form_id'=>$form_id,
            'data'=>$data,
            'emphasis_keyword'=>$emphasis_keyword
        ];
        $res=$this->https_request($url,$post,'json');
    }
    function https_request($url,$data,$type){
        if($type=='json'){//json $_POST=json_decode(file_get_contents('php://input'), TRUE);
            $headers = array("Content-type: application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");
            $data=json_encode($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}