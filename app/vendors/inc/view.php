<?php
/****************************************************
 * FileName: app/vendors/inc/view.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/

class AppView {

    private static $_views = array();

    protected $_format = 'text/html';

    protected $_out = '';

    protected $_passedVars = array();

    protected $_controller = null;

    public static function getInstance($format = 'html', &$controller = null){
        $format = strtolower($format);

        if(isset(self::$_views[$format]))
            return self::$_views[$format];

        if(!App::import('vendor', "inc/view/{$format}")){
            App::import('vendor', "inc/view/html");
            $format = 'html';
        }
        $class = ucfirst($format) . 'View';

        if(!class_exists($class))
            throw new AppViewException($class . ' cant not be found');
        if(!is_subclass_of($class, __CLASS__))
            throw new AppViewException($class . ' is not a AppView');

        $ins = new $class($controller);
        return (self::$_views[$format] = $ins);

    }
    
    public function __construct(&$controller){
        $this->_passedVars = array_merge($this->_passedVars, array('viewVars', 'encoding'));
        if (is_object($controller)) {
            $count = count($this->_passedVars);
            for ($j = 0; $j < $count; $j++) {
                $var = $this->_passedVars[$j];
                $this->{$var} = $controller->{$var};
            }
            $this->_controller = $controller;
        }
    }

    /**
     * function render
     * call subclass _render
     * check rendered & do encoding convert
     * if using smarty:
     *   render a tpl via $action and $path, return html
     *   $path is base on VIEWS
     * else:
     *   the params is no use 
     *
     * @param string $action
     * @param string $path
     * @return string
     * @access public
     */
    public function render($action = null, $path = null){
        $this->_out = $this->_render($action, $path);
        if($this->encoding != Configure::read("App.encoding")){
            $this->_out = @iconv(Configure::read("App.encoding"),"{$this->encoding}//IGNORE", $this->_out);
        }
        return $this->_out;
    }

    protected function _render(){
        return '';
    }
}

class AppViewException extends Exception{}
?>
