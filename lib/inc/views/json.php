<?php
class JsonView extends NF_View{

    public function setScriptPath($path){
        return;
    }

    public function getScriptPath(){
        return '';
    }

    public function render($tpl, $tpl_vars = array()){
        if(!isset($this->_vars['no_html_data']))
            nforum_error404(true);
        $this->_initHeader();
        load('inc/json');
        return NF_Json::encode(array_merge($this->_vars['no_html_data'], $tpl_vars));
    }

    protected function _initHeader(){
        parent::_initHeader();
        header('Content-Type:application/json;charset=' . c('application.encoding'));
    }
}
