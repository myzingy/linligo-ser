<?php

namespace App\Http\Controllers;

use App\Models\Wxuser;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Input;

class WxuserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'api_token' =>str_random(64)
        ]);
    }
    public function miniLogin(){
        $appid=config('wechat.appid');
        $secret=config('wechat.secret');
        $js_code=Input::get('code');
        $url=<<<END
https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$js_code&grant_type=authorization_code
END;
        $obj=json_decode(file_get_contents($url));
        $data=[];
        $this->decryptData(Input::get('encryptedData'),Input::get('iv'),$obj->session_key,$data);
        if($data){
            $res=Wxuser::updateOrCreate([
                'openid'=>$data['openId'],
            ],[
                'unionid'=>!empty($data['unionId'])?$data['unionId']:$data['openId'],
                'userinfo'=>$data,
                'source'=>'wxlite',
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
        return ['code'=>200];
    }
}
