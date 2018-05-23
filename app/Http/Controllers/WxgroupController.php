<?php

namespace App\Http\Controllers;

use App\Models\Wxgroup;
use App\Models\WxgroupsAct;
use App\Models\WxgroupsUser;
use App\Models\Wxuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class WxgroupController extends Controller
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
    public function bind(){

        $wxuser=Wxuser::where(['uid'=>Auth::user()->id])->first();
        $data=[];
        $res=$this->decryptData(Input::get('share_res.encryptedData'),Input::get('share_res.iv'),$wxuser->session_key,$data);
        if($res!=0) return;
        $share_openid=Input::get('share_openid');
        $groupid=$data['openGId'];
        $Wxgroup=Wxgroup::where(['groupid'=>$groupid])->first();
        if(!$Wxgroup){
            $Wxgroup=Wxgroup::create([
                'groupid'=>$groupid,
                'openid'=>$share_openid,
            ]);
            $act_id=Input::get('act_id');
            if($act_id){
                WxgroupsAct::create([
                    'groupid'=>$groupid,
                    'act_id'=>$act_id
                ]);
            }
        }
        DB::beginTransaction();
        try{
            WxgroupsUser::create([
                'groupid'=>$groupid,
                'openid'=>$wxuser->openid,
                'uid'=>$wxuser->uid
            ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
        }
        return "ok";
    }
}
