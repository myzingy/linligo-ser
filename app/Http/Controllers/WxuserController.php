<?php

namespace App\Http\Controllers;

use App\Models\Wxuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class WxuserController extends Controller
{
    public function miniLogin(){
        $appid=config('wechat.appid');
        $secret=config('wechat.secret');
        $js_code=Input::get('code');
        $url=<<<END
https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$js_code&grant_type=authorization_code
END;
        $obj=json_decode(file_get_contents($url));
        if(empty($obj->session_key)) throw new \Error($obj->errmsg);
        $data=[];
        $this->decryptData(Input::get('encryptedData'),Input::get('iv'),$obj->session_key,$data);
        if($data){
            $res=Wxuser::updateOrCreate([
                'openid'=>$data['openId'],
            ],[
                'unionid'=>!empty($data['unionId'])?$data['unionId']:$data['openId'],
                'userinfo'=>$data,
                'source'=>'wxlite',
                'session_key'=>$obj->session_key
            ]);
        }

        return ['userinfo'=>$data,'access_token'=>$res->user->api_token,'refresh_token'=>$res->user->api_token];
    }
    private function decryptData( $encryptedData, $iv, $sessionKey,&$data )
    {
        if (strlen($sessionKey) != 24) {
            return -1;
        }
        $aesKey=base64_decode($sessionKey);


        if (strlen($iv) != 24) {
            return -2;
        }
        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj=json_decode( $result );
        if( $dataObj  == NULL )
        {
            return -3;
        }
        $appid=config('wechat.appid');
        if( $dataObj->watermark->appid != $appid )
        {
            return -4;
        }
        $data = json_decode($result,true);
        return 0;
    }
    public function setPhoneNumber(){
        $wxuser=Wxuser::where(['uid'=>Auth::user()->id])->first();
        $data=[];
        $this->decryptData(Input::get('encryptedData'),Input::get('iv'),$wxuser->session_key,$data);
        if(!empty($data['phoneNumber'])){
            $wxuser->phone=$data['phoneNumber'];
            $wxuser->save();
            return $data;
        }
        return false;

    }
    public function setShareOpenid(){
        $share_openid=Input::get('share_openid');
        $wxuser=Wxuser::where(['uid'=>Auth::user()->id])->first();
        if($share_openid && !$wxuser->share_openid){
            $wxuser->share_openid=$share_openid;
            $wxuser->save();
            if($share_openid!=$wxuser->openid){
                $shuser=Wxuser::where(['openid'=>$share_openid])->with(['user'])->first();
                if(!empty($shuser->user)){
                    $red_packet=rand(10,100);
                    $shuser->user->red_packet+=$red_packet;
                    $shuser->user->save();
                }
            }
            return ['share_openid'=>$share_openid];
        }
        return ['share_openid'=>$share_openid];
    }
    public function show(){
        $shuser=Wxuser::where(['uid'=>Auth::user()->id])->with(['user'])->first();
        return $shuser;
    }
}
