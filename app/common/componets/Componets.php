<?php
namespace app\common\componets;

use think\template\TagLib;

class Componets extends TagLib
{
    protected $tags = [
        'uedit'=> ['attr'=>'name,content','close'=> 0]
    ];

    public function tagUedit($tag){
     return '这是编辑器组件,参数：name='.$tag['name'].';content='.$tag['content'];
    }

}