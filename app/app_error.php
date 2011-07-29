<?php
/**
 * Application error for nforum
 * error page & director page
 * 
 * @author xw
 */
class AppError extends ErrorHandler {

    /**
     * error page handler
     * @param $param
     */
    public function error($params) {
        $this->controller->brief = true;
        $this->controller->css[] = "error.css";
        $this->controller->notice[] = array("url"=>"", "text"=>"错误信息");
        $this->controller->jsr[] = "setInterval(function(){history.go(-1);}, {$params['time']} * 1000);";
        $this->controller->base = Configure::read('site.prefix');
        $this->controller->set($params);
        $this->_outputMessage('error');    
    }

    /**
     * wait page handler
     * @param $param
     */
    public function redirect($params){
        $this->controller->brief = true;
        $this->controller->css[] = "error.css";
        $this->controller->notice[] = array("url"=>"", "text"=>"提示信息");
        if(empty($params['url']['url']))
            $script = "setInterval(function(){history.go(-1);}, {$params['time']} * 1000);";
        else
            $script = <<<JS
var url='{$this->controller->base}{$params['url']['url']}',re=[[/&amp;/g,'&'],[/&#37;/g,'%'],[/&lt;/g,'<'],[/&gt;/g,'>'],[/&quot;/g,'"'],[/&#39;/g,'\''],[/&#40;/g,'('],[/&#41;/g,')'],[/&#43;/g,'+'],[/&#45;/g,'-']];for(var i=re.length-1;i>=0;i--)url=url.replace(re[i][0],re[i][1]);setInterval(function(){window.location=url;},{$params['time']}*1000);
JS;
        $this->controller->jsr[] = $script;
        $this->controller->base = Configure::read('site.prefix');
        $this->controller->set($params);
        $this->_outputMessage('redirect');    
    }

    /**
     * error for miss anything
     * @param $param
     */
    public function error404($params){
        $this->controller->set($params);
        $this->controller->brief = true;
        $this->controller->base = Configure::read('site.prefix');
        parent::error404($params);
    }

    public function missingController($params){
        $this->error404($params);
    }

    public function missingAction($params){
        $this->error404($params);
    }

    public function privateAction($params){
        $this->error404($params);
    }
}
?>
