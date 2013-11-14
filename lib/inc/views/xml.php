<?php
class XmlView extends NF_View{

    public function setScriptPath($path){
        return;
    }

    public function getScriptPath(){
        return '';
    }
    /**
     * function render
     * use $this->_vars['no_html_data'|'root']
     *
     * @return string
     * @access public
     */
    public function render($tpl, $tpl_vars = array()){
        if(!isset($this->_vars['no_html_data']))
            nforum_error404(true);
        if(isset($this->_vars['root']) && !empty($this->_vars['root']))
            $root = $this->_vars['root'];
        else
            $root = strtolower($this->_request->getControllerName());

        $this->_initHeader();
        load('inc/xml');
        return NF_Xml::encode($this->_vars['no_html_data'], $root);
    }

    protected function _initHeader(){
        parent::_initHeader();
        header('Content-Type:application/xml;charset=' . c('application.encoding'));
    }
}
