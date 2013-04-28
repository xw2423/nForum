<?php
/**
 * Board controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/section", "model/board", "model/threads", "inc/pagination"));
class BoardController extends AppController {

    public $components = array("Cookie");

    private $_board;

    public function beforeFilter(){
        parent::beforeFilter();
        if(!isset($this->params['name'])){
            $this->error(ECode::$BOARD_NONE);
        }

        try{
            $boardName = $this->params['name'];
            if(preg_match("/^\d+$/", $boardName))
                throw new BoardNullException();
            $this->_board = Board::getInstance($boardName);
            if($this->_board->isDir())
                throw new BoardNullException();
        }catch(BoardNullException $e){
            $this->error(ECode::$BOARD_UNKNOW);
        }

        if(isset($this->params['mode'])){
            $mode = (int)trim($this->params['mode']);
            if(!$this->_board->setMode($mode))
                $this->error(ECode::$BOARD_NOPERM);
        }
        if(!$this->_board->hasReadPerm(User::getInstance())){
            if(!$this->ByrSession->isLogin)
                $this->requestLogin();
            $this->error(ECode::$BOARD_NOPERM);
        }
        $this->_board->setOnBoard();
        if($this->_board->getMode() != $this->Cookie->read('BMODE'))
            $this->Cookie->write('BMODE', $this->_board->getMode(), false);
    }

    public function index(){
        $this->js[] = "forum.board.js";
        $this->css[] = "board.css";
        $this->_getNotice();
        $this->notice[] = array("url"=>"", "text"=>"文章列表");
        $this->cache(false);

        App::import('Sanitize');
        $pageBar = "";
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;
        $pagination = new Pagination($this->_board, Configure::read("pagination.threads"));
        $threads = $pagination->getPage($p);
        $u = User::getInstance();
        if($bm = $u->isBM($this->_board) || $u->isAdmin())
            $this->js[] = "forum.manage.js";
        $info = false;
        $curTime = strtotime(date("Y-m-d", time()));
        $pageArticle = Configure::read("pagination.article");
        foreach($threads as $v){
            $page = ceil($v->articleNum / $pageArticle);
            $last = $v->LAST;
            $postTime = ($curTime > $v->POSTTIME)?date("Y-m-d", $v->POSTTIME):(date("H:i:s", $v->POSTTIME)."&emsp;");
            $replyTime = ($curTime > $last->POSTTIME)?date("Y-m-d", $last->POSTTIME):(date("H:i:s", $last->POSTTIME)."&emsp;");
            $info[] = array(
                "tag" => $this->_getTag($v),
                "title" => Sanitize::html($v->TITLE),
                "poster" => $v->isSubject()?$v->OWNER:"原帖已删除",
                "postTime" => $postTime,
                "gid" => $v->ID,
                "last" => $last->OWNER,
                "replyTime" => $replyTime,
                "num" => $v->articleNum - 1,
                "page" => $page,
                "att" => $v->hasAttach()
            );
        }
        $this->title = Configure::read('site.name').'-'.$this->_board->DESC;
        $this->set("info", $info);
        $link = "{$this->base}/board/{$this->_board->NAME}?p=%page%";
        $this->set("pageBar", $pagination->getPageBar($p, $link));
        $this->set("pagination", $pagination);

        $bms = split(" ", $this->_board->BM);
        foreach($bms as &$v){
            if(preg_match("/[^0-9a-zA-Z]/", $v)){
                $v = array($v, false);
            }else{
                $v = array($v, true);
            }
        }

        $this->set("todayNum", $this->_board->getTodayNum());
        $this->set("curNum", $this->_board->CURRENTUSERS);
        if(isset($this->_board->MAXONLINE)){
            $this->set("maxNum", $this->_board->MAXONLINE);
            $this->set("maxTime", date("Y-m-d H:i:s", $this->_board->MAXTIME));
        }
        $this->set("bms", $bms);
        $this->set("bName", $this->_board->NAME);
        $this->set("bm", $bm);
        $this->set("tmpl", $this->_board->isTmplPost());
        $this->set("hasVote", count($this->_board->getVotes()) != 0);
        //for default search day
        $this->set("searchDay", Configure::read("search.day"));
        //for elite path
        $this->set("elitePath", urlencode($this->_board->getElitePath()));
        $this->jsr[] = "window.user_post=" . ($this->_board->hasPostPerm($u) && !$this->_board->isDeny($u)?"true":"false") . ";";

    }

    public function mode(){
        $this->js[] = "forum.board.js";
        $this->css[] = "board.css";
        $this->_getNotice();
        switch($this->params['mode']){
            case BOARD::$NORMAL:
                $tmp = '经典模式';
                break;
            case BOARD::$DIGEST:
                $tmp = '文摘模式';
                break;
            case BOARD::$MARK:
                $tmp = '保留模式';
                break;
            case BOARD::$DELETED:
                $tmp = '回收模式';
                break;
            case BOARD::$JUNK:
                $tmp = '纸篓模式';
                break;
            case BOARD::$ORIGIN:
                $tmp = '原作模式';
                break;
            default:
                $tmp = '主题模式';
        }
        $this->notice[] = array("url"=>"", "text"=>$tmp);
        $this->cache(false);

        App::import('Sanitize');
        $pageBar = "";
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;
        $pagination = new Pagination($this->_board, Configure::read("pagination.threads"));
        $articles = $pagination->getPage($p);
        $u = User::getInstance();
        if($bm = $u->isBM($this->_board) || $u->isAdmin())
            $this->js[] = "forum.manage.js";
        $info = false;
        $curTime = strtotime(date("Y-m-d", time()));
        $sort = $this->_board->isSortMode();
        foreach($articles as $v){
            $postTime = ($curTime > $v->POSTTIME)?date("Y-m-d", $v->POSTTIME):(date("H:i:s", $v->POSTTIME)."&emsp;");
            $info[] = array(
                "tag" => $this->_getTag($v),
                "title" => Sanitize::html($v->TITLE),
                "poster" => $v->OWNER,
                "postTime" => $postTime,
                "id" => $sort?$v->ID:$v->getPos(),
                "gid" => $v->GROUPID,
                "att" => $v->hasAttach()
            );
        }
        $this->title = Configure::read('site.name').'-'.$this->_board->DESC;
        $this->set("info", $info);
        $link = "{$this->base}/board/{$this->_board->NAME}/mode/{$this->params['mode']}?p=%page%";
        $this->set("pageBar", $pagination->getPageBar($p, $link));
        $this->set("pagination", $pagination);

        $bms = split(" ", $this->_board->BM);
        foreach($bms as &$v){
            if(preg_match("/[^0-9a-zA-Z]/", $v)){
                $v = array($v, false);
            }else{
                $v = array($v, true);
            }
        }

        $this->set("todayNum", $this->_board->getTodayNum());
        $this->set("curNum", $this->_board->CURRENTUSERS);
        if(isset($this->_board->MAXONLINE)){
            $this->set("maxNum", $this->_board->MAXONLINE);
            $this->set("maxTime", date("Y-m-d H:i:s", $this->_board->MAXTIME));
        }
        $this->set("bms", $bms);
        $this->set("bName", $this->_board->NAME);
        $this->set("bm", $u->isBM($this->_board));
        $this->set("tmpl", $this->_board->isTmplPost());
        $this->set("hasVote", count($this->_board->getVotes()) != 0);
        $this->set("mode", (int)$this->params['mode']);
        //for default search day
        $this->set("searchDay", Configure::read("search.day"));
        //for elite path
        $this->set("elitePath", urlencode($this->_board->getElitePath()));
        $this->jsr[] = "window.user_post=" . ($this->_board->hasPostPerm($u) && !$this->_board->isDeny($u)?"true":"false") . ";";
    }

    public function vote(){
        $this->requestLogin();
        $this->_getNotice();
        $this->notice[] = array("url"=>"", "text"=>"投票");
        App::import('Sanitize');
        if(isset($this->params['num'])){
            $num = (int) $this->params['num'];
            $vote = $this->_board->getVote($num);
            if($vote === false)
                $this->error();
            $vote['start'] = date('Y-m-d H:i:s', $vote['start']);
            $vote['day'] .= '天';
            $vote['title'] = Sanitize::html($vote['title']);
            if(is_array($vote['val'])){
                foreach($vote['val'] as &$v)
                    $v = Sanitize::html($v);
            }
            $this->set($vote);
            $this->set("num", $num);
            $this->set("bName", $this->_board->NAME);
            $this->render("vote_que");
            return;
        }
        $votes = $this->_board->getVotes();
        $info = array();
        foreach($votes as $k=>$v){
            $info[$k]['owner'] = $v['USERID'];
            $info[$k]['title'] = Sanitize::html($v['TITLE']);
            $info[$k]['start'] = date('Y-m-d H:i:s', $v['DATE']);
            $info[$k]['type'] = $v['TYPE'];
            $info[$k]['day'] = $v['MAXDAY'].'天';
        }
        $this->set("info", $info);
        $this->set("bName", $this->_board->NAME);
    }

    public function ajax_vote(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();

        if(!isset($this->params['num']))
            $this->error(ECode::$BOARD_VOTEFAIL);

        $num = (int) $this->params['num'];
        $vote = $this->_board->getVote($num);
        if($vote === false)
            $this->error(ECode::$BOARD_VOTEFAIL);

        $v = @$this->params['form']['v'];
        $msg = @$this->params['form']['msg'];
        $msg = nforum_iconv('utf-8', $this->encoding, $msg);
        $val1 = $val2 = 0;
        if($vote['type'] == '数字'){
            $val1 = (int)$v;
        }else if($vote['type'] == '复选'){
            if(count((array)$v) > $vote['limit'])
                $this->error(ECode::$BOARD_VOTEFAIL);
            foreach((array)$v as $k=>$v){
                if($k < 32)
                    $val1 += 1 << $k;
                else
                    $val2 += 1 << ($k - 32);
            }
        }else if($vote['type'] != '问答'){
            $v = intval($v);
            if($v < 32)
                $val1 = 1 << $v;
            else
                $val2 = 1 << ($v - 32);
        }
        if(!$this->_board->vote($num, $val1, $val2, $msg))
            $this->error(ECode::$BOARD_VOTEFAIL);

        $ret['ajax_code'] = ECode::$BOARD_VOTESUCCESS;
        $ret['default'] = '/board/' .  $this->_board->NAME;
        $mode = $this->Cookie->read('BMODE');
        if($mode != null && $mode != BOARD::$THREAD) $ret['default'] .= '/mode/' . $this->Cookie->read('BMODE');
        $ret['list'][] = array("text" => '版面:' . $this->_board->DESC, "url" => $ret['default']);
        $ret['list'][] = array("text" => '投票列表', "url" => '/board/' .  $this->_board->NAME . '/vote/');
        $ret['list'][] = array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"));
        $this->set('no_html_data', $ret);
    }

    public function denylist(){
        $this->cache(false);
        $this->requestLogin();
        $u = User::getInstance();
        try {
            $ret = $this->_board->getDeny();
        } catch(BoardDenyException $e) {
            $this->error($e->getMessage());
        }
        $this->_getNotice();
        $this->notice[] = array("url"=>"", "text"=>"封禁名单");
        $this->title = Configure::read('site.name').'-'.$this->_board->DESC;
        $this->js[] = "forum.board.js";
        $this->css[] = "board.css";
        $this->set('bName', $this->_board->NAME);
        $this->set('data', $ret);
        $this->set('maxday', $u->isAdmin()?70:14);
    }

    public function ajax_adddeny(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        $u = User::getInstance();
        if (!isset($this->params['form']['id']))
            $this->error(ECode::$DENY_NOID);
        if (!isset($this->params['form']['reason']))
            $this->error(ECode::$DENY_NOREASON);
        if (!isset($this->params['form']['day']))
            $this->error(ECode::$DENY_INVALIDDAY);
        $id = $this->params['form']['id'];
        $reason = nforum_iconv('utf-8', $this->encoding, $this->params['form']['reason']);
        $day = intval($this->params['form']['day']);
        if ($day < 1)
            $this->error(ECode::$DENY_INVALIDDAY);
        try {
            $this->_board->addDeny($id, $reason, $day);
        } catch (BoardDenyException $e) {
            $this->error($e->getMessage());
        }
        $ret['ajax_code'] = ECode::$SYS_AJAXOK;
        $ret['default'] = '/board/' . $this->_board->NAME . '/denylist';
        $ret['list'][] = array('text' => '版面封禁列表:' . $this->_board->DESC, 'url' => '/board/' . $this->_board->NAME . '/denylist');
        $ret['list'][] = array('text' => '版面:' . $this->_board->DESC, 'url' => '/board/' . $this->_board->NAME);
        $ret['list'][] = array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"));
        $this->set('no_html_data', $ret);
    }

    public function ajax_moddeny(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        $u = User::getInstance();
        if (!isset($this->params['form']['id']))
            $this->error(ECode::$DENY_NOID);
        if (!isset($this->params['form']['reason']))
            $this->error(ECode::$DENY_NOREASON);
        if (!isset($this->params['form']['day']))
            $this->error(ECode::$DENY_INVALIDDAY);
        $id = $this->params['form']['id'];
        $reason = nforum_iconv('utf-8', $this->encoding, $this->params['form']['reason']);
        $day = intval($this->params['form']['day']);
        if ($day < 1)
            $this->error(ECode::$DENY_INVALIDDAY);
        try {
            $this->_board->modDeny($id, $reason, $day);
        } catch (BoardDenyException $e) {
            $this->error($e->getMessage());
        }
        $ret['ajax_code'] = ECode::$SYS_AJAXOK;
        $ret['default'] = '/board/' . $this->_board->NAME . '/denylist';
        $ret['list'][] = array('text' => '版面封禁列表:' . $this->_board->DESC, 'url' => '/board/' . $this->_board->NAME . '/denylist');
        $ret['list'][] = array('text' => '版面:' . $this->_board->DESC, 'url' => '/board/' . $this->_board->NAME);
        $ret['list'][] = array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"));
        $this->set('no_html_data', $ret);
    }

    public function ajax_deldeny(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        $u = User::getInstance();
        if (!isset($this->params['form']['id']))
            $this->error(ECode::$DENY_NOID);
        $id = $this->params['form']['id'];
        try {
            $this->_board->delDeny($id);
        } catch (BoardDenyException $e) {
            $this->error($e->getMessage());
        }
        $ret['ajax_code'] = ECode::$SYS_AJAXOK;
        $ret['default'] = '/board/' . $this->_board->NAME . '/denylist';
        $ret['list'][] = array('text' => '版面封禁列表:' . $this->_board->DESC, 'url' => '/board/' . $this->_board->NAME . '/denylist');
        $ret['list'][] = array('text' => '版面:' . $this->_board->DESC, 'url' => '/board/' . $this->_board->NAME);
        $ret['list'][] = array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"));
        $this->set('no_html_data', $ret);
    }

    public function ajax_denyreasons() {
        $this->cache(false);
        $u = User::getInstance();
        $ret = array();
        if ($u->isBM($this->_board) || $u->isAdmin())
            $ret = $this->_board->getDenyReasons();
        $this->set('no_ajax_info', true);
        $this->set('no_html_data', $ret);
    }

    private function _getTag($threads){
        if($threads->isTop()){
            return "T";
        }
        if($threads->isB()){
            return "B";
        }
        if($threads->isM()){
            return "M";
        }
        if($threads->isNoRe()){
            return ";";
        }
        if($threads->isG()){
            return "G";
        }
        if($threads->articleNum > 1000){
            return "L3";
        }
        if($threads->articleNum > 100){
            return "L2";
        }
        if($threads->articleNum > 10)
            return "L";
        return "N";
    }

    private function _getNotice(){
        $root = Configure::read("section.{$this->_board->SECNUM}");
        $this->notice[] = array("url"=>"/section/{$this->_board->SECNUM}", "text"=>$root[0]);
        $boards = array(); $tmp = $this->_board;
        while(!is_null($tmp = $tmp->getDir())){
            $boards[] = array("url"=>"/section/{$tmp->NAME}", "text"=>$tmp->DESC);
        }
        foreach($boards as $v)
            $this->notice[] = $v;
        $url = "/board/{$this->_board->NAME}";
        $mode = $this->Cookie->read('BMODE');
        if($mode != null && $mode != BOARD::$THREAD) $url .= '/mode/' . $this->Cookie->read('BMODE');
        $this->notice[] = array("url"=>$url, "text"=>$this->_board->DESC);
    }
}
?>
