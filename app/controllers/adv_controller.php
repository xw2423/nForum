<?php
/**
 * Adv controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/adv"));
class AdvController extends AppController {

    private $_type;
    public function beforeFilter(){
        parent::beforeFilter();
        if(!$this->ByrSession->isLogin)
            $this->_stop();
        $u = User::getInstance();
        if(!in_array($u->userid, Configure::read("adv.id")))
            $this->_stop();
        $this->brief = true;
        if(!isset($this->params['type'])
            || !in_array(intval($this->params['type']), range(1, 4)))
            $this->_type = 1;
        else
            $this->_type = intval($this->params['type']);
        $this->front = true;
    }

    public function index(){
        $this->js[] = "forum.adv.js";
        $this->css[] = "adv.css";

        $p = 1;
        if(isset($this->params['url']['p']))
            $p = $this->params['url']['p'];
        $adv = new Adv();
        $adv->type = $this->_type;
        $search = array();
        if(isset($this->params['url']['remark'])
            && trim($this->params['url']['remark']) != ''){
            $search['remark'] = $adv->search = trim($this->params['url']['remark']);

        }
        if(isset($this->params['url']['sTime'])
            && trim($this->params['url']['sTime']) != '')
            $search['sTime'] = $adv->search_start = trim($this->params['url']['sTime']);
        if(isset($this->params['url']['eTime'])
            && trim($this->params['url']['eTime']) != '')
            $search['eTime'] = $adv->search_end = trim($this->params['url']['eTime']);
        App::import('vendor', "inc/pagination");
        $page = new Pagination($adv, 30);
        $res = $page->getPage($p);
        $ret['page'] = $page->getCurPage();
        $ret['total'] = $adv->getTotalNum();
        $ret['aPath'] = Configure::read("adv.path");
        foreach($res as $v){
            $ret['info'][] = $v;
        }
        $this->set($ret);
        $this->set($search);
        $this->set("dir", Configure::read('adv.path'));
        $this->set("type", ($this->_type == 1 || $this->_type == 2)?true:false);
        $this->set("advType", $this->_type);
        $this->set("hasPrivilege", isset($res[0]['privilege']) && '1' == $res[0]['privilege']);

        $query = '';
        foreach($search as $k=>$v){
            $query .= '&' . $k . '=' . $v;
        }
        $this->set("pageBar", $page->getPageBar($p, "?p=%page%" . $query));
        $this->set("pagination", $page);
    }

    public function advSet(){
        $p = 1;
        if(isset($this->params['form']['p']))
            $p = $this->params['form']['p'];

        $url = $sTime = $eTime = $remark = "";
        $privilege = $weight = $switch = 0;
        if(!isset($this->params['form']['aid']))
            $this->redirect("/adv/{$this->_type}?p=$p");
        $aid = $this->params['form']['aid'];
        if(isset($this->params['form']['url']))
            $url = $this->params['form']['url'];
        if(isset($this->params['form']['sTime']))
            $sTime = $this->params['form']['sTime'];
        if(isset($this->params['form']['eTime']))
            $eTime = $this->params['form']['eTime'];
        if(isset($this->params['form']['privilege']))
            $privilege = 1;
        if(isset($this->params['form']['switch']))
            $switch = 1;
        if(isset($this->params['form']['weight']))
            $weight = $this->params['form']['weight'];
        if(isset($this->params['form']['remark']))
            $remark = $this->params['form']['remark'];
        $adv = new Adv();
        $adv->type = $this->_type;
        $adv->update($aid, $url, $sTime, $eTime, $switch, $weight, $privilege, $remark);

        $this->redirect("/adv/{$this->_type}?p=$p");
    }

    public function advDel(){
        $p = 1;
        if(isset($this->params['form']['p']))
            $p = $this->params['form']['p'];

        if(!isset($this->params['form']['aid']))
            $this->redirect("/adv/{$this->_type}?p=$p");
        $aid = $this->params['form']['aid'];
        $adv = new Adv();
        $adv->type = $this->_type;
        $file = $adv->delete($aid);
        @unlink(WWW_ROOT . Configure::read('adv.path') . DS . $file);
        $this->redirect("/adv/{$this->_type}?p=$p");
    }

    public function advAdd(){
        $p = 1;
        if(isset($this->params['form']['p']))
            $p = $this->params['form']['p'];

        $url = $sTime = $eTime = $remark = "";
        $privilege = $weight = $switch = 0;
        if (isset($this->params['form']['img'])) {
            $errno = $this->params['form']['img']['error'];
        } else {
            $errno = UPLOAD_ERR_PARTIAL;
        }
        switch($errno){
            case UPLOAD_ERR_OK:
                $tmpFile = $this->params['form']['img']['tmp_name'];
                $tmpName = $this->params['form']['img']['name'];
                if (!is_uploaded_file($tmpFile)) {
                    $this->redirect("/adv/{$this->_type}?p=$p");
                }
                $ext = strrchr($tmpName, '.');
                $file = date("Y-m-d-H-i-s", time()) . $ext;
                $dir = Configure::read('adv.path');
                $path = $dir . DS . $file;
                $fullDir = WWW_ROOT . $dir;
                $fullPath = WWW_ROOT . $path;
                if(!is_dir($fullDir)){
                    @mkdir($fullDir);
                }
                if(is_file($fullPath)){
                    $this->redirect("/adv/{$this->_type}?p=$p");
                }
                $imgInf = @getimagesize($tmpFile);
                if($imgInf === false){
                    $this->redirect("/adv/{$this->_type}?p=$p");
                }
                if(!in_array($imgInf[2], range(1, 3))){
                    $this->redirect("/adv/{$this->_type}?p=$p");
                }
                if (!move_uploaded_file($tmpFile, $fullPath)) {
                    $this->redirect("/adv/{$this->_type}?p=$p");
                }
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
            case UPLOAD_ERR_PARTIAL:
            case UPLOAD_ERR_NO_FILE:
                $this->redirect("/adv/{$this->_type}?p=$p");
            default:
                $this->redirect("/adv/{$this->_type}?p=$p");
        }
        if(isset($this->params['form']['url']))
            $url = $this->params['form']['url'];
        if(isset($this->params['form']['sTime']))
            $sTime = $this->params['form']['sTime'];
        if(isset($this->params['form']['eTime']))
            $eTime = $this->params['form']['eTime'];
        if(isset($this->params['form']['privilege']))
            $privilege = 1;
        if(isset($this->params['form']['switch']))
            $switch = 1;
        if(isset($this->params['form']['weight']))
            $weight = $this->params['form']['weight'];
        if(isset($this->params['form']['remark']))
            $remark = $this->params['form']['remark'];
        $adv = new Adv();
        $adv->type = $this->_type;
        $adv->add($this->_type, $file, $url, $sTime, $eTime, $switch, $weight, $privilege, $remark);

        $this->redirect("/adv/{$this->_type}?p=$p");
    }
}
?>
