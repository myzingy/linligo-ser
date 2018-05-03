<?php

/**
 * Created by PhpStorm.
 * User: goto9
 * Date: 2018/5/2
 * Time: 19:04
 */
namespace App\Observers;

use App\Models\Wxuser;
use App\User;
use Illuminate\Support\Facades\Hash;

class WxuserObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(Wxuser $wxuser)
    {
        //
        $user=User::create([
            'name' => $wxuser->userinfo['nickName'],
            'email' => $wxuser->openid,
            'password' => Hash::make($wxuser->openid),
            'api_token' =>str_random(64)
        ]);
        $wxuser->uid=$user->id;
        $wxuser->save();
    }
}