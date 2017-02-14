<?php
/**
 * ThumbPHP 对象函数库
 */

/**
 * 对象转数组
 * @param $obj
 * @return array
 */
function obj2arr($obj)
{
    if(is_object($obj)){
        $obj = (array)$obj;
        $obj = obj2arr($obj);
    }elseif(is_array($obj)){
        foreach($obj as $key => $value){
            $obj[$key] = obj2arr($value);
        }
    }
    return $obj;
}