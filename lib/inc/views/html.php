<?php
class HtmlView extends NF_View{
    private $_smarty = null;

    public function setScriptPath($path){
        $this->_getSmarty()->template_dir = $path;
    }

    public function getScriptPath(){
        return $this->_getSmarty()->template_dir;
    }

    /**
     * function render
     * render a tpl via $tpl and $tpl_vars, return html
     * $path is base on VIEWS
     *
     * @param string $tpl
     * @param array $tpl_vars
     * @return string
     */
    public function render($tpl, $tpl_vars = array()) {
        $this->_initHeader();
        $smarty = $this->_getSmarty();
        $smarty->assign($this->_vars);
        $smarty->assign($tpl_vars);
        return $smarty->fetch($tpl);
    }

    public function setModule($module){
        $smarty = $this->_getSmarty();
        $smarty->compile_id = $module;
    }

    protected function _initHeader(){
        parent::_initHeader();
        header('Content-Type:text/html;charset=' . c('application.encoding'));
    }

    private function _getSmarty(){
        if(null === $this->_smarty){
            define('SMARTY_RESOURCE_CHAR_SET', 'ISO-8859-1');
            require('Smarty.class.php');
            $this->_smarty = new Smarty();
            $this->_smarty->caching = false;
            $this->_smarty->template_dir = VIEW;
            $this->_smarty->compile_dir  = TMP . '/compile';
            $this->_smarty->cache_dir    = CACHE;
            $this->_smarty->left_delimiter = '<{';
            $this->_smarty->right_delimiter = '}>';
            $c = c('view.smarty');
            $this->_smarty->compile_check = $c['compile_check'];
            $this->_smarty->force_compile = $c['force_compile'];
            $this->_initFilter();
        }
        return $this->_smarty;
    }

    private function _initFilter(){
        if(c('view.pack.html')){
            if(method_exists($this->_getSmarty(), "register_prefilter"))
                $this->_getSmarty()->register_prefilter("smarty_htmlCompress");
            else
                $this->_getSmarty()->registerFilter("pre", "smarty_htmlCompress");
        }
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
