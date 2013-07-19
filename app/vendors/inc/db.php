<?php
/****************************************************
 * FileName: app/vendors/inc/db.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/

/**
 * class DB
 * simple database operater
 *
 * @extends PDO
 * @author xw
 */
class DB extends PDO{

    public $stm = null;
    public $version;

    private static $_db;
    private static $_config;

    private $_link = null;
    private $_sql = null;
    private $_cache = null;
    private $_connected = false;
    private $_fetchMode = parent::FETCH_ASSOC;


    /**
     * function getInstance
     *
     * @param array $dbconfig array('ServerName', 'UserName', 'Password', 'DefaultDb', 'DB_Port', 'DB_TYPE')
     * @return DB object
     * @static
     * @access public
     * @throws DBException
     */
    public static function getInstance($dbConfig = null){
        if (!is_array($dbConfig)) {
            $dbConfig = Configure::read("db");
        }
        if(isset(self::$_db) && self::$_config == $dbConfig)
            return self::$_db;
        self::_initConfig($dbConfig);
        if (!class_exists('PDO'))
            throw new DBException("PDO isn't supported");
        self::$_db = new DB();
        return self::$_db;
    }

    /**
     * fucntion __construct()
     *
     * @param $dbconfig array('ServerName', 'UserName', 'Password', 'DefaultDb', 'DB_Port', 'DB_TYPE')
     * @return DB object
     * @access public
     */
    public function __construct(){
        $configs = self::$_config;
        try {
            parent::__construct($configs['dsn'], $configs['user'], $configs['pwd']);
            $this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "can't connect database!";
            trigger_error($e->getMessage(), E_USER_ERROR);
            exit();
        }
        $this->exec('SET NAMES '.$configs['charset']);
        $this->_connected = true;
        $this->version = $this->getAttribute(constant("PDO::ATTR_SERVER_INFO"));
        unset($configs);
    }

    public function free(){
        $this->stm = null;
    }

    /**
     * fuction one
     * get the first record of query
     * if no result return false
     *
     * @param string $sql
     * @param array $param sql params
     * @param int $mode fetch mode default is $this->_fetchMode
     * @return mixed query result|false
     */
    public function one($sql, $param = null, $mode = null){
        $this->stm = $this->_query($sql, $param);
        if(is_null($mode))
            $mode = $this->_fetchMode;
        $this->stm->setFetchMode($mode);
        return $this->stm->fetch();
    }

    /**
     * fuction all
     * get all the record of query
     * if no result return false
     *
     * @param string $sql
     * @param array $param sql params
     * @param int $mode fetch mode default is $this->_fetchMode
     * @return mixd query result|array() while fail
     */
    public function all($sql, $param = null, $mode = null){
        $this->stm = $this->_query($sql, $param);
        if(is_null($mode))
            $mode = $this->_fetchMode;
        $this->stm->setFetchMode($mode);
        $ret = $this->stm->fetchAll();
        return empty($ret)?array():$ret;
    }

    /**
     * fuction find
     * find value from sql result
     * the sql result will be cached
     * if $val is array ,it will match a row of result
     * and the sequence of $val must be the same as that in sql result
     * be careful the sql result is string
     *
     * @param mixed $val string|array
     * @param string $sql
     * @param array $param param of sql
     * @return mixed effective row number|false
     */
    public function find($val, $sql, $param = null){
        if($sql !== $this->_sql){
            $this->_cache = $this->all($sql, $param, parent::FETCH_NUM);
            $this->_sql = $sql;
        }
        if(!$this->_cache)
            return false;
        if(is_array($val)){
            return in_array($val, $this->_cache);
        }else{
            foreach($this->_cache as $v){
                if(in_array((string)$val, $v))
                    return true;
            }
        }
        return false;
    }

    /**
     * function insert
     * insert record into table
     * if error return false
     *
     * @param string $table
     * @param array $val array("k"=>array(keys), "v"=>array(array(value)))
     * @return mixed effective row number|false
     */
    public function insert($table, $val){
        $key = $vals = "";
        $kcount = 0;
        if(isset($val['k'])){
            $key = "(" . join(",", array_map(array($this, '_addSpecialChar'), $val['k'])) . ")";
            $kcount = count($val['k']);
        }
        if(isset($val['v']) && !empty($val['v'])){
            foreach($val['v'] as $v){
                if(count($v) != $kcount && $kcount != 0)
                    continue;
                $vals[] = "('" . join("','", array_map(array($this, '_addslashes'), $v)) . "')";
            }
        }else{
            return false;
        }
        $sql = "INSERT INTO `{$table}`{$key} VALUES" . join(",", $vals);
        $this->stm = $this->_query($sql);
        return $this->stm->rowCount();
    }

    /**
     * fuction delete
     * delete record into table
     * if error return false
     *
     * @param string $table
     * @param string $where String with "where"
     * @param array $param param of $where
     * @return mixed effective row number|false
     */
    public function delete($table, $where, $param = null){
        if(!preg_match("/^where/i", $where))
            $where = "";
        $sql = "DELETE FROM `$table` $where";
        $this->stm = $this->_query($sql, $param);
        return $this->stm->rowCount();
    }

    /**
     * function update
     * update record into table
     * if error return false
     * add \ before key, it will not quote around value
     *
     * @param string $table
     * @param array $val array("key"=>value) or array("\\key"=>value);
     * @param string $where String with "where"
     * @param array $param params of $where
     * @return mixed effective row number|false
     */
    public function update($table, $val, $where, $param = null){
        if(!is_array($val))
            return false;
        if(!preg_match("/^where/i", $where))
            $where = "";
        foreach($val as $k=>$v){
            if(preg_match("/^\\\/", $k))
                $update[] = $this->_addSpecialChar(substr($k,1)) . "=$v";
            else
                $update[] = $this->_addSpecialChar($k) . "='" . $this->_addslashes($v) . "' ";
        }
        $sql = "UPDATE $table SET " . join(" , ", $update) . " $where";
        $this->stm = $this->_query($sql, $param);
        return $this->stm->rowCount();
    }

    private static function _initConfig($dbConfig){
        if(empty($dbConfig))
            throw new DBException("no db config");
        if(empty(self::$_config['params']))
            self::$_config['params'] = array();
        self::$_config = $dbConfig;
    }

    private function _addSpecialChar($val){
        if(self::$_config['dbms'] !== "mysql")
            return $val;
        if('*' == $val ||
            false !== strpos($val,'(') ||
            false !== strpos($val,'.') ||
            false !== stripos($val,'null') ||
            false !== strpos($val,'`')) {
        }else if(false === strpos($val,'`')){
            $val = '`' . trim($val) . '`';
        }
        return $val;
    }

    private function _addslashes($val){
        /*
        if(!class_exists("Sanitize")){
            App::import('Sanitize');
        }
        return Sanitize::clean($val);
        */
        return addslashes($val);
    }

    private function _query($sql, $param = null){
        //fix "General error: 2050" in some mysql version
        $this->free();

        $stm = $this->prepare($sql);
        if($stm === false)
            throw new DBException($sql);
        $ret = is_null($param)?$stm->execute():$stm->execute($param);
        if($ret === false)
            throw new DBException($sql);
        return $stm;
    }
}
class DBException extends Exception{}
?>
