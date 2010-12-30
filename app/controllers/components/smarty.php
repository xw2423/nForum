<?php
/**
 * smarty component for nforum 
 * @author xw       
 */
require("Smarty.class.php");
class SmartyComponent extends Smarty {

    public $controller = null;
    public $hasRendered = false;
    public $plugins = null;
    public $action = null;
    private $_passedVars = array( 'plugins', 'action', 'viewPath', 'viewVars');

    public function initialize(&$controller) {
        $this->controller = $controller;
        $this->caching = false;
        $this->template_dir = VIEWS;
        $this->compile_dir  = TMP . 'compile';
        $this->cache_dir    = CACHE;
        $this->left_delimiter = '<{';
        $this->right_delimiter = '}>';
    }


    public function render(){
    }
}
?>
