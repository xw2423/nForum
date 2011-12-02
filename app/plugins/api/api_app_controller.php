<?php
class ApiAppController extends AppController {

    public $components = array('api.ApiSession');
    protected $_abase = "";
    protected $_exts = array('xml' => 'application/xml', 'json'=> 'application/json');

    public function __construct(){
        parent::__construct();
        //delete RedirectAcl&ByrSession components
        array_pop($this->components);
        array_pop($this->components);
        $this->encoding = "utf-8";
        if(true === Configure::read('plugins.api.use_domain')){
            Configure::write('plugins.api.base', '');
            Configure::write('site.prefix', '');
        }
    }

    public function beforeFilter(){
        $this->RequestHandler->enabled = false;
        $this->path = str_replace($this->base, '', $this->here);
        $this->base = Configure::read('site.prefix');
        $this->_abase = Configure::read('plugins.api.base');
        if(!in_array($this->params['url']['ext'], array_keys($this->_exts))
            && !($this->params['controller'] === 'attachment' && $this->params['action'] === 'download'))
            $this->error404('Unknow Return Format');
        $this->_initPlugin();
        $this->ApiSession->setFromHost();
        $this->ApiSession->initLogin();
        $this->cache(false);
        App::import('vendor', 'inc/wrapper');
    }

    //no app_controller afterFilter
    public function afterFilter(){}

    public function beforeRender(){
        $this->html = false;
        $this->set('no_html_data', $this->get('data'));
    }

    public function error($code = null){
        $code = is_null($code)?ECode::$SYS_ERROR:$code;
        $req = str_replace($this->_abase, '', $this->path);
        $_error = array('request' => $req, 'code'=> $code, 'msg' => ECode::msg($code));
        $this->set('data', $_error);
        $this->name = 'error';
        echo $this->render();
        $this->_stop();
    }

    public function errorAPI(){
        $this->error404('Unknown API');
    }

    public function error404($text = ''){
        $this->header('HTTP/1.0 404 Not Found');
        $this->header('Content-Type:text/html;charset=' . $this->encoding);
        echo $text;
        $this->_stop();
    }

    public function requestLogin(){
        if(!$this->ApiSession->isLogin){
            $this->error(ECode::$SYS_NOLOGIN);
        }
    }
}
?>
