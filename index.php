<?php
/**
 * 应用入口文件
 */

// 检测PHP环境，需要PHP版本>5.3.0
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

//定义应用目录
define('APP_PATH','./Application/');

//默认开启开发模式,上线后需false
define('DEBUG_MODE',TRUE);

//引入thumbPHP入口文件
require './ThumbPHP/ThumbPHP.php';
