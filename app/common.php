<?php
// 应用公共文件
/**
 * 生成随机数
 * @param $len
 * @return string
 */
function GetRandStr($len) {
	$chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k","l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v","w", "x", "y", "z","0", "1", "2","3", "4", "5", "6", "7", "8", "9");
	$charsLen = count($chars) - 1;
	shuffle($chars);
	$output = "";
	for ($i=0; $i<$len; $i++){
		$output .= $chars[mt_rand(0, $charsLen)];
	}
	return $output;
}


/**
 * HTTP请求
 * @param string $url			请求地址
 * @param mixed $params			请求参数
 * @param int $requestType		请求类型
 * @param array $headers		请求头
 * @param int $timeout			请求超时
 * @return mixed
 */
function do_curl_request($url, $params = "", $requestType = 'post', $headers = ['Content-type:application/x-www-form-urlencoded;charset=UTF-8'], $timeout = 30, $options = []) 
{

    if ($url == '' || $timeout <= 0) {
        return false;
    }

    //判断请求类型
    $requestHttp = array('post', 'get');
    if (false === in_array($requestType, $requestHttp)) {
        return false;
    }

    $curl = curl_init();

    $requestString = $params;
    if (true === is_array($params) || true === is_object($params)) {
        $requestString = http_build_query($params);
    }

    if ($options && is_array($options)) {
        foreach ($options as $key => $value) {
            curl_setopt($curl, $key, $value);
        }
    }
    //请求类型
    switch ($requestType) {
        case 'post': {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $requestString);
            }
            break;
        case 'get': {
                if (is_string($requestString) && strlen($requestString) > 0) {
                    if (false === strpos($url, '?')) {
                        $url = $url . '?' . $requestString;
                    } else {
                        $url = $url . '&' . $requestString;
                    }
                }
            }
            break;
        default:
            break;
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    // 不验证证书
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    // 不验证HOST
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSLVERSION, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, (int) $timeout);

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
}