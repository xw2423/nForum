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
        $this->controller->html = ($params['html'] !== false);
        unset($params['html']);
        if($this->controller->html){
            if(!$this->controller->RedirectAcl->isRedirect()){
                echo $params['ajax_code'] . ':' . $params['ajax_msg'];
                return;
            }
            $time = Configure::read("redirect.error");
            $this->controller->brief = true;
            $this->controller->css[] = "error.css";
            $this->controller->notice[] = array("url"=>"", "text"=>"错误信息");
            $this->controller->base = Configure::read('site.prefix');
            $this->controller->set($params);
        }else{
            unset($params['html']);
            $this->controller->set('no_html_data', $params);
        }
        $this->_outputMessage('error');
    }

    /**
     * wait page handler
     * @param $param
     */
    public function redirect($params){
        $this->controller->html = ($params['html'] !== false);
        unset($params['html']);
        if($this->controller->html){
            $time = Configure::read("redirect.wait");
            $this->controller->brief = true;
            $this->controller->css[] = "error.css";
            $this->controller->notice[] = array("url"=>"", "text"=>"提示信息");
            if(empty($params['url']['url']))
                $script = "setTimeout(function(){history.go(-1);}, {$params['time']} * 1000);";
            else
                $script = <<<JS
    var url='{$this->controller->base}{$params['url']['url']}',re=[[/&amp;/g,'&'],[/&#37;/g,'%'],[/&lt;/g,'<'],[/&gt;/g,'>'],[/&quot;/g,'"'],[/&#39;/g,'\''],[/&#40;/g,'('],[/&#41;/g,')'],[/&#43;/g,'+'],[/&#45;/g,'-']];for(var i=re.length-1;i>=0;i--)url=url.replace(re[i][0],re[i][1]);setTimeout(function(){window.location=url;},{$time}*1000);
JS;
            $this->controller->jsr[] = $script;
            $this->controller->base = Configure::read('site.prefix');
            $params['time'] = $time;
            $this->controller->set($params);
        }else{
            $this->controller->set('no_html_data', $params);
        }
        $this->_outputMessage('redirect');
    }

    /**
     * error for miss anything
     * @param $param
     */
    public function error404($params){
        if(isset($params['html'])){
            $this->controller->html = ($params['html'] !== false);
            unset($params['html']);
        }
        if($this->controller->html && $this->controller->RedirectAcl->isRedirect()){
            $this->controller->notice[] = array("url"=>"", "text"=>"该页面不存在");
            $this->controller->brief = true;
            $this->controller->base = Configure::read('site.prefix');
            $this->controller->set($params);
        }else{
            $this->controller->header('HTTP/1.0 404 Not Found');
            $this->controller->header('Content-Type:text/html;charset=' . $this->controller->encoding);
            if(!empty($params['code']))
                echo $params['code'] . ':';
            echo $params['msg'];
            $this->controller->_stop();
        }
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
