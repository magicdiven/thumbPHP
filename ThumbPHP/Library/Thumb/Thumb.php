<?php
namespace Thumb;
/**
 * ThumbPHP 引导类
 */
class Thumb
{
    //实例化对象
    static private $_instance = array();

    /**
     * 应用程序员初始化
     */
    static public function start()
    {
        (DEBUG_MODE and error_reporting(E_ALL)) or error_reporting(0);//开启开发模式则报错级别开到最大，否则不报错（用于生产模式）

        //注册自动加载方法
        spl_autoload_register('Thumb\Thumb::classLoader');

        //运行应用
        App::run();
    }

    /**
     * 应用自动加载机制
     * @param $className 类名，包含命名空间前缀
     */
    static public function classLoader($className)
    {
        $pathName = explode('\\',$className);
        switch($pathName[0]){
            case 'Thumb':
                //加载核心文件
                self::loadCore($pathName);
                break;
            default:
                //加载应用文件
                self::loadApp($pathName);
        }
    }

    /**
     * 实例化对象类
     */
    static public function instance($class,$method = '')
    {
        $identify = $class.$method;
        if(!isset(self::$_instance[$identify])){
            $o = new $class();
            if(!empty($method)&&method_exists($o,$method)){
                self::$_instance[$identify] = call_user_func(array(
                    &$o,
                    $method
                ));
            }else{
                self::$_instance[$identify] = $o;
            }
        }

        return self::$_instance[$identify];
    }

    /**
     * 加载核心文件
     */
    static private function loadCore($pathName)
    {
        $fileName = LIB_PATH;
        foreach($pathName as $_pathName){
            $fileName .= '/'.$_pathName;
        }
        include $fileName.EXT;
    }

    /**
     * 加载应用文件
     */
    static private function loadApp($pathName)
    {
        $fileName = substr(APP_PATH,0,-1);
        foreach($pathName as $_pathName){
            $fileName .= '/'.$_pathName;
        }
        include $fileName.EXT;
    }

    /**
     * 加载系统css文件
     */
    static public function loadBaseStatics()
    {
        $jqPath     = __PUBLIC__.'statics/js/js-1.11.0/dist/jquery.min.js';
        $zuiCssPath = __PUBLIC__.'statics/zui-1.5.0-dist/dist/css/zui.min.css';
        $zuiJsPath  = __PUBLIC__.'statics/zui-1.5.0-dist/dist/js/zui.min.js';
        echo "<link rel='stylesheet' type='text/css' href='{$zuiCssPath}' />";
        echo "<script src='{$jqPath}'></script>";
        echo "<script src='{$zuiJsPath}'></script>";
    }
}