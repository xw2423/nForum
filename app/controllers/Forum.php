<?php
/**
 * forum controller for nforum
 *
 * @author xw
 */
class ForumController extends NF_Controller {

    public function frontAction(){
        $this->getRequest()->front = true;

        load('model/adv');
        //load banner
        $this->set('banner_adv', Adv::getBanner());
        //load left
        $this->set('left_adv', Adv::getLeft());

        $site = c('site');
        $cookie = c('cookie');
        $jsr = c('jsr');

        $jsr['domain'] = preg_replace('/^https?:\/\//', '', $site['domain']);
        $jsr['cookie_domain'] = $cookie['domain'];
        $jsr['base'] = $this->base;
        $jsr['prefix'] = $cookie['prefix'];
        $jsr['home'] = $site['home'];
        $jsr['static'] = $site['static'];
        $jsr['uid'] = User::getInstance()->userid;

        load('inc/json');
        $jsr = 'var sys_merge=' . NF_Json::encode($jsr);
        $this->jsr = array_merge(array($jsr), $this->jsr);
    }

    public function indexAction(){
        $this->css[] = "xwidget.css";
        $this->notice = array(array("url"=>c("site.notice.url"), "text"=>c("site.notice.text")));

        load('model/widget');
        $u = User::getInstance();
        $ret = $w = array();
        $ret = Widget::wGet($u);
        if(empty($ret)){
            $this->set("widget", array());
            return;
        }
        $persistent = c("widget.persistent");
        if($persistent){
            $time = (array) nforum_cache_read("widget_time");
            $update = false;
        }
        $w = array_fill(1, ($u->getCustom("userdefine1", 31) == 1)?3:2, array());
        foreach($ret as $v){
            $w[$v['col']][$v['row']] = $v;
            if($persistent){
                try{
                    $ww = Widget::getInstance($v['name']);
                    if(!$ww->wHasPerm(User::getInstance())){
                        $ww = new EWidget('你无权访问此应用');
                        $html = Widget::html($ww->wGetList());
                    }else if(strpos($v['name'], "favor-") === 0){
                        $html = Widget::html($ww->wGetList());
                    }else if(!isset($time[$v['name']]) || $time[$v['name']] < $ww->wGetTime() || false === ($html = nforum_cache_read("widget_" . $v['name']))){
                        $time[$v['name']] = $ww->wGetTime();
                        $html = Widget::html($ww->wGetList());
                        nforum_cache_write("widget_" . $v['name'], $html);
                        $update = true;
                    }
                }catch(WidgetNullException $e){
                    $ww = new EWidget();
                    //if persistent,it will not check wiget time and will not update widget_time.
                    $html = Widget::html($ww->wGetList());
                }
                $w[$v['col']][$v['row']]['content'] = $html;
            }
        }
        if($persistent && $update) nforum_cache_write("widget_time", $time);
        foreach($w as &$v)
            ksort($v);
        $this->set("widget", $w);
        $this->jsr[] = 'SYS.widget.persistent=' . ($persistent?'true':'false');
        $this->jsr[] = "xWidget.init(SESSION.get('is_login'), SESSION.get('id'))";
    }

    public function preAction(){
        $this->getRequest()->front = true;
        if(NF_Session::getInstance()->isLogin)
            $this->redirect(c("site.home"));
        $this->js[] = "forum.pre.js";
        $this->css[] = "preindex.css";

        load('model/adv');
        $this->set("preimg", Adv::getPreImg());
        $this->set("preadv", Adv::getPreAdv());

        $site = c('site');
        $cookie = c('cookie');
        $jsr = c('jsr');

        $jsr['domain'] = preg_replace('/^https?:\/\//', '', $site['domain']);
        $jsr['cookie_domain'] = $cookie['domain'];
        $jsr['base'] = $this->base;
        $jsr['prefix'] = $cookie['prefix'];
        $jsr['home'] = $site['home'];
        $jsr['static'] = $site['static'];
        $jsr['uid'] = User::getInstance()->userid;

        load('inc/json');
        $jsr = 'var sys_merge=' . NF_Json::encode($jsr);
        $this->jsr = array_merge(array($jsr), $this->jsr);
    }

    public function flinkAction(){
        $this->css[] = "flink.css";
        $this->notice[] = array("url"=>"", "text"=>"友情链接");
        $file = BBS_HOME . '/etc/friend_link';
        $plant = $img = array();
        if(file_exists($file)){
            $content = file_get_contents($file);
            $contents = explode("*img*",$content);      //分开文字连接和图片链接
            $plant = explode("\n",$contents[0]);         //获取文字连接
            if(isset($contents[1]))
                $img = explode("\n",$contents[1]);
        }
        foreach($plant as &$v){
            $v = split("[ \r\n\t]+", $v);
        }
        foreach($img as &$v){
            $v = split("[ \r\n\t]+", $v);
        }
        $this->set("plant", $plant);
        $this->set("img", $img);
    }

    public function onlineAction(){
        $this->requestLogin();
        $this->css[] = "mail.css";
        $this->notice[] = array("url"=>"/online", "text"=>"在线用户");

        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;
        try{
            load(array('model/onlineuser', 'inc/pagination'));
            $f = Forum::getOnlineUser();
            $pagination = new Pagination($f, c("pagination.friend"));
            $users = $pagination->getPage($p);
        }catch(FriendNullException $e){
            $this->error();
        }
        if($f->getTotalNum() > 0){
            $info = array();
            foreach($users as $v){
                $info[] = array(
                    "fid" => $v->userid,
                    "from" => $v->userfrom,
                    "mode" => $v->mode,
                    "idle" => sprintf('%02d:%02d',intval($v->idle/60), ($v->idle%60))
                );
            }
            $this->set("friends", $info);
        }
        $link = "{$this->base}/online?p=%page%";
        $this->set("pageBar", $pagination->getPageBar($p, $link));
        $this->set("pagination", $pagination);
        $this->set(array(
            'webTotal' => Forum::getOnlineNum()
            ,'webUser' => Forum::getOnlineUserNum()
            ,'webGuest' => Forum::getOnlineGuestNum()
        ));
    }
}
