<?php
/**
 * Created by PhpStorm.
 * User: goto9
 * Date: 2018/5/4
 * Time: 22:39
 */
function http($url,$post=""){
    if($post && is_array($post)){
        $query = http_build_query($post);
    }else{
        $query=$post;
    }
    $opts = array(
        'http'=>array(
            'method'=>$query?"POST":"GET",
            'timeout'=>30,
            //'header' => 'Content-type:application/x-www-form-urlencoded',
            'header' => 'Content-type:application/json',
            'content' => $query
        )
    );
    $context = stream_context_create($opts);
    $html =@file_get_contents($url, false, $context);
    return $html;
}
function getMapUri($querystring_arrays,$api='/ws/geocoder/v1',$method='GET'){
    $key='BSSBZ-4HIK6-LKVSF-ERVRJ-WMXYE-GYBZZ';
    $sk='JVXrFilfVjzHGaMvlLchgcJl7l56dVt3';
    $MAP_BASE='http://apis.map.qq.com';
    $MAP_GEOCODER_API=$api;
    if(empty($querystring_arrays['key'])){
        $querystring_arrays['key']=$key;
    }
    ksort($querystring_arrays);
    $querystring = http_build_query($querystring_arrays);
    $sn=md5(urlencode($MAP_GEOCODER_API.'?'. urldecode($querystring) . $sk));
    return $MAP_BASE.$MAP_GEOCODER_API.'?'.$querystring.'&sn='.$sn;
}