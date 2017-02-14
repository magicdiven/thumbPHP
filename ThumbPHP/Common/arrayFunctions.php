<?php
/**
 * ThumbPHP 数组函数库
 */

/**
 * 获取数组维数
 * @param $arr 数组
 * @return int 数组维数
 */
function getArrLv($arr)
{
    if(!is_array($arr)){
        return 0;
    }

    $maxArrLvs = 1;
    $arrLv     = 1;
    foreach($arr as $key => $value){
        is_array($value) and $arrLv = getArrLv($value)+1;

        if($arrLv>$maxArrLvs){
            $maxArrLvs = $arrLv;
        }
    }

    return $maxArrLvs;
}

/**
 * 对当前数组的第一维数据进行去重,第一维数组类型支持字符串,数组,对象等
 * @param      $arr        数组
 * @param bool $reserveKey 是否保留原始键值key,默认不保留
 * @return array
 */
function MixedArrUnique($arr,$reserveKey = FALSE)
{
    if(is_array($arr) and !empty($arr)){
        foreach($arr as $key => $value){
            $tmpArr[$key] = serialize($value).'';
        }
        $tmpArr = array_unique($tmpArr);
        $arr    = array();
        foreach($tmpArr as $key => $value){
            if($reserveKey){
                $arr[$key] = unserialize($value);
            }else{
                $arr[] = unserialize($value);
            }
        }
    }
    return $arr;
}

/**
 * 多维数组去重
 * @param      $arr
 * @param bool $reserveKey
 * @return array
 */
function multiArrUnique(&$arr,$reserveKey = FALSE)
{
    if(!is_array($arr)){
        return $arr;
    }

    $arrLv = getArrLv($arr);
    $arr   = MixedArrUnique($arr,$reserveKey);
    for($j = 0;$j<$arrLv;++$j){
        foreach($arr as $key => &$value){
            is_array($value) and multiArrUnique($value,$reserveKey);

            $value = MixedArrUnique($value);
        }
    }
    return $arr;
}
