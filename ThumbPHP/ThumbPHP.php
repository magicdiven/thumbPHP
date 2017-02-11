<?php
/**
 * 公共入口文件
 */

//版本信息
const THUMB_VERTION = '1.0.0';
//类文件后缀
const EXT = '.php';
const HTML_EXT = '.html';

//定义路由模式
const URL_COMMON    =   0;//普通模式
const URL_PATHINFO  =   1;//PATHINFO模式

//系统常量定义
defined('THUMB_PATH') or define('THUMB_PATH',__DIR__.'/');//ThumbPHP公共入口文件目录
//defined('APP_PATH') or define('APP_PATH',dirname($_SERVER['SCRIPT_FILENAME']));
defined('LIB_PATH') or define('LIB_PATH', realpath(THUMB_PATH . 'Library') . '/');//系统核心类库目录
defined('CORE_PATH') or define('CORE_PATH', LIB_PATH . 'Thumb/');//Thumb类库目录
defined('VENDOR_PATH') or define('VENDOR_PATH', LIB_PATH . 'Vendor/');//第三方插件类库目录
defined('TPL_PATH') or define('TPL_PATH', THUMB_PATH . 'Tpl/');//模板目录

define('__ROOT__',substr($_SERVER['SCRIPT_NAME'],0,-9));//ThumbPHP根目录
//define('__ROOT__','/');//ThumbPHP根目录
define('__PUBLIC__',__ROOT__.'Public/');//ThumbPHP公共资源目录
define('__COMMON__',__ROOT__.'Common/');//ThumbPHP公共配置公共函数目录

//加载核心Thumb类
require CORE_PATH . 'Thumb' . EXT;
Thumb\Thumb::start();