<?php
/**
 * mysqli数据库驱动
 */

namespace Thumb\Db\Driver;

use Thumb\Db;

class Mysqli extends Db
{

    /**
     * 判断mysqli扩展，读取数据库配置信息
     * Mysqli constructor.
     * @param array $dbConfig
     */
    public function __construct($dbConfig = array())
    {
        if(!extension_loaded('mysqli')){
            echo 'mysqli扩展不存在！';
        }
        if(!empty($dbConfig)){
            $this->dbConfig = $dbConfig;
        }
    }

    /**
     * 连接数据库
     * @param array $dbConfig
     * @param int   $linkNum
     * @return mixed
     * @throws \Exception
     */
    public function connect($dbConfig = array(),$linkNum = 0)
    {
        if(!isset($this->linkId[$linkNum])){
            if(empty($dbConfig)){
                $dbConfig = $this->dbConfig;
            }
            $this->linkId[$linkNum] = new \mysqli($dbConfig['DB_HOST'],$dbConfig['DB_USER'],$dbConfig['DB_PWD'],$dbConfig['DB_NAME'],$dbConfig['DB_PORT']?intval($dbConfig['DB_PORT']):3306);
            if(mysqli_connect_errno()){
                throwException(mysqli_connect_error());
            }
            $dbVersion = $this->linkId[$linkNum]->server_version;

            //设置数据库编码
            $this->linkId[$linkNum]->query("SET NAMES '".$dbConfig['DB_CHARSET']."'");
            //设置sql_mode为不严格检查
            if($dbVersion>'5.0.1'){
                $this->linkId[$linkNum]->query("SET sql_mode=''");
            }
            //标记连接成功
            $this->connected = TRUE;
            //注销数据库安全信息
            unset($this->dbConfig);
        }
        return $this->linkId[$linkNum];
    }

    /**
     * 执行SQL查询
     * @param $queryStr
     * @return array|bool
     */
    public function query($queryStr)
    {
        $this->initConnect();
        if(!$this->_linkId){
            return FALSE;
        }
        $this->queryStr = $queryStr;
        //释放前一次查询结果
        if($this->queryId){
            $this->free();
        }

        $this->queryId = $this->_linkId->query($queryStr);

        if(FALSE===$this->queryId){
            $this->error();
            return FALSE;
        }else{
            $this->numRows    = $this->queryId->num_rows;
            $this->filedCount = $this->queryId->field_count;
            return $this->getAll();
        }
    }

    /**
     * 执行SQL指令
     * @param $queryStr
     * @return bool
     */
    public function execute($queryStr)
    {
        $this->initConnect();
        if(!$this->_linkId){
            return FALSE;
        }
        $this->queryStr = $queryStr;
        //释放前一次查询结果
        if($this->queryId){
            $this->free();
        }

        $result = $this->_linkId->query($queryStr);
        if(FALSE===$result){
            $this->error();
            return FALSE;
        }else{
            $this->numRows   = $this->_linkId->affected_rows;
            $this->lastInsId = $this->_linkId->insert_id;
            return $this->numRows;
        }
    }


    public function insert($data)
    {

    }

    public function insertAll($datas)
    {

    }

    public function delete($id)
    {

    }

    public function update()
    {

    }

    public function select()
    {

    }

    public function selectOne()
    {

    }

    /**
     * 返回最后执行的sql语句
     * @return string
     */
    public function getLastSql()
    {
        return $this->queryStr;
    }

    /**
     * 释放查询结果
     */
    public function free()
    {
        if($this->queryId){
            $this->queryId->free();
        }
    }

    /**
     * 数据库当前错误信息以及当前SQL指令
     */
    public function error()
    {
        $this->error = 'Mysql错误代码【'.$this->_linkId->errno.'】:'.$this->_linkId->error;
        if(''!==$this->queryStr){
            $this->error .= "<br/> [异常SQL语句] : ".$this->queryStr;
        }

        trace($this->error,'ERR');
    }

    /**
     * 获取所有查询结果
     * @return array
     */
    public function getAll()
    {
        $result = array();
        if($this->numRows>0 and $count = $this->numRows){
            while($count--){
                $result[] = $this->queryId->fetch_assoc();
            }
        }
        return $result;
    }

    /**
     * 获取最后插入ID
     * @return null
     */
    public function getLastInsId()
    {
        return $this->lastInsId;
    }

    /**
     * 关闭数据库
     */
    public function close()
    {
        if ($this->_linkId) {
            $this->_linkId->close();
        }
        $this->_linkId = NULL;
    }

}