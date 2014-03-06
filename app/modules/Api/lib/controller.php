<?php
class NF_ApiController extends NF_Controller {

    protected $_abase = "";
    protected $_exts = array('xml' => 'application/xml', 'json'=> 'application/json');

    protected $_method = array();

    public function init(){
        c('application.encoding', 'utf-8');
        $this->cache(false);
        parent::init();
        $this->getRequest()->front = true;
        $this->_abase = c('modules.api.base');

        if($this->getRequest()->ext === 'html'
            && !($this->getRequest()->getControllerName() === 'Attachment' && $this->getRequest()->getActionName() === 'download'))
            exit('Unknow Return Format');
        $this->_checkMethod();
        load('inc/wrapper');
    }

    public function requestLogin(){
        if(!NF_ApiSession::getInstance()->isLogin){
            $this->error(ECode::$SYS_NOLOGIN);
        }
    }

    protected function beforeRender(){
        $this->getRequest()->html = false;
        $this->set('no_html_data', $this->get('data'));
    }

    protected function afterRender(){}

    protected function _initSession(){
        load('api.session');
        try{
            NF_ApiSession::getInstance()->init();
        }catch(LoginException $e){
            $this->error($e->getMessage());
        }
    }

    protected function _checkMethod(){
        $ms = array('post'/*, 'head', 'put', 'options', 'delete'*/);
        $a = $this->getRequest()->getActionName();
        $m = strtolower($this->getRequest()->getMethod());
        if('get' === $m){
            if(!isset($this->_method['get']) || !in_array($a, $this->_method['get'])){
                foreach($ms as $v){
                    if(isset($this->_method[$v]) && in_array($a, $this->_method[$v]))
                        $this->error(ECode::$SYS_REQUESTERROR);
                }
            }
        }else if(!isset($this->_method[$m]) || !in_array($a, $this->_method[$m])){
            $this->error(ECode::$SYS_REQUESTERROR);
        }
    }
}
