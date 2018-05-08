<?php

/**
 * Created by PhpStorm.
 * User: goto9
 * Date: 2018/5/2
 * Time: 19:04
 */
namespace App\Observers;

use App\Models\ActivityOrdersItems;
use App\Models\ItemNames;

class ActivityOrdersItemsObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(ActivityOrdersItems $aoi)
    {
        //
        $name=trim($aoi->name);
        if(mb_strlen($name,'utf-8')>1){
            ItemNames::updateOrCreate([
                'name' => $name
            ],[
                'name' => $name
            ]);
        }

    }
}