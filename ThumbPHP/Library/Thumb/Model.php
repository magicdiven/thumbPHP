<?php
/**
 * ThumbPHP Model模型类
 */

namespace Thumb;

class Model
{
    protected $dbConfigName = '';       //数据库配置
    protected $db = NULL;               //当前数据库库操作对象
    protected $_db = array();           //数据库对象池
    protected $modelName = '';          //模型名称
    protected $dbName = '';             //数据库名称
    protected $tableName = '';          //数据库表名称
    protected $tablePrefix = NULL;      //数据库表前缀

    /* 链式操作存储的sql片段，每次执行完后还原为''. */
    private $_whereStr = '';            //where条件语句
    private $_fieldsStr = '*';          //字段
    private $_joinStr = '';             //联表和联表条件
    private $_limitStr = '';            //限制行数
    private $_orderStr = '';            //排序
    private $_groupStr = '';            //分组
    private $_havingStr = '';           //分组条件


    /**
     * 获取DB类的实例对象，检查模型名、表前缀、和数据库连接信息
     * Model constructor.
     */
    public function __construct()
    {
        //模型初始化
        $this->_init();
    }

    /**
     * 获取当前数据对象名称
     */
    public function getModelName()
    {
        if(empty($this->modelName)){
            $modelName = get_class($this);
            while($pos = strpos($modelName,'\\')){
                $modelName = substr($modelName,$pos+1);
            }
        }else{
            $modelName = $this->modelName;
        }
        return $modelName;
    }

    /**
     * 切换当前数据库连接或重连数据库
     * @param string         $dbConfigName 数据库配置名
     * @param integer|string $linkNum      数据库连接序号
     * @param bool           $force        强制重新连接数据库
     * @return Model
     */
    public function db($dbConfigName = '',$linkNum = 0,$force = FALSE)
    {

        if(''===$linkNum&&$this->db){
            return $this->db;
        }

        if(!isset($this->_db[$linkNum])||$force){
            //创建一个新的实例
            if(!empty($dbConfigName)&&is_string($dbConfigName)){
                $this->dbConfigName = $dbConfigName;
                $dbConfig           = C($dbConfigName);
                $dbConfig['DB_PREFIX'] and $this->tablePrefix = $dbConfig['DB_PREFIX'];
            }
            $this->_db[$linkNum] = Db::getInstance($dbConfigName,$dbConfig);
        }elseif(NULL===$dbConfigName){
            $this->db[$linkNum]->close();   //关闭数据库连接
            unset($this->_db[$linkNum]);
            return FALSE;
        }

        //切换当前数据库连接
        $this->db = $this->_db[$linkNum];
        return $this;
    }

    /**
     * 选择数据表，表名默认等于表模型名，不含表前缀，不含默认模型层名称
     * @param string $tableName 数据表名
     * @return $this
     */
    protected function table($tableName = '')
    {
        $this->tableName = $this->parseDbTable($tableName);

        return $this;
    }

    /**
     * SQL查询
     * @param $queryStr 查询sql语句
     * @return mixed
     */
    public function query($queryStr)
    {
        return $this->db->query($queryStr);
    }

    /**
     * 执行SQL指令
     * @param $queryStr
     * @return mixed
     */
    public function execute($queryStr)
    {
        return $this->db->execute($queryStr);
    }

    /**
     * where条件语句
     * @param $condition 条件，支持字符串和数组
     * @return $this
     */
    protected function where($condition)
    {
        $this->parseWhere($condition);
        return $this;
    }

    /**
     * 解析where条件语句
     * @param $condition
     */
    private function parseWhere($condition)
    {
        if(empty($condition)){
        }elseif(is_string($condition)){
            $this->_whereStr .= ' '.$condition.' ';
        }elseif(is_array($condition)){
            $where = $this->parseArrCondition($condition);
            $this->_whereStr .= ' '.$where.' ';
            unset($where);
        }
        return;
    }

    /**
     * 字段值
     * @param $fields 字段，支持字符串和数组
     * @return $this
     */
    protected function fields($fields)
    {
        $this->parseFields($fields);
        return $this;
    }

    /**
     * 解析字段
     * @param $fields 字段
     */
    private function parseFields($fields)
    {
        if(empty($fields)){
        }elseif(is_string($fields)){
            $this->_fieldsStr = ' '.$fields.' ';
        }elseif(is_array($fields)){
            $this->_fieldsStr = ' '.implode(', ',$fields).' ';
        }
        return;
    }

    /**
     * 联表
     * @param $type LEFT|RIGHT...
     * @param $table
     * @return $this
     */
    protected function join($type,$table)
    {
        $this->parseJoin($type,$table);
        return $this;
    }

    /**
     * 解析联表
     * @param $type LEFT|RIGHT...
     * @param $table
     */
    private function parseJoin($type,$table)
    {
        $table = $this->parseDbTable($table);
        $this->_joinStr .= ' '.strtoupper($type).' JOIN '.$table;
    }

    /**
     * 联表条件
     * @param $condition
     * @return $this
     */
    protected function on($condition)
    {
        $this->parseOn($condition);
        return $this;
    }

    /**
     * 解析联表条件,支持字符串和数组
     * @param $condition
     */
    private function parseOn($condition)
    {
        if(empty($condition)){
        }elseif(is_string($condition)){
            $this->_joinStr .= ' ON '.$condition;
        }elseif(is_array($condition)){
            $join = $this->parseArrCondition($condition);
            $this->_joinStr .= ' ON '.$join;
            unset($join);
        }
        return;
    }

    /**
     * 数据开始与结束行数设置
     * @param      $offset
     * @param null $length
     * @return $this
     */
    protected function limit($offset,$length = NULL)
    {
        if($length){
            $this->_limitStr = ' LIMIT '.$offset.','.$length;
        }else{
            $this->_limitStr = ' LIMIT '.$offset;
        }
        return $this;
    }

    /**
     * 排序
     * @param string $orderStr
     * @return $this
     */
    protected function order($orderStr = '')
    {
        if($orderStr){
            $this->_orderStr = ' ORDER BY '.$orderStr;
        }
        return $this;
    }

    /**
     * 分组
     * @param string $groupStr
     * @return $this
     */
    protected function group($groupStr = '')
    {
        if($groupStr){
            $this->_groupStr = ' GROUP BY '.$groupStr;
        }
        return $this;
    }

    /**
     * 分组条件，支持字符串和数组
     * @param $condition
     * @return $this
     */
    protected function having($condition)
    {
        $this->parseHaving($condition);
        return $this;
    }

    /**
     * 解析分组条件
     * @param $condition
     */
    private function parseHaving($condition)
    {
        if(empty($condition)){
        }elseif(is_string($condition)){
            $this->_havingStr .= ' HAVING '.$condition;
        }elseif(is_array($condition)){
            $having = $this->parseArrCondition($condition);
            $this->_havingStr .= ' HAVING '.$having;
            unset($having);
        }
        return;
    }

    /**
     * 解析数组形式的条件
     * @param $condition
     * @return string
     */
    private function parseArrCondition($condition)
    {
        $condStr = '';
        foreach($condition as $field => $_condition){
            if(!is_array($_condition)){
                if((is_string($_condition) and FALSE!==strpos($_condition,'.')) or !is_string($_condition)){//有'.'或者非字符串
                    $condStr .= $field.'='.$_condition.' AND ';
                }else{//字符串且无'.'
                    $condStr .= $field.'=\''.escapeString($_condition).'\' AND ';
                }
            }else{
                foreach($_condition as $op1 => $_condition1){
                    if((is_string($_condition1) and FALSE!==strpos($_condition,'.')) or !is_string($_condition1)){
                        $condStr .= $field.$op1.$_condition1.' AND ';
                    }else{
                        $condStr .= $field.$op1.'\''.escapeString($_condition1).'\' AND ';
                    }
                }
            }
        }
        return $condStr;
    }

    /**
     * 拼装链式sql语句
     * @param $type SELECT|INSERT|UPDATE|DELETE
     * @return string
     */
    private function parseSql($type)
    {
        !empty($this->_whereStr) and $this->_whereStr = ' WHERE'.rtrim($this->_whereStr,'AND ');
        !empty($this->_joinStr) and $this->_joinStr = rtrim($this->_joinStr,'AND ');
        !empty($this->_havingStr) and $this->_havingStr = rtrim($this->_havingStr,'AND ');

        $queryStr='';
        switch($type){
            case 'SELECT':
                $queryStr = 'SELECT'.$this->_fieldsStr.'FROM '.$this->tableName.$this->_joinStr.$this->_whereStr.$this->_groupStr.$this->_havingStr.$this->_orderStr.$this->_limitStr;
                break;
        }

        $this->initSqlParams();

        return $queryStr;
    }

    /**
     * 解析用'.'拼接的数据库和表
     * @param $tableName
     * @return string
     */
    private function parseDbTable($tableName)
    {
        if(empty($tableName)){
            return $this->tableName;
        }

        $dbTable = explode('.',$tableName);
        if(2==count($dbTable)){//数据库.数据表
            return $tableName;
        }
        return $this->tablePrefix.$tableName;
    }

    /**
     * 执行查询
     * @return mixed
     */
    protected function select()
    {
        $queryStr = $this->parseSql('SELECT');
        return $this->query($queryStr);
    }

    /**
     * 返回最后执行的sql
     * @return mixed
     */
    protected function getLastSql()
    {
        return $this->db->getLastSql();
    }

    /**
     * 获取最后插入ID
     * @return mixed
     */
    public function getLastInsId()
    {
        return $this->db->getLastInsId();
    }

    /**
     * 初始化链式操作的存储sql片段的参数
     */
    private function initSqlParams()
    {
        $this->_whereStr  = '';
        $this->_fieldsStr = '*';
        $this->_joinStr   = '';
        $this->_limitStr  = '';
        $this->_orderStr  = '';
        $this->_groupStr  = '';
        $this->_havingStr = '';
    }

    /**
     * 模型初始化设置
     */
    public function _init()
    {
        $this->modelName = $this->getModelName();

        //表名默认等于表模型名，不含表前缀，不含默认模型层名称
        $this->tableName = substr($this->modelName,0,-strlen(C('DEFAULT_M_LAYER')));
    }
}

?>