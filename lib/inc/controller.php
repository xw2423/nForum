<?php
class NF_Controller extends Yaf_Controller_Abstract{

    //js & css for controller
    public $js = array();
    public $css = array();
    //js for run
    public $jsr = array();

    //normal for nav,special for notice
    public $notice = array();

    //title
    public $title = "";

    //encoding
    public $encoding = "GBK";

    //adapter cake to get request params
    public $params = array();

    protected $_output = '';
    protected $_isRendered = false;
    protected $_initKbs = false;

    public function init(){
        $this->base = $this->getRequest()->getBaseUri();
        $this->encoding = c('application.encoding');

        $this->clearAll();

        //compatiable for cakephp
        $this->params = $this->getRequest()->getParams();
        $this->params['url'] = $this->getRequest()->getQuery();
        $this->params['form'] = array_merge($this->getRequest()->getPost(), $this->getRequest()->getFiles());

        $this->_initKbs();
    }

    public function autoRender($flag = null){
        return Yaf_Dispatcher::getInstance()->autoRender($flag);
    }

    /**
     * get variable of templete
     * @param string $name
     * @return value|null
     */
    public function get($name) {
        return $this->_view->get($name);
    }

    /**
     * assing variable to templete
     * @param mixed $one
     * @param mixed $two
     */
    public function set($one, $two = null) {
        $this->_view->set($one, $two);
    }

    /**
     * clear variable that has set
     * @param $one string or array
     */
    public function clear($one){
        $this->_view->clear($one);
    }

    public function clearAll(){
        $this->_view->clearAll();
    }

    /**
     * add redirect for ajax page
     * using 'location:URL' as text
     * if url is relative, redirect base on DOMAIN.BASE
     *
     * @param string $url
     * @param int $status
     * @param boolean $exit
     */
    public function redirect($url, $status = null, $exit = true){
        if(!$this->getRequest()->front && !preg_match("#^(https|http|ftp|rtsp|mms)://#i", $url)){
            echo "location:$url";
            $this->_stop();
        }
        nforum_redirect($this->base . $url);
    }

    public function error($mixed = null){
        nforum_error($mixed);
    }

    public function error404(){
        nforum_error404();
    }

    /**
     * make cache in respones
     * @param $switch cache or not
     * @param $modified
     * @param $expires
     */
    public function cache($switch = false, $modified = 0, $expires = null){
        nforum_cache($switch, $modified, $expires);
    }

    /**
     * check whehter login or redirect
     * @param $from
     */
    public function requestLogin(){
        if(!NF_Session::getInstance()->isLogin)
            $this->error(ECode::$SYS_NOLOGIN);

        //no cache if need login
        $this->cache(false);
    }

    protected function render($tpl, array $path = null) {
        if($this->_isRendered)
            return $this->_output;
        $this->beforeRender();
        $reset = false;
        if('html' === $this->getRequest()->ext){
            $module = $this->getRequest()->getModuleName();
            $this->_view->setModule($module);
            if('Index' !== $module){
                $this->set('view', VIEW);
                $this->_view->setScriptPath(MODULE . DS . $module . DS . 'views');
                $reset = true;
            }
        }
        if(null === $path)
            $path = strtolower($this->getRequest()->getControllerName()) . DS;
        else
            $path = substr($path[0], 1);
        $this->_output = $this->_view->render($path . $tpl . '.' . c('application.view.ext'));
        if($reset)
            $this->_view->setScriptPath(VIEW);
        $this->_isRendered = true;
        $this->afterRender();
        $this->_output = nforum_iconv('GBK', $this->encoding, $this->_output, 2);

        return $this->_output;
    }

    protected function display($tpl = null, array $params = null) {
        echo $this->render($tpl, $params);
    }

    protected function beforeRender(){
        if(!$this->getRequest()->html){
            $this->_ajaxFix();
            return;
        }

        $site = c('site');

        //handle title
        $title = $site['name'];
        if(!empty($this->title)){
            $title = $this->title;
        }else if(isset($this->notice[0]) &&  $this->getRequest()->url != $site['home']){
            $title .= '-' . $this->notice[0]['text'];
        }else{
            $title .= '-' . $site['desc'];
        }

        //handle notice
        if($this->getRequest()->url != $site['home'])
            $this->notice= array_merge(array(array('url' => $site['home'], 'text' => $site['name'])), $this->notice);

        /* handle normal html page start*/
        if(!$this->getRequest()->spider && !$this->getRequest()->front){
            $tmp = array();
            foreach($this->notice as $v){
                $tmp[] = '<a href="' . (empty($v['url'])?'javascript:void(0)':($this->base.$v['url'])) . '">' . $v['text'] . '</a>';
            }
            $tmp = join('&ensp;>>&ensp;', $tmp);
            $this->jsr[] = <<<EOT
$('#notice_nav').html('{$tmp}');$.setTitle('{$title}');
EOT;

            $syn = c('ubb.syntax');
            if(c('ubb.parse') && !empty($syn) && $this->get('hasSyn') !== false){
                $this->set('syntax', $syn);
            }
        }
        /* handle normal html page end*/

        //handle js & css
        $this->_initAsset();
        //add pack js&css
        $asset_pack = nforum_cache_read('asset_pack');
        if(is_array($asset_pack) && ($this->getRequest()->front || $this->getRequest()->spider)){
            $this->set('js' ,array_merge(array('js/' . $asset_pack['js']), $this->get('js')));
            $this->set('css' ,array_merge(array('css/' . $asset_pack['css']), $this->get('css')));

            $syn = c('ubb.syntax');
            if(c('ubb.parse') && !empty($syn)){
                $this->set('js' ,array_merge($this->get('js'), array($syn . '/scripts/shCore.js', $syn . '/scripts/shAutoloader.js')));
            }
        }

        try{
            $u = User::getInstance();
            $uid = $u->userid;
            $admin = $u->isAdmin();
            $reg = $u->isReg();
        }catch(UserNullException $e){
            $uid = 'guest';
            $admin = false;
            $reg = false;
        }

        if($this->getRequest()->front || $this->getRequest()->spider){
            $this->set(array(
                'refer' => c('refer.enable')
                ,'webTotal' => Forum::getOnlineNum()
                ,'webUser' => Forum::getOnlineUserNum()
                ,'webGuest' => Forum::getOnlineGuestNum()
                ,'keywords' => $site['keywords']
                ,'description' => $site['description']
                ,'home' => $site['home']
                ,'preIndex' => $site['preIndex']
            ));
        }

        //basic variables
        $arr = array(
            'base' => $this->base
            ,'encoding' => $this->encoding
            ,'id' => $uid
            ,'isLogin' => NF_Session::getInstance()->isLogin
            ,'isAdmin' => $admin
            ,'isReg' => $reg
            ,'isFront' => $this->getRequest()->front
            ,'isSpider' => $this->getRequest()->spider
            ,'notice' => $this->notice
            ,'webTitle' => $title
            ,'domain' => $site['domain']
            ,'static' => $site['static']
            ,'siteName' => $site['name']
            ,'jsr' => $this->jsr
        );

        $this->set($arr);
    }

    protected function afterRender(){
        if($this->getRequest()->html){
            if($this->getRequest()->spider){
                if(!$this->getRequest()->front || '/' === $this->getRequest()->url)
                    $this->_output = $this->_view->render('header.tpl') . $this->_output . $this->_view->render('footer.tpl');
            }else if(!$this->getRequest()->front){
                $this->_output = $this->_view->render('css.tpl') . $this->_output . $this->_view->render('script.tpl');
            }
        }
    }

    protected function _stop(){
        exit();
    }

    protected function header($header){
        header($header);
    }

    protected function _initAsset(){
        load('inc/packer');
        $p = new Packer();
        $js_out = $css_out = '';
        $js_pack = c('view.pack.js');
        $css_pack = c('view.pack.css');

        /* handle js*/
        $js_filter = array('js/plupload.min.js');
        //get relative path with WWW
        foreach($this->js as $k=>$v){
            if(!is_numeric($k)){
                foreach($v as $m=>$_v){
                    foreach($_v as $js)
                        $this->js[] = "js/{$m}/{$js}";
                }
                unset($this->js[$k]);
            }else{
                $this->js[$k] = "js/$v";
            }
        }
        //check for direct output
        foreach($this->js as $k=>$v){
            if(!in_array($v, $js_filter)){
                $js_out .= $js_pack?$p->pack(WWW . DS . $v, 'js'):file_get_contents(WWW . DS . $v);
                unset($this->js[$k]);
            }
        }

        if($js_pack){
            foreach($this->js as &$js)
                $js = str_replace("js/", "cjs/", $js);
        }
        /* handle js end*/

        /* handle css*/
        $css_filter = array('css/xwidget.css');
        //get relative path with WWW
        foreach($this->css as $k=>$v){
            if(!is_numeric($k)){
                foreach($v as $m=>$_v){
                    foreach($_v as $js)
                        $this->css[] = "css/{$m}/{$js}";
                }
                unset($this->css[$k]);
            }else{
                $this->css[$k] = "css/$v";
            }
        }
        /**
         * sometime css can output with html to reduce http request
         * but if css has image referrence, the image will hard to find
         * so I keep css file as a link
         */
        //check for direct output
        foreach($this->css as $k=>$v){
            if(!in_array($v, $css_filter)){
                $css_out .= $css_pack?$p->pack(WWW . DS . $v, 'css'):file_get_contents(WWW . DS . $v);
                unset($this->css[$k]);
            }
        }
        if($css_pack){
            foreach($this->css as &$css)
                $css = str_replace("css/", "ccss/", $css);
        }
        /* handle css end*/

        $this->set("js", $this->js);
        $this->set("js_out", $js_out);
        $this->set("css", $this->css);
        $this->set("css_out", $css_out);
    }

    protected function _ajaxFix(){
        $data = $this->get('no_html_data');
        if(null === $data) $data = array();
        //use no_ajax_info param to ignore ajax info,
        if(!isset($data['ajax_st']) && null === $this->get('no_ajax_info')){
            $data['ajax_st'] = 1;
            $data['ajax_code'] = isset($data['ajax_code'])?$data['ajax_code']:ECode::$SYS_AJAXOK;
            $data['ajax_msg'] = ECode::msg($data['ajax_code']);
            //code may be a string,check msg == code to set default code
            if($data['ajax_msg']  == strval($data['ajax_code']))
                $data['ajax_code'] = ECode::$SYS_AJAXOK;
        }
        if(isset($this->params['url']['ajax_redirect'])){
            $data['default'] = $this->params['url']['ajax_redirect'];
            array_splice($data['list'], 0, 0, array(array(
                'text' => rawurldecode($this->params['url']['ajax_title'])
                ,'url' => $this->params['url']['ajax_redirect']
            )));
        }
        $this->set('no_html_data', $data);
    }

    protected function _initKbs(){
        if($this->_initKbs) return;

        //kbs install
        if (BUILD_PHP_EXTENSION==0)
            @dl("libphpbbslib.so");
        chdir(BBS_HOME);
        if (!bbs_ext_initialized())
            bbs_init_ext();

        //load basic
        load(array('model/forum', 'model/user'));

        //init session
        $this->_initSession();
        $this->_initKbs = true;

    }

    protected function _initSession(){
        load('model/session');
        NF_Session::getInstance()->init($this->getRequest()->get('sid', null));
    }
}
