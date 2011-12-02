<?php
/****************************************************
 * FileName: app/vendors/inc/xml.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
/**
 * class ApiView
 * render for json xml 
 *
 * @author xw
 */

class ApiView {
    public $controller = null;
    public $hasRendered = false;
    public $encoding = null;
    private $_passedVars = array('viewVars', 'encoding', 'name');
    private $_out = '';

    public function __construct(&$controller){
        if (is_object($controller)) {
            $count = count($this->_passedVars);
            for ($j = 0; $j < $count; $j++) {
                $var = $this->_passedVars[$j];
                $this->{$var} = $controller->{$var};
            }
        }
    }

    /**
     * render function but the params are not the same as smatry
     * @param string $type json|xml
     * @param $path no use
     * @return string
     * @access public
     */
    public function render($type = null, $path = null){
        if(!$this->hasRendered){
            $this->_out = $this->{'_render_' . $action}();
            if($this->encoding != Configure::read("App.encoding")){
                @$this->_out = iconv(Configure::read("App.encoding"),"{$this->encoding}//IGNORE", $this->_out);
            }
            $this->hasRendered = true;
        }
        return $this->_out;
    }

    private function _render_xml(){
        $data = isset($this->viewVars['data'])?$this->viewVars['data']:'';
        App::import("vendor", "inc/xml");
        if(isset($this->viewVars['root']) && !empty($this->viewVars['root']))
            $root = $this->viewVars['root'];
        else
            $root = strtolower($this->name);
        return BYRXml::encode($data, $root);
    }
    
    private function _render_json(){
        $data = isset($this->viewVars['data'])?$this->viewVars['data']:'';
        App::import("vendor", "inc/json");
        return BYRJSON::encode($data);
    }
}
