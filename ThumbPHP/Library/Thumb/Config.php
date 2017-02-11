<?php
/**
 * ThumbPHP 系统配置类
 */
namespace Thumb;

class Config
{
    static $config = array();   //记录所有的配置

    /**
     * 初始化配置加载
     * 加载配置的顺序为：惯例配置->公共配置->应用配置
     * 以上是配置文件的加载顺序，因为后面的配置会覆盖之前的同名配置（在没有生效的前提下），所以配置的优先顺序从右到左
     */
    static public function init()
    {
        //加载惯例配置
        self::$config = include THUMB_PATH.'Conf/config'.EXT;
        //加载公共配置
        if(file_exists(__COMMON__.'Conf/'.Config::get('DEFAULT_CONFIG_FILE_NAME').EXT) and $filePath = __COMMON__.'Conf/'.Config::get('DEFAULT_CONFIG_FILE_NAME').EXT){
            $commonConfig = include $filePath;
            self::$config = array_merge(self::$config,(isset($commonConfig) and is_array($commonConfig))?$commonConfig:array());
        }
        //加载应用配置
        if(file_exists(APP_PATH.'Common/Conf/'.Config::get('DEFAULT_CONFIG_FILE_NAME').EXT) and $filePath = APP_PATH.'Common/Conf/'.Config::get('DEFAULT_CONFIG_FILE_NAME').EXT){
            $appConfig    = include $filePath;
            self::$config = array_merge(self::$config,(isset($appConfig) and is_array($appConfig))?$appConfig:array());
        }
    }

    /**
     * 加载模块配置
     */
    static public function loadModuleConfig()
    {
        if(file_exists(APP_PATH.__MODULE__.'/Common/Conf/'.Config::get('DEFAULT_CONFIG_FILE_NAME').EXT) and $filePath = APP_PATH.__MODULE__.'/Common/Conf/'.Config::get('DEFAULT_CONFIG_FILE_NAME').EXT){
            $moduleConfig = include $filePath;
            self::$config = array_merge(self::$config,(isset($moduleConfig) and is_array($moduleConfig))?$moduleConfig:array());
        }
    }

    /**
     * 根据配置名查看配置信息
     * @param string $configName
     * @return array|mixed
     */
    static public function get($configName = '')
    {
        if(!empty(self::$config)){
            if(!empty($configName)){
                $configName = strtoupper($configName);
                return self::$config[$configName];
            }else{
                return self::$config;
            }
        }
    }
}