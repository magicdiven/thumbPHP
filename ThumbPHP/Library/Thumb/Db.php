<?php
/**
 * 数据库中间层实现类
 */

namespace Thumb;

class Db
{
    static private $instance = array();     //数据库连接实例
    static private $_instance = NULL;       //当前数据库连接实例

    protected $dbConfig = array();  //数据库参数配置
    protected $linkId = array();    //数据库连接id，支持多个连接
    protected $_linkId = NULL;      //当前连接id
    protected $connected = FALSE;   // 是否已经连接数据库
    protected $queryId = NULL;      //当前查询id

    protected $queryStr = '';       //当前sql指令
    protected $error = '';          //错误信息

    protected $numRows = 0;         //返回或者影响记录数
    protected $filedCount = 0;      //返回字段数
    protected $lastInsId = NULL;    //最后插入ID
    
    /**
     * 获取数据库类实例
     * @param string $dbConfigName 数据库配置名
     * @param array  $dbConfig     数据库配置信息
     * @return mixed 返回数据库驱动类
     */
    static public function getInstance($dbConfigName = '',$dbConfig = array())
    {
        if(!isset(self::$instance[$dbConfigName])){
            $dbClass = 'Thumb\\Db\\Driver\\'.ucfirst($dbConfig['DB_TYPE']);
            if(class_exists($dbClass)){
                self::$instance[$dbConfigName] = new $dbClass($dbConfig);
            }else{
                //类没有定义的提示
                $msg = "class {$dbClass} not found";
                throwException($msg);
                return FALSE;
            }
        }

        return self::$instance[$dbConfigName];
    }

    /**
     * 初始化数据库连接
     */
    protected function initConnect()
    {
        if(!$this->connected){
            $this->_linkId = $this->connect();
        }
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        // 释放查询
        if($this->queryId){
            $this->free();
        }
        // 关闭连接
        $this->close();
    }
}