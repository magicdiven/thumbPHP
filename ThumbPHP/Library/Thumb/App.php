<?php
namespace Thumb;
/**
 * 应用程序类，执行应用程序过程管理
 */

class App
{
    /**
     * 应用程序初始化
     */
    static public function init()
    {
        //框架编码
        header('content-type:text/html; charset='.Config::get('DEFAULT_CHARSET'));

        //加载配置文件
        Config::init();

        //加载系统css样式
        Thumb::loadBaseStatics();

        //加载公共函数
        self::loadCommFunc();

        //路由解析
        Route::checkUrl();

        //加载模块配置
        Config::loadModuleConfig();

        //加载访问模块公共函数
        self::loadModuleCommFunc();

        //执行应用
        self::exec();
    }

    /**
     * 运行应用实例
     */
    static public function run()
    {
        self::init();
    }

    /**
     * 根据url的参数，定位模块、控制器、操作,并执行
     */
    static public function exec()
    {
        $className = __MODULE__.'\\'.C('DEFAULT_C_LAYER').'\\'.__CONTROLLER__.C('DEFAULT_C_LAYER');
        $class     = new $className;
        $class->{__ACTION__}();
    }

    /**
     * 加载核心类库的公共函数->系统公共函数->应用公共函数
     */
    static public function loadCommFunc()
    {
        //加载核心类库的公共函数
        include THUMB_PATH.'Common/'.Config::get('COMMON_FUNC').EXT;
        //加载系统公共函数
        if(file_exists(__COMMON__.Config::get('COMMON_FUNC').EXT) and $filePath = __COMMON__.Config::get('COMMON_FUNC').EXT){
            include $filePath;
        }
        //加载应用公共函数
        if(file_exists(APP_PATH.'Common/'.Config::get('COMMON_FUNC').EXT) and $filePath = APP_PATH.'Common/'.Config::get('COMMON_FUNC').EXT){
            include $filePath;
        }
    }

    /**
     * 加载模块公共函数
     * 加载公共函数的顺序为：模块公共函数
     */
    static public function loadModuleCommFunc()
    {
        //加载当前访问的模块公共函数
        if(file_exists(APP_PATH.__MODULE__.'Common/'.Config::get('COMMON_FUNC').EXT) and $filePath = APP_PATH.__MODULE__.'Common/'.Config::get('COMMON_FUNC').EXT){
            include $filePath;
        }
    }
}