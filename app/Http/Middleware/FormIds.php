<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormIds
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $formIds=$request->header('formIds','');
        $openid=$request->header('openid','');
        if($formIds) {
            $formIds = @json_decode($formIds, true);
        }
        if(!$formIds || !$openid) return $next($request);
        try{
            $data=[];
            $uid=Auth::id();
            $ctime=date('Y-m-d H:i:s',time());
            foreach ($formIds as $formId=>$etime){
                array_push($data,[
                    'uid'=>$uid,
                    'openid'=>$openid,
                    'etime'=>$etime+6.5*86400,
                    'formid'=>$formId,
                    'created_at'=>$ctime,
                    'updated_at'=>$ctime,
                ]);
            }
            DB::table('wx_users_formid')->insert($data);
            header('formIds: clean');
        }catch (\Exception $e){
            Log::error('middleware-FormIds',$e);
        }
        return $next($request);
    }
}
