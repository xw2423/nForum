<?php
class XmlView extends AppView {
    protected $_passedVars = array('name');

    public function __construct(&$controller){
        parent::__construct($controller); 
        if($controller->html)
            throw new AppViewException('wrong render type');
    }

    /**
     * function _render
     * use viewVars['data'|'root'] in controller 
     *
     * @return string
     * @access protected
     */
    protected function _render($action = null, $path = null){
        if(!isset($this->viewVars['no_html_data']))
            throw new AppViewException('no render data');

        if(isset($this->viewVars['root']) && !empty($this->viewVars['root']))
            $root = $this->viewVars['root'];
        else
            $root = strtolower($this->name);

        $this->_controller->header('Content-Type:application/xml;charset=' . $this->encoding);
        App::import("vendor", "inc/xml");
        return BYRXml::encode($this->viewVars['no_html_data'], $root);
    }
}
?>
