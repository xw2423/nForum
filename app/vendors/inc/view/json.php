<?php
class JsonView extends AppView {

    public function __construct(&$controller){
        parent::__construct($controller); 
        if($controller->html)
            throw new AppViewException('wrong render type');
    }

    /**
     * function _render
     * use viewVars['data'] in controller 
     *
     * @return string
     * @access protected
     */
    protected function _render($action = null, $path = null){
        if(!isset($this->viewVars['no_html_data']))
            throw new AppViewException('Unknow Data');

        $this->_controller->header('Content-Type:application/json;charset=' . $this->encoding);
        App::import("vendor", "inc/json");
        return BYRJSON::encode($this->viewVars['no_html_data']);
    }
}
?>
