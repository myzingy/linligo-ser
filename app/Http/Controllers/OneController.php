<?php

namespace App\Http\Controllers;


class OneController extends Controller
{
    public function footData(){
        $xc=file_get_contents('./xc.txt');
        preg_match_all("/(.*)---(.*)[\r\n]*/",$xc,$match);
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
            'tabs'=>$tabs,
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
    function category(){
        $data=[
            'tabs'=>[
                ['name'=>'瓜果梨桃','checkNum'=>0],
                ['name'=>'新鲜水果','checkNum'=>0],
                ['name'=>'调味蔬菜','checkNum'=>0],
                ['name'=>'叶菜蔬菜','checkNum'=>0],
                ['name'=>'蘑菇菌类','checkNum'=>0],
                ['name'=>'新鲜蔬菜','checkNum'=>0],
                ['name'=>'大肉家禽','checkNum'=>0],
                //['name'=>'鲜活水产','checkNum'=>0],
            ],
            'list'=>[
                explode('、','西瓜、甜瓜、梨瓜、哈密瓜、红富士、青苹果、梨、毛桃、油桃、蟠桃'),
                explode('、','香蕉、桔子、芦柑、蜜桔、冰糖橘、圣女果、柚子、蜜柚、红心柚'),
                explode('、','香菜、蒜、姜、小葱、大葱、蒜苗、韭菜、茴香、青辣椒、小米椒、红辣椒、菜辣椒'),
                explode('、','小香芹、芹菜、白菜、包菜、空心菜、小白菜、青菜、油菜、油麦菜、菠菜、叶生菜、苦菊、西兰花、苦苣菜、茼蒿、紫甘蓝'),
                explode('、','香菇、金针菇、平菇、杏鲍菇、黑木耳、小木耳、蟹味菇、白玉菇、花菇'),
                explode('、','土豆、红薯、西红柿、黄瓜、鲜南瓜、老南瓜、胡萝卜、白萝卜、'),
                explode('、','牛肉、红薯、西红柿、黄瓜、鲜南瓜、老南瓜、胡萝卜、白萝卜'),
            ],
        ];
        foreach ($data['list'] as &$items){
            foreach ($items as &$item){
                $item=['name'=>$item,'checked'=>false];
            }
        }
        return $data;
    }
}
