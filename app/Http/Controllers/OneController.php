<?php

namespace App\Http\Controllers;


class OneController extends Controller
{
    public function footData(){
        $xc=file_get_contents('./xc.txt');
        preg_match_all("/(.*)---(.*)[\r\n]/",$xc,$match);
        $tabs=[];
        $list=[];
        $ror=[];
        foreach ($match[2] as $i=>$rows_str){
            $local=trim($match[1][$i]);
            array_push($tabs,[
                'name'=>$local,'checkNum'=>0
            ]);
            $rows=explode('、',$rows_str);

            foreach ($rows as $ri=>$name){
                $name=trim($name);
                if(empty($ror[$name][$local])){
                    $ror[$name][$local]=10;
                }else{
                    $ror[$name][$local]+=10;
                }
                $rows[$ri]=[
                    'name'=>$name,'checked'=>false
                ];
            }
            array_push($list,$rows);
        }
        $data=[
            'tabs'=>$match[1],
            'list'=>$list,
            'ror'=>$ror,
        ];
        return $data;
    }
    public function footDataSG(){
        $sg=file_get_contents('./sg.txt');
        preg_match_all("/(.*)\t(.*)[\r\n]/",$sg,$match);
        $sgdata=[];
        foreach ($match[1] as $i=>$name){
            $local=trim(strtr($match[2][$i],array(
                '市'=>'',
                '新疆维'=>'新疆',
                '广西壮'=>'广西',
                '宁夏回'=>'宁夏',
                '西藏自'=>'西藏'
            )));
            $name=trim($name);
            if(empty($sgdata[$name][$local])){
                $sgdata[$name][$local]=10;
            }else{
                $sgdata[$name][$local]+=10;
            }

        }
        $sgname=array_keys($sgdata);
        $sg001=['苹果','梨','桃','西瓜','香蕉','桔子','橙子',];
        $sg999=['白杏','崩瓜','番荔枝/释迦果','长枣','沙果','李广杏','毛葡萄','姑娘果'];
        $data=[
            'tabs'=>[
                ['name'=>'日常水果','checkNum'=>0],
                ['name'=>'全部水果','checkNum'=>0],
                ['name'=>'冷门水果','checkNum'=>0],
            ],
            'list'=>[
                $sg001,
                array_values(array_diff($sgname,$sg999,$sg001)),
                $sg999
            ],
            'ror'=>$sgdata,
        ];
        foreach ($data['list'] as &$items){
            foreach ($items as &$item){
                $item=['name'=>$item,'checked'=>false];
            }
        }
        return $data;
        //die(json_encode($data,JSON_UNESCAPED_UNICODE));
    }
}
