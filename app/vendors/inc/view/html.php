<?php
class HtmlView extends AppView{
    
    public $smarty = null;
    protected $_passedVars = array('plugin', 'action', 'viewPath', 'front', 'spider');


    public function __construct(&$controller){
        parent::__construct($controller); 
        if(!$controller->html)
            throw new AppViewException('Unknow Return Format');
        require("Smarty.class.php");
        $this->smarty = new Smarty();
    }

    /**
     * function _render
     * render a tpl via $action and $path, return html
     * $path is base on VIEWS
     *
     * @param string $action
     * @param string $path
     * @return string
     * @access protected
     */
    protected function _render($action = null, $path = null){

        if(is_null($action))
            $action = $this->action;
        if(is_null($path))
            $path = $this->viewPath . DS;
        $viewFileName = $path . $action . ".tpl";
        if($this->plugin && file_exists(APP . "plugins" . DS . $this->plugin . DS . "views" . DS . $viewFileName)){
            $viewFileName = APP . 'plugins' . DS . $this->plugin . DS . "views" . DS . $viewFileName;
        }

        $this->_controller->header('Content-Type:text/html;charset=' . $this->encoding);
        $this->_initSmarty();
        $this->_initFilter();
        $this->_assignVars();
        $out = $this->smarty->fetch($viewFileName);    
        return $out;
    }

    private function _initSmarty(){
        $this->smarty->caching = false;
        $this->smarty->template_dir = VIEWS;
        $this->smarty->compile_dir  = TMP . 'compile';
        $this->smarty->cache_dir    = CACHE;
        $this->smarty->left_delimiter = '<{';
        $this->smarty->right_delimiter = '}>';
        $config = Configure::read("smarty");
        if($config){
            $this->smarty->compile_check = $config['compile_check'];
            $this->smarty->force_compile = $config['force_compile'];
        }

    }

    private function _initFilter(){
        if(true === Configure::read("pack.on")){
            if(method_exists($this->smarty, "register_prefilter"))
                $this->smarty->register_prefilter("smarty_htmlCompress");
            else
                $this->smarty->registerFilter("pre", "smarty_htmlCompress");
        }
    }

    private function _assignVars(){
        $this->smarty->assign($this->viewVars);
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
