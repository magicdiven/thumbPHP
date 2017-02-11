<?php
/**
 * ThumbPHP 路由解析类
 */

namespace Thumb;

class Route
{

    /**
     * 检测URL
     */
    static public function checkUrl()
    {
        //URL映射定义(静态路由)
        self::parseUrl();
        self::optimizeGet();
    }

    /**
     * 解析URL
     */
    static public function parseUrl()
    {
        switch(C('URL_MODE')){
            case URL_PATHINFO:
                self::parsePathinfoUrl();
                break;
            case URL_COMMON:
                self::parseCommonUrl();
                break;
        }
    }

    /**
     * 解析PATH_INFO模式的URL
     */
    static public function parsePathinfoUrl()
    {
        $query  = parsePatnInfoQueryString();
        $path   = $query['path'];
        $params = array();

        if(isset($path)){
            switch(count($path)){
                case 3://[/模块/控制器/操作]
                    $params[C('VAR_ACTION')]     = array_pop($path);
                    $params[C('VAR_CONTROLLER')] = array_pop($path);
                    $params[C('VAR_MODULE')]     = array_pop($path);
                    unset($path);
                    break;
                case 2://[/模块/控制器]
                    $params[C('VAR_ACTION')]     = C('DEFAULT_ACTION');
                    $params[C('VAR_CONTROLLER')] = array_pop($path);
                    $params[C('VAR_MODULE')]     = array_pop($path);
                    unset($path);
                    break;
                case 1://[/模块]
                    $params[C('VAR_ACTION')]     = C('DEFAULT_ACTION');
                    $params[C('VAR_CONTROLLER')] = C('DEFAULT_CONTROLLER');
                    $params[C('VAR_MODULE')]     = array_pop($path);
                    unset($path);
                    break;
            }
        }else{
            $params[C('VAR_ACTION')]     = C('DEFAULT_ACTION');
            $params[C('VAR_CONTROLLER')] = C('DEFAULT_CONTROLLER');
            $params[C('VAR_MODULE')]     = C('DEFAULT_MODULE');
        }

        defined('__MODULE__') or define('__MODULE__',$params[C('VAR_MODULE')]);
        defined('__CONTROLLER__') or define('__CONTROLLER__',$params[C('VAR_CONTROLLER')]);
        defined('__ACTION__') or define('__ACTION__',$params[C('VAR_ACTION')]);
        unset($params);

        return TRUE;
    }

    /**
     * 解析普通模式的URL
     */
    static public function parseCommonUrl()
    {
        $queryString = $_SERVER['QUERY_STRING'];
        $params      = array();//接受请求的参数
        parse_str($queryString,$params);

        $params[C('VAR_MODULE')]     = empty($params[C('VAR_MODULE')])?C('DEFAULT_MODULE'):$params[C('VAR_MODULE')];
        $params[C('VAR_CONTROLLER')] = empty($params[C('VAR_CONTROLLER')])?C('DEFAULT_CONTROLLER'):$params[C('VAR_CONTROLLER')];
        $params[C('VAR_ACTION')]     = empty($params[C('VAR_ACTION')])?C('DEFAULT_ACTION'):$params[C('VAR_ACTION')];

        defined('__MODULE__') or define('__MODULE__',$params[C('VAR_MODULE')]);
        defined('__CONTROLLER__') or define('__CONTROLLER__',$params[C('VAR_CONTROLLER')]);
        defined('__ACTION__') or define('__ACTION__',$params[C('VAR_ACTION')]);
        unset($params);

        return TRUE;
    }

    /**
     * 优化$_GET值,去掉应用初始化的内置参数
     * 去掉模块获取，控制器获取，操作获取的参数
     */
    static public function optimizeGet()
    {
        switch(C('URL_MODE')){
            case URL_PATHINFO:
                if(__MODULE__ and __CONTROLLER__ and __ACTION__){
                    unset($_GET['/'.__MODULE__.'/'.__CONTROLLER__.'/'.__ACTION__]);
                }
                if(__MODULE__ and __CONTROLLER__){
                    unset($_GET['/'.__MODULE__.'/'.__CONTROLLER__]);
                }
                if(__MODULE__){
                    unset($_GET['/'.__MODULE__]);
                }
                break;

            case URL_COMMON:
                if(__MODULE__ and __CONTROLLER__ and __ACTION__){
                    unset($_GET[C('VAR_MODULE')]);
                    unset($_GET[C('VAR_CONTROLLER')]);
                    unset($_GET[C('VAR_ACTION')]);
                }
                if(__MODULE__ and __CONTROLLER__){
                    unset($_GET[C('VAR_MODULE')]);
                    unset($_GET[C('VAR_CONTROLLER')]);
                }
                if(__MODULE__){
                    unset($_GET[C('VAR_MODULE')]);
                }
                break;
        }
    }
}