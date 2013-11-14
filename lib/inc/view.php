<?php
class NF_View implements Yaf_View_Interface{

    private static $_views = array();

    protected $_vars = array();

    protected $_request = null;

    protected $_initHeader = false;

    public static function getInstance($request){
        $ext = $request->ext;
        if(isset(self::$_views[$ext]))
            return self::$_views[$ext];

        if(!load("inc/views/{$ext}"))
            exit();
        $class = ucfirst($ext) . 'View';

        if(!class_exists($class))
            exit();
        if(!is_subclass_of($class, __CLASS__))
            exit();

        return (self::$_views[$ext] = new $class($request));
    }

    public function __construct($request){
        $this->_request = $request;
    }

    public function render($tpl, $tpl_vars = array()){
        return '';
    }

    public function display($tpl, $tpl_vars = null){
        echo $this->render($tpl, $tpl_vars);
    }

    public function assign($name, $value = null){
        return $this->set($name, $value);
    }


    /**
     * assing variable to templete
     * @param $one string or array
     * @param $two mixed
     */
    public function set($one, $two = null) {
        if(is_array($one) && is_null($two)){
            foreach($one as $k => $v){
                $this->_vars[$k] = $v;
            }
        }
        if(is_string($one) && !is_null($two)){
            $this->_vars[$one] = $two;
        }
    }

    /**
     * get variable of templete
     * @param $name string
     * @return value|null
     */
    public function get($name = null) {
        return isset($this->_vars[$name])?$this->_vars[$name]:null;
    }

    public function clear($one){
        if(is_array($one)){
            foreach($one as $k => $v){
                unset($this->_vars[$k]);
            }
        }
        if(is_string($one)){
            unset($this->_vars[$one]);
        }
    }

    public function clearAll(){
        $this->_vars = array();
    }

    public function getScriptPath(){ return '';}
    public function setScriptPath($dir){}

    protected function _initHeader(){
        if($this->_initHeader) return;
        $this->_initHeader = true;
    }
}
