<?php
/**
 * 系统惯例配置
 */

return array(
    /**     * 默认设定     */
    //默认模块
    'DEFAULT_MODULE'           => 'Index',
    //默认控制器
    'DEFAULT_CONTROLLER'       => 'Index',
    //默认方法
    'DEFAULT_ACTION'           => 'index',
    // 默认的模型层名称
    'DEFAULT_M_LAYER'          => 'Model',
    // 默认的控制器层名称
    'DEFAULT_C_LAYER'          => 'Controller',
    // 默认的视图层名称
    'DEFAULT_V_LAYER'          => 'View',
    //默认字符编码
    'DEFAULT_CHARSET'          => 'utf-8',
    //默认为PATHINFO访问模式
    'URL_MODE'                 => URL_PATHINFO, //URL_COMMON 为普通模式
    //默认存放配置参数的文件名
    'DEFAULT_CONFIG_FILE_NAME' => 'config',

    /**     * 默认数据库设置  目前实现的数据库驱动有Mysqli   */
    'DEFAULT_DATABASE'         => array(
        //数据库类型
        'DB_TYPE'    => 'Mysqli',
        //服务器地址
        'DB_HOST'    => '',
        //数据库名
        'DB_NAME'    => '',
        //用户名
        'DB_USER'    => '',
        //密码
        'DB_PWD'     => '',
        //端口
        'DB_PORT'    => '',
        //数据库表前缀
        'DB_PREFIX'  => '',
        //数据库编码默认采用utf8
        'DB_CHARSET' => 'utf8',
    ),

    /**     * 系统变量名设置     */
    //默认模块获取变量
    'VAR_MODULE'               => 'm',
    //默认控制器获取变量
    'VAR_CONTROLLER'           => 'c',
    //默认操作获取变量
    'VAR_ACTION'               => 'a',
    //默认分页参数
    'VAR_PAGE'                 => 'p',
    
    //公共函数文件名，需创建在Common文件夹下
    'COMMON_FUNC'              => 'functions',

);