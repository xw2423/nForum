<?php
load("model/board");

/**
 * class Collection is a set of board
 * Section & Favor is Collection
 *
 * @author xw
 */
abstract class Collection{

    private $_info = array();

    abstract public function isRoot();

    /**
     * function getParent get parent collection
     *
     * @return mixed Collection|null
     * @access public
     */
    abstract public function getParent();

    public function isNull(){
        return empty($this->_info);
    }

    /**
     * function getList get the board in collection
     *
     * @return array
     * @access public
     */
    public function getList(){
        $ret = array();
        foreach($this->_info as $v){
            if(!$v->isDir())
                $ret[] = $v;
        }
        return $ret;
    }

    /**
     * function getDir get the dir board in collection
     *
     * @return array
     * @access public
     */
    public function getDir(){
        $ret = array();
        foreach($this->_info as $v){
            if($v->isDir())
                $ret[] = $v;
        }
        return $ret;
    }

    /**
     * function getAll get the set
     *
     * @return array
     * @access public
     */
    public function getAll(){
        return $this->_info;
    }

    /**
     * function __construct
     * the info is a array contain the board
     *
     * @param $info
     * @access protected
     */
    protected function __construct($info){
        if(!is_array($info))
            throw new CollectionNullException();
        $this->_info = array();
        foreach($info as $v){
            $this->_info[] = new Board($v);
        }
    }
}
class CollectionNullException extends Exception{}
