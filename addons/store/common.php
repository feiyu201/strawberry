<?php

/**
 * 获取商品的分类及最底层ID
 *
 * @param $id
 * @return mixed
 */
function getCategoryNameId($id){
    if (empty($category['pid'])){
        return null;
    }
    $categoryId = \addons\store\model\GoodsCategoryJoin::where('goods_id',$id)->value('category_id');
    $category = \addons\store\model\GoodsCategory::where('id',$categoryId)->find();
    $title[] = $category['name'];
    $category['title'] = $category['name'];
    if ($category['pid'] != 0) {
        $title[] = \addons\store\model\GoodsCategory::where('id',$category['pid'])->value('name');
        $id_2 = \addons\store\model\GoodsCategory::where('id',$category['pid'])->value('pid');
        if ( $id_2 != 0) {
            $title[] = \addons\store\model\GoodsCategory::where('id',$id_2)->value('name');
        }
    }
    $title = array_reverse($title);
    $category['title'] = implode(' | ',$title);
    return $category;
}

/**
 * 获取规格基础表格
 *
 * @param $id
 * @return array|null
 */
function getSpec($id){
    $specType = \addons\store\model\GoodsSpecType::where('goods_id',$id)->select();
    $specTypeArr = [];
    foreach ($specType as $val) {
        $specTypeContent = json_decode($val['value'],true);
        $specTypeArr[] = [
            'title'=> $val['name'],
            'content'=> $specTypeContent
        ];
    }
    $specTypeArr = count($specTypeArr)>0?jsonToHtml($specTypeArr):null;
    return $specTypeArr;
}

/**
 * 写入规格数据（含规格图片）
 *
 * @param $data
 * @param $id
 */
function setSpecTypeSql($data,$id){
    foreach ($data as $val) {
        $type = \addons\store\model\GoodsSpecType::where('name',$val['name'])->where('goods_id',$id)->find();
        if($type != null) {
            $type->value = json_encode($val['value'], JSON_UNESCAPED_UNICODE);
            $type->save();
        } else{
            \addons\store\model\GoodsSpecType::create([
                'goods_id'=>$id,
                'value'=>json_encode($val['value'], JSON_UNESCAPED_UNICODE),
                'name'=>$val['name']
            ]);
        }
    }
}

/**
 * 写入规格基础数据与规格数值
 *
 * @param $pushItem
 * @param $id
 */
function setSpecValBaseSql($pushItem,$id){
    \addons\store\model\GoodsSpecBase::where('goods_id',$id)->delete();
    \addons\store\model\GoodsSpecValue::where('goods_id',$id)->delete();
    foreach ($pushItem as $val) {
        $val['goods_id'] = $id;

        $insetBase = \addons\store\model\GoodsSpecBase::create([
            'price'=> (float)$val['price'],
//                    'inventory'=> $val['inventory'],
            'weight'=> $val['weight'],
            'barcode'=> $val['barcode'],
            'coding'=> $val['coding'],
            'original_price'=> (float)$val['original_price'],
            'goods_id'=> $val['goods_id'],
        ]);
        foreach ($val['filed'] as $v){
            // 写入规格基础对应的规格值
            \addons\store\model\GoodsSpecValue::create([
                'goods_spec_base_id'=> $insetBase->id,
                'value'=> $v,
                'goods_id'=> $id
            ]);
        }
    }
}

/**
 * 获取规格价格表格
 *
 * @param $id
 * @return mixed|null
 */
function getSpecTable($id){
    $specTble = \addons\store\model\GoodsSpecValue::where('goods_id',$id)->group('goods_spec_base_id')->select()->toArray();
    foreach ($specTble as $k=>$v) {
        $specTble[$k]['spec'] = \addons\store\model\GoodsSpecValue::where('goods_spec_base_id',$v['goods_spec_base_id'])->field('value')->select();
        $specTble[$k] =array_merge($specTble[$k],\addons\store\model\GoodsSpecBase::withoutField('id,goods_id,add_time')->find($v['goods_spec_base_id'])->toArray());
    }
    $specTble = count($specTble)>0?jsonToHtml($specTble):null;
    return $specTble;
}


function getSpecImages($id){
    $specImagesArr = \addons\store\model\GoodsSpecType::where('goods_id',$id)->select()->toArray();
    $imgArr = [];
    foreach ($specImagesArr as $k => $v) {
        $imgArray = json_decode($v['value'],true);
        foreach ($imgArray as $val) {
            $imgArr[] = [
                'name'=> $val['name'],
                'img' =>$val['images'],
            ];
        }
    }
    return $imgArr;
}

/**
 * json处理
 *
 * @param $str
 * @return string|string[]
 */
function escapeQuotes($str) {
    if ($str && is_string($str)) {
        $str = str_replace("'",  "\u0027", $str);
        $str = str_replace('"',  "\u0022", $str);
    }
    return $str;
}

/**
 * json处理
 *
 * @param $data
 * @return string|string[]
 */
function jsonToHtml($data){
    return escapeQuotes(json_encode($data));
}