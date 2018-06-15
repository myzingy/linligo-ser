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
                ['name'=>'花叶蔬菜','checkNum'=>0],
                ['name'=>'果干蔬菜','checkNum'=>0],
                ['name'=>'蘑菇菌类','checkNum'=>0],
                ['name'=>'猪肉猪骨','checkNum'=>0],
                ['name'=>'牛羊肉骨','checkNum'=>0],
                //['name'=>'鲜活水产','checkNum'=>0],
            ],
            'list'=>[
                explode('、','西瓜、甜瓜、梨瓜、哈密瓜-花皮、哈密瓜-黄皮、苹果-红富士、苹果-花牛、苹果-早熟、梨-酥梨、梨-啤梨、梨-皇冠、梨-香梨、桃-水蜜桃、桃-硬白桃、桃-油桃'),
                explode('、','菠萝、橙子、黑布林、火龙果-白肉、火龙果-红肉、荔枝、荔枝-妃子笑、芒果-大台芒、芒果-小台芒、猕猴桃、木瓜、奇异果、柠檬、牛油果、葡萄-黑色、葡萄-绿色、圣女果、圣女果-小、提子-黑提、提子-红提、提子-青提、香蕉、杨梅、樱桃-小、樱桃-大、车厘子、桔子、芦柑、蜜桔、冰糖橘、柚子、蜜柚、红心柚'),
                explode('、','香菜、大蒜、生姜、小葱、大葱、蒜苗、韭菜、茴香、辣椒-螺丝椒、辣椒-青尖椒、辣椒-青圆椒、辣椒-线椒、辣椒-小米椒'),
                explode('、','菜花、菜花-松花菜、矮脚油菜、白菜、包菜-包心菜、包菜-卷心菜、包菜-紫甘蓝、菠菜、大青菜、红苋菜、茴香、韭菜、韭黄、芹菜、芹菜-小香芹、青菜苔、生菜、生菜-花叶生菜、生菜-球生菜、娃娃菜、小白菜、油麦菜'),
                explode('、','冬瓜、佛手、荷兰豆、红薯、黄瓜、豇豆、苦瓜、莲藕、芦笋、萝卜-白萝卜、萝卜-胡萝卜、萝卜-水萝卜、南瓜-老南瓜、南瓜-青南瓜、南瓜-小金瓜、茄子-茄王、茄子-圆茄、茄子-长茄、秋葵、乳瓜、丝瓜、四季豆、蒜台、铁杆山药、土豆、莴笋、西葫芦、西红柿、西兰花、鲜竹笋、小豆芽、大豆芽、玉米、芋头、紫薯'),
                explode('、','白玉菇、海鲜菇、金针菇、鲜香菇、蟹味菇、杏鲍菇'),
                explode('、','猪棒骨、猪里脊、猪排骨、猪前腿、猪蹄、猪腿肉、猪五花肉'),
                explode('、','羊棒骨、羊后腿、羊排、羊前腿、羊腿肉、羊蝎子、牛棒骨、牛前腿、牛腱子、牛肋条、牛里脊、牛腩肉、牛腿肉'),
            ],
        ];
        foreach ($data['list'] as &$items){
            array_multisort($items);
            foreach ($items as &$item){
                $item=['name'=>$item,'checked'=>false];
            }

        }
        return $data;
    }
}
