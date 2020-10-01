<?php

declare (strict_types = 1);

//------------------------
// Ric 助手函数
//-------------------------

if (!function_exists('getValidate')) {
    /**
     * 生成api验证规则
     */
    function getValidate($class,$fun, $data){
        $re  = new ReflectionClass($class);
        $code = $re->getMethod($fun)->getDocComment();
        $zs = comment2Array($code);
        $rule = [];
        $message = [];
        foreach ($zs as $val){
            if($val[0] == 'param' && isset($val[5])){
                $zkey = preg_replace('/\$/i', '', $val[2]);
                $str = explode('.',$val[5]);
                foreach ($str as $vkey => $vval){
                    $vstr = explode('_',$vval);
                    $rule[$zkey][] = $vstr[0];
                    if(isset($vstr[1])){
                        $message[$zkey.'.'.$vstr[0]] = $vstr[1];
                    }
                }
                $rule[$zkey] = implode('|',$rule[$zkey]);
            }
        }

        $result = validate($rule,$message)->failException(true)->check($data);
    }
}


if (!function_exists('api')) {
    /**
     * 提供api调用
     */
    function api($class, $fun, $data){
        try {
            getValidate($class, $fun, $data);
            $newclass = new $class;
            return $newclass->$fun($data);
        } catch (\Exception $e) {
            abort(-1, $e->getMessage());
        }
    }
}

if (!function_exists('rpc')) {
    /**
     * 提供类调用
     */
    function rpc($class, $fun, $data){
        try {
            getValidate($class, $fun, $data);
            return invoke([$class,$fun],$data);
        } catch (\Exception $e) {
            abort(-1, $e->getMessage());
        }
    }
}

if (!function_exists('toTrue')) {
    /**
     * 返回操作成功json信息
     * @param array $object 当前返回对象
     * @param string $special 特殊返回对象处理 有类型：select
     */
    function toTrue($object,$message=''){
        try {
            $data = tocode(100,$message);
            $data['data'] = $object;
            ob_clean();
            return json($data);
        } catch (\Exception $e) {
            abort(-1, $e->getMessage());
        }
    }
}

if (!function_exists('toFalse')) {
    /**
     * 返回json错误信息
     * @param string $status 当前错误状态
     * @param string $message 返回错误信息前追加内容,默认为空
     */
    function toFalse($status,$message=''){
        try {
            $data = tocode($status,$message);
            return json($data);
        } catch (\Exception $e) {
            abort(-1, $e->getMessage());
        }
    }
}

if (!function_exists('totrue')) {
    /**
     * 返回操作成功json信息
     * @param array $object 当前返回对象
     * @param string $special 特殊返回对象处理 有类型：select
     */
    function totrue($object,$message=''){
        try {
            $data = tocode(100,$message);
            $data['data'] = $object;
            ob_clean();
            return json($data);
        } catch (\Exception $e) {
            abort(-1, $e->getMessage());
        }
    }
}

if (!function_exists('tofalse')) {
    /**
     * 返回json错误信息
     * @param string $status 当前错误状态
     * @param string $message 返回错误信息前追加内容,默认为空
     */
    function tofalse($status,$message=''){
        try {
            $data = tocode($status,$message);
            return json($data);
        } catch (\Exception $e) {
            abort(-1, $e->getMessage());
        }
    }
}

if (!function_exists('tocode')) {
    /**
     * json返回错误结果
     */
    function tocode($status, $message = ''){
        $object = config('codemsg');
        if(isset($object[$status]) && !empty($message)){
            $object = [
                'status'  => $status,
                'message' => $object[$status] .','. $message
            ];
        }elseif(!empty($object[$status])){
            $object = [
                'status'  => $status,
                'message' => $object[$status]
            ];
        }else{
            $object = [
                'status'  => $status,
                'message' => $message
            ];
        }
        $object['data'] = null;
        return $object;
    }
}

if (!function_exists('comment2Array')) {
    /**
     * 注释字符串转数组
     *
     * @param string $comment
     *
     * @return array
     */

    function comment2Array($comment = '')
    {
        // 多空格转换成单空格
        $comment = preg_replace('/[ ]+/', ' ', $comment);

        preg_match_all('/\*[\s+]?@(.*?|)[\n|\r]/is', $comment, $matches);

        $arr = [];
        foreach ($matches[1] as $key => $match) {
            $arr[$key] = explode(' ', $match);
        }
        return $arr;
    }
}

