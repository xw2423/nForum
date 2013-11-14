<?php
/**
 * Refer controller for nforum
 *
 * @author xw
 */
load('model/refer');
class ReferController extends NF_Controller {

    public function init(){
        parent::init();
        if(!c('refer.enable'))
            $this->error(ECode::$REFER_DISABLED);
        $this->requestLogin();
        $this->notice[] = array("url"=>"/mail", "text"=>"ÎÄÕÂÌáĞÑ");
    }

    public function indexAction(){
        $this->js[] = "forum.refer.js";
        $this->css[] = "mail.css";

        $type = Refer::$AT;
        $pageBar = "";
        if(isset($this->params['type']))
            $type = $this->params['type'];

        try{
            $refer = new Refer(User::getInstance(), $type);
        }catch(ReferNullException $e){
            $this->error(ECode::$REFER_NONE);
        }
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;

        load("inc/pagination");
        try{
            $pagination = new Pagination($refer, c("pagination.mail"));
            $articles = $pagination->getPage($p);
        }catch(Exception $e){
            $this->error(ECode::$REFER_NONE);
        }
        if($refer->getTotalNum() > 0){
            $info = array();
            foreach($articles as $v){
                $info[] = array(
                    "index" => $v['INDEX'],
                    "id" => $v['ID'],
                    "board" => $v['BOARD'],
                    "user" => $v['USER'],
                    "title" => nforum_html($v['TITLE']),
                    "time" => date("Y-m-d H:i:s", $v['TIME']),
                    "read" => ($v['FLAG'] === Refer::$FLAG_READ)
                );
            }
            $this->set("info", $info);
        }
        $link = "{$this->base}/refer/{$type}?p=%page%";
        $this->set("pageBar", $pagination->getPageBar($p, $link));
        $this->set("pagination", $pagination);
        $this->set("type", $type);
    }

    public function ajax_readAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!isset($this->params['type']))
            $this->error(ECode::$REFER_NONE);
        if(!isset($this->params['form']['index']))
            $this->error(ECode::$REFER_NONE);

        $type = $this->params['type'];
        try{
            $refer = new Refer(User::getInstance(), $type);
        }catch(ReferNullException $e){
            $this->error(ECode::$REFER_NONE);
        }

        if('all' == $this->params['form']['index'])
            $refer->setRead();
        else
            $refer->setRead(intval($this->params['form']['index']));
    }

    public function ajax_deleteAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!isset($this->params['type']))
            $this->error(ECode::$REFER_NONE);

        $type = $this->params['type'];
        try{
            $refer = new Refer(User::getInstance(), $type);
        }catch(ReferNullException $e){
            $this->error(ECode::$REFER_NONE);
        }
        if(isset($this->params['form']['all'])){
            //delete all
            $refer->delete();
        }else{
            //delete normal
            foreach($this->params['form'] as $k=>$v){
                if(!preg_match("/m_/", $k))
                    continue;
                $num = split("_", $k);
                $refer->delete(intval($num[1]));
            }
        }
        $ret['ajax_code'] = ECode::$REFER_DELETEOK;
        $ret['default'] = "/refer/$type";
        $ret['list'][] = array("text" => $refer->getDesc(), "url" => "/refer/$type");
        $this->set('no_html_data', $ret);
    }
}
