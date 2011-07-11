<?php
/****************************************************
 * FileName: app/vendors/inc/view.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
require("Smarty.class.php");

/**
 * class SmartyView
 * use Smarty instead of cakephp view 
 *
 * @require Smarty       
 * @author xw
 */
class SmartyView extends Smarty {

    public $controller = null;
    public $hasRendered = false;
    public $plugin = null;
    public $action = null;
    public $viewPath = null;
    public $viewVars = null;
    public $encoding = null;
    private $_passedVars = array('plugin', 'action', 'viewPath', 'viewVars', 'encoding');

    public function __construct(&$controller, $config = null){
        parent::__construct();
        if (is_object($controller)) {
            $count = count($this->_passedVars);
            for ($j = 0; $j < $count; $j++) {
                $var = $this->_passedVars[$j];
                $this->{$var} = $controller->{$var};
            }
        }
        $this->_initSmarty($config);
        $this->_initFilter();
    }

    /**
     * function render
     * render a tpl via $action and $path, return html
     * $path is base on VIEWS
     *
     * @param string $action
     * @param string $path
     * @return string
     * @access public
     */
    public function render($action = null, $path = null){
        if($this->hasRendered)
            return "";
        $this->_assignVars();

        if(is_null($action))
            $action = $this->action;
        if(is_null($path))
            $path = $this->viewPath . DS;
        $viewFileName = $path . $action . ".tpl";
        if($this->plugin && file_exists(APP . "plugins" . DS . $this->plugin . DS . "views" . DS . $viewFileName)){
            $viewFileName = APP . 'plugins' . DS . $this->plugin . DS . "views" . DS . $viewFileName;
        }
        $this->hasRendered = true;
        $out = $this->fetch($viewFileName);    
        if($this->encoding != Configure::read("App.encoding")){
            $out = @iconv(Configure::read("App.encoding"),"{$this->encoding}//IGNORE", $out);
        }
        return $out;
    }

    private function _initSmarty($config){
        $this->caching = false;
        $this->template_dir = VIEWS;
        $this->compile_dir  = TMP . 'compile';
        $this->cache_dir    = CACHE;
        $this->left_delimiter = '<{';
        $this->right_delimiter = '}>';
        if(!is_null($config)){
            $this->compile_check = $config['compile_check'];
            $this->force_compile = $config['force_compile'];
        }

    }

    private function _initFilter(){
        if(true === Configure::read("pack.on")){
            if(method_exists(__CLASS__, "register_prefilter"))
                $this->register_prefilter("smarty_htmlCompress");
            else
                $this->registerFilter("pre", "smarty_htmlCompress");
        }
    }

    private function _assignVars(){
        $this->assign($this->viewVars);
    }
}
function smarty_htmlCompress($out, &$smarty){
    $pattern = array(
        "/<!--.*?-->/",
        "/(\s*[\n\r]+\s*)+/"
    );
    $replace = array("", "");
    $out = preg_replace($pattern, $replace, $out);
    return $out;
}
?>
