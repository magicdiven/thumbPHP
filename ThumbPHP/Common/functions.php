<?php
/**
 * ThumbPHP 系统函数库
 */

/**
 * 抛出异常处理
 * @param     $msg
 * @param int $code
 * @throws Exception
 */
function throwException($msg,$code = 0)
{
    throw new Exception($msg,$code);
}

/**
 * @param string $value 传递值
 * @param string $type  标记类型
 */
function trace($value = '',$type = 'DEBUG')
{
    //    $traceInfo = debug_backtrace();
    echo $value,"<br/>";
    //    var_dump('<pre>', $traceInfo[6]);
}

/**
 * 根据配置名查看配置信息
 * @param string $configName
 * @return array|mixed
 */
function C($configName = '')
{
    return \Thumb\Config::get($configName);
}

/**
 * 解析PATHINFO模式的URL对应的请求路径path和请求参数params
 * @return array
 */
function parsePatnInfoQueryString()
{
    $query = $params = array();//接受请求的路径和参数

    if(isset($_SERVER['PATH_INFO'])){
        $path = explode('/',$_SERVER['PATH_INFO']);
        parse_str($_SERVER['QUERY_STRING'],$params);
    }else{
        $queryString = $_SERVER['QUERY_STRING'];
        if(!empty($queryString)){
            //访问非默认的 [模块/控制器/操作] 时解析url，访问默认的 [模块/控制器/操作] 且在url上省略时，不进行任何解析
            if('/'==$queryString[0]){
                //如果第一个参数将QUERY_STRING中出现的第一个'&'转化成'?'
                $pos = strpos($queryString,'&') and $queryString[$pos] = '?';
                if(!empty($queryString)){
                    if(strpos($queryString,'?')){// [模块/控制器/操作?]参数1=值1&参数2=值2...
                        $info = parse_url($queryString);
                        $path = explode('/',$info['path']);
                        parse_str($info['query'],$params);
                    }elseif(FALSE!==strpos($queryString,'/')){// [模块/控制器/操作]
                        $path   = explode('/',$queryString);
                        $params = array();
                    }
                    unset($queryString);
                }
            }
        }
    }

    $query['path']   = isset($path)?array_filter($path):NULL;
    $query['params'] = empty($params)?'':$params;

    return $query;
}

/**
 * 判断是否SSL协议
 * @return bool
 */
function is_ssl()
{
    if(isset($_SERVER['HTTPS']) and ('1'==$_SERVER['HTTPS'] or 'on'==$_SERVER['HTTPS'] or 'ON'==$_SERVER['HTTPS'])){
        return TRUE;
    }elseif(isset($_SERVER['SERVER_PORT']) and ('443'==$_SERVER['SERVER_PORT'])){
        return TRUE;
    }
    return FALSE;
}

/**
 * 转义字符串中的特殊字符以用于SQL语句
 * @param $string 转义之前的字符串
 * @return string 转义之后的字符串
 */
function escapeString($string)
{
    if(version_compare(PHP_VERSION,'5.4.0','<')){
        return mysql_escape_string($string);
    }else{
        return mysql_real_escape_string($string);
    }
}


include 'arrayFunctions' . EXT;
include 'objectFunctions' . EXT;
