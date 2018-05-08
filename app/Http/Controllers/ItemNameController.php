<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ItemNames;
use App\Models\Wxuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class ItemNameController extends Controller
{
    public function show($name){
        $model=ItemNames::where('name','like','%'.$name.'%')->limit(10)->get();
        return $model;
    }
}
