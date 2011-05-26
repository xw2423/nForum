<?php

class ApiView {
    public $controller = null;
    public $hasRendered = false;
    public $encoding = null;
    private $_passedVars = array('viewVars', 'encoding', 'name');

    public function __construct(&$controller){
        if (is_object($controller)) {
            $count = count($this->_passedVars);
            for ($j = 0; $j < $count; $j++) {
                $var = $this->_passedVars[$j];
                $this->{$var} = $controller->{$var};
            }
        }
    }

    public function render($action = null, $path = null){
        if($this->hasRendered)
            return "";

        $out = $this->{'_render_' . $action}();
        $this->hasRendered = true;
        if($this->encoding != Configure::read("App.encoding")){
            @$out = iconv(Configure::read("App.encoding"),"{$this->encoding}//IGNORE", $out);
        }
        return $out;
    }

    private function _render_xml(){
        $data = isset($this->viewVars['data'])?$this->viewVars['data']:'';
        App::import("vendor", "api.ApiXml");
        if(isset($this->viewVars['root']) && !empty($this->viewVars['root']))
            $root = $this->viewVars['root'];
        else
            $root = strtolower($this->name);
        return ApiXml::encode($data, $root);
    }
    
    private function _render_json(){
        $data = isset($this->viewVars['data'])?$this->viewVars['data']:'';
        App::import("vendor", "inc/json");
        return BYRJSON::encode($data);
    }
}
