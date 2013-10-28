<?php
App::import("vendor", array("inc/db"));
App::import("vendor", "vote.vote");
class IndexController extends VoteAppController {
    private $_board = "nVote";

    //new vote per day
    private $_newVoteMax = 2;

    //show comment number
    private $_commentNum = 10;

    //max item for new vote
    private $_itemMax = 20;

    public function beforeFilter(){
        parent::beforeFilter();
        $this->notice[] = array("url" => "/vote", "text" => "投票");
    }
    public function index(){
        $this->css[] = "xwidget.css";
        $this->css['plugin']['vote'][] = "vote.css";
        $this->js['plugin']['vote'][] = "vote.js";
        $this->cache(false);
        @$category = $this->params['url']['c'];
        $u = User::getInstance();
        $time = time();
        $yes = $time - 86400;
        $search = '';
        $params = array();
        if(isset($this->params['url']['s'])){
            $search = trim(rawurldecode($this->params['url']['s']));
            $search = nforum_iconv('utf-8', $this->encoding, $search);
            if('' != $search){
                App::import('Sanitize');
                $this->set('search', Sanitize::html($search));
                $search = addslashes($search);
                $search = " and (uid like '%$search%' or subject like '%$search%' or `desc` like '%$search%')";
            }
        }
        switch($category){
            case 'hot':
                $title = "热门投票";
                $sql = "select * from pl_vote where status=1 and end>? $search order by num desc, vid desc";
                $params = array($yes);
                break;
            case 'list':
                @$user = trim($this->params['url']['u']);
                App::import('Sanitize');
                $title = ($user === $u->userid)?"我的投票":Sanitize::html("{$user}的投票");
                $this->set("vote_user", Sanitize::html($user));
                $sql = "select * from pl_vote where status=1 and uid=? $search order by vid desc";
                $params = array($user);
                break;
            case 'all':
                $title = "全部投票";
                $sql = "select * from pl_vote where status=1 $search order by vid desc";
                break;
            case 'join':
                $this->requestLogin();
                $title = "我参与的投票";
                $sql = "select * from pl_vote where status=1 and vid in (select vid from pl_vote_result where uid=?) $search order by vid desc";
                $params = array($u->userid);
                break;
            case 'del':
                if(!$u->isAdmin())
                    $this->error('你无权查看此类投票');
                $title = "已删除的投票";
                $sql = "select * from pl_vote where status=0 $search order by vid desc";
                break;
            default:
                $title = "最新投票";
                $category = "new";
                $sql = "select * from pl_vote where status=1 and end>? $search order by vid desc";
                $params = array($yes);
        }
        $list = new VoteList($sql, $params);
        App::import("vendor", "inc/pagination");
        $page = new Pagination($list, 10);
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;
        $res = $page->getPage($p);
        if(count($res) == 0){
            $info = false;
        }else{
            $info = array();
            App::import('Sanitize');
            foreach($res as $v){
                $info[] = array(
                    "vid" =>$v->vid,
                    "title" =>Sanitize::html($v->subject),
                    "start" =>date("Y-m-d H:i:s", $v->start),
                    "end" =>date("Y-m-d", $v->end),
                    "num" =>$v->num,
                    "isEnd" =>$v->isEnd(),
                    "isDel" =>$v->isDeleted(),
                    "admin"=>$u->userid === $v->uid || $u->isAdmin(),
                    "uid" =>$v->uid
                );
            }
        }
        $query = $this->params['url'];
        unset($query['url']);
        unset($query['p']);
        unset($query['ext']);
        foreach($query as $k=>&$v)
            $v = $k . '=' . rawurlencode($v);
        $query[] = "p=%page%";
        $link = "{$this->base}/vote?". join("&", $query);

        $pageBar = $page->getPageBar($p, $link);
        $this->set("pageBar", $pageBar);
        $this->set("totalNum", $list->getTotalNum());
        $this->set("info", $info);
        $this->set("category", $category);
        $this->set("voteTitle", $title);
        $this->notice[] = array("url" => "", "text" => $title);

        /* right rank*/
        $week = nforum_cache_read("vote_week");
        $month = nforum_cache_read("vote_month");
        $year = nforum_cache_read("vote_year");
        $this->set("week", $week);
        $this->set("month", $month);
        $this->set("year", $year);
        /* right rank end*/
    }

    public function add(){
        $this->requestLogin();
        $this->css['plugin']['vote'][] = "vote.css";
        $this->js['plugin']['vote'][] = "vote.js";
        $this->notice[] = array("url" => "", "text" => "新投票");
        $this->cache(false);

        $u = User::getInstance();
        $db = DB::getInstance();
        if(!$u->isAdmin()){
            $sql = "select count(*) as num from pl_vote where status=1 and start>=? and uid=?";
            $res = $db->one($sql, array(strtotime(date("Y-m-d",time())), $u->userid));
            if($res !== false && $res['num'] >= $this->_newVoteMax)
                $this->error("每天你最多开启{$this->_newVoteMax}次投票");
        }
        for($i = 2;$i<=$this->_itemMax - 1; $i++)
            $limit[$i] = $i;
        $this->set('limit', $limit);
        $this->set('itemMax', $this->_itemMax);

        $secs = Configure::read("section");
        foreach($secs as $k=>&$v){
            $v = $k . "区:" . $v[0];
        }
        $this->set("sec", $secs);
        $this->set("selected", 0);
    }

    public function ajax_add(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        $this->requestLogin();
        $db = DB::getInstance();
        $u = User::getInstance();
        if(!$u->isAdmin()){
            $sql = "select count(*) as num from pl_vote where status=1 and start>=? and uid=?";
            $res = $db->one($sql, array(strtotime(date("Y-m-d",time())), $u->userid));
            if($res !== false && $res['num'] >= $this->_newVoteMax)
                $this->error("每天你最多开启{$this->_newVoteMax}次投票");
        }
        $subject = @trim($this->params['form']['subject']);
        $desc = @trim($this->params['form']['desc']);
        $end = @trim($this->params['form']['end']);
        $type = @trim($this->params['form']['type']);
        $limit = @trim($this->params['form']['limit']);
        $result_voted = isset($this->params['form']['result_voted'])?1:0;

        if(empty($subject) || empty($end))
            $this->error();
        if($type != "0" && $type != "1")
            $type = 0;
        if(empty($limit) || intval($limit) < 2 || intval($limit) > $this->_itemMax - 1)
            $limit = 0;
        if(strtotime($end) === false || !preg_match("/\d{4}(-\d{2}){2}/", $end))
            $this->error("截止日期错误");
        $items = array();
        foreach($this->params['form'] as $k=>$v){
            if(preg_match('/^i\d+$/', $k) && trim($v) != "")
                $items[] = nforum_iconv('UTF-8', $this->encoding, trim($v));
        }
        $realNum = count($items);
        if($realNum < 2 || $realNum > $this->_itemMax)
            $this->error("选项数量错误，发起投票失败");
        if($limit > $realNum)
            $limit = $realNum;
        $subject = nforum_iconv('UTF-8', $this->encoding, $subject);
        $desc = nforum_iconv('UTF-8', $this->encoding, $desc);
        $vid = Vote::add($u->userid, $subject, $desc, strtotime($end), $type, $limit, $items, $result_voted);
        $site = Configure::read("site");
        $a_title = $subject;
        $a_content = "主题:$subject\n描述:$desc\n发起人:{$u->userid}\n类型:".(($type==0)?'单选':'多选')."\n截止日期:$end\n链接:[url={$site['domain']}{$site['prefix']}/vote/view/$vid]{$site['domain']}{$site['prefix']}/vote/view/{$vid}[/url]\n[vote=$vid][/vote]";
        App::import("vendor", "model/article");
        $aid = Article::autoPost($this->_board, $a_title, $a_content);
        $db->update("pl_vote", array("aid"=>$aid), "where vid=?", array($vid));

        if(isset($this->params['form']['b'])){
            App::import("vendor", "model/board");
            try{
                $board = Board::getInstance(trim($this->params['form']['b']));
                if($board->hasPostPerm($u)){
                    Article::autoPost($board->NAME, '[投票]' . $a_title, $a_content);
                }
            }catch(Exception $e){}
        }
        $ret['ajax_code'] = "发起投票成功";
        $ret['default'] = "/vote?c=list&u=".$u->userid;
        $ret['list'][] = array("text" => '我的投票', "url" => "/vote?c=list&u=".$u->userid);
        $ret['list'][] = array("text" => '热门投票', "url" => "/vote?c=hot");
        $this->set('no_html_data', $ret);
    }

    public function view(){
        $this->css[] = "xwidget.css";
        $this->css['plugin']['vote'][] = "vote.css";
        $this->js['plugin']['vote'][] = "vote.js";
        $this->js[] = "forum.share.js";
        $this->notice[] = array("url" => "", "text" => "查看投票");
        $this->cache(false);
        if(!isset($this->params['vid']))
            $this->error("未知的投票");
        $vid = intval($this->params['vid']);
        try{
            $vote = new Vote($vid);
        }catch(VoteNullException $e){
            $this->error("未知的投票");
        }
        $u = User::getInstance();
        if($vote->isDeleted() && !$u->isAdmin())
            $this->error("此投票已删除");
        $myres = $vote->getResult($u->userid);

        $voted = false;
        if($myres !== false){
            $voted = true;
            $myres['time'] = date("Y-m-d H:i:s", $myres['time']);
            $this->set("myres", $myres);
        }
        App::import('Sanitize');
        $info = array(
            "vid" =>$vote->vid,
            "title" =>Sanitize::html($vote->subject),
            "desc" =>nl2br(Sanitize::html($vote->desc)),
            "start" =>date("Y-m-d H:i:s", $vote->start),
            "end" =>date("Y-m-d", $vote->end),
            "num" =>$vote->num,
            "type" =>$vote->type,
            "limit" =>$vote->limit,
            "aid" =>$vote->aid,
            "isEnd" =>$vote->isEnd(),
            "isDel" =>$vote->isDeleted(),
            "voted" =>$voted,
            "uid" =>$vote->uid
        );
        $item = $vote->items;
        foreach($item as $k=>$v){
            $item[$k]["label"] = Sanitize::html($v["label"]);
            $item[$k]["percent"] = ($vote->total === 0)?0:round(intval($v['num'])*100/$vote->total);
            $item[$k]["on"] = ($myres !== false) && in_array($v['viid'], $myres['items']);
        }
        $this->set("board", $this->_board);
        $this->set("admin", $u->userid === $vote->uid || $u->isAdmin());
        $this->set("result_voted", $vote->result_voted);
        $this->set("no_result", !$vote->isEnd() && !$this->get('admin') && $vote->result_voted && !$voted);
        $this->set("vinfo", $info);
        $this->set("vitems", $item);

        try{
            $u = User::getInstance($vote->uid);
            $furl = $u->getFace();
        }catch(UserNullException $e){
            $furl = false;
        }
        $this->set("furl", Sanitize::html($furl));
        $this->set("fwidth", ($u->userface_width === 0)?"":$u->userface_width);
        $this->set("fheight", ($u->userface_height === 0)?"":$u->userface_height);

        App::import('vendor', array('model/board', 'model/threads', 'inc/ubb'));
        try{
            $threads = Threads::getInstance($vote->aid, Board::getInstance($this->_board));
            $s = $threads->getTotalNum() - $this->_commentNum + 1;
            $articles = $threads->getRecord($s > 1?$s:1, $this->_commentNum);
            $info = array();
            foreach($articles as $v){
                if($v->OWNER === 'deliver') continue;
                $tmp = array();
                try{
                    $own = User::getInstance($v->OWNER);
                    $tmp['uid'] = $own->userid;
                    $tmp['furl'] = Sanitize::html($own->getFace());
                }catch(UserNullException $e){
                    $tmp['uid'] = $v->OWNER;
                    $tmp['furl'] = false;
                }
                $tmp['time'] = date("Y-m-d H:i:s", $v->POSTTIME);
                $content = $v->getPlant();
                $content = preg_replace("|<br/>【 在 deliver[\s\S]+<br/>: \.{5,}<br/>|", '', $content);
                $content = preg_replace("/&nbsp;/", " ", $content);
                $content = preg_replace("/  /", "&nbsp;&nbsp;", $content);
                $content = preg_replace("|※ 修改:·([\S]+) .*?FROM:[\s]*([0-9a-zA-Z.:*]+)|", '', $content);
                $content = preg_replace("|※ 来源:.*FROM:[\s]*([0-9a-zA-Z.:*]+)|", '', $content);
                $s = (($pos = strpos($content, "<br/><br/>")) === false)?0:$pos + 10;
                $e = (($pos = strpos($content, "<br/>--<br/>")) === false)?strlen($content):$pos + 7;
                $content = preg_replace(
                    array("'^(<br/>)+'", "|(<br/>)+--$|")
                    ,array("", "")
                    ,substr($content, $s, $e - $s)
                );
                if(Configure::read("ubb.parse")){
                    $content = XUBB::parse($content);
                }
                $tmp['content'] = $content;
                $info[] = $tmp;
            }
            if(!strncmp($threads->TITLE, "Re: ", 4))
                $this->set("title", $threads->TITLE);
            else
                $this->set("title", 'Re: ' . $threads->TITLE);
            $this->set("ajax_title", urlencode('投票:' . $threads->TITLE));
            $this->set("reid", $threads->ID);
            $this->set("more", $threads->getTotalNum() > $this->_commentNum + 1);
            $this->set("comments", $info);
        }catch(ThreadsNullException $e){
            $this->set("comments", false);
        }
    }

    public function ajax_vote(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        $this->requestLogin();
        if(!isset($this->params['vid']))
            $this->error("未知的投票");
        $vid = intval($this->params['vid']);
        try{
            $vote = new Vote($vid);
        }catch(VoteNullException $e){
            $this->error("未知的投票");
        }
        $u = User::getInstance();
        if($vote->isDeleted() && !$u->isAdmin())
            $this->error("此投票已删除");
        $myres = $vote->getResult($u->userid);
        if($myres !== false)
            $this->error("你已经投过票了");
        if($vote->isDeleted())
            $this->error("此投票已删除");
        if($vote->isEnd())
            $this->error("此投票已截止");
        if($vote->type == "0"){
            @$viid = $this->params['form']['v'.$vote->vid];
            if(!$vote->hasItem($viid))
                $this->error("未知的选项，投票失败");
            $vote->vote($u->userid, $viid);
        }else if($vote->type == "1"){
            $items = array_keys($this->params['form']);
            if(count($items) == 0)
                $this->error("请至少选择一个选项");
            if(count($items) > $vote->limit && $vote->limit != 0)
                $this->error("投票个数超过限制，投票失败");
            $items = preg_replace("/v{$vote->vid}_/", "", $items);
            foreach($items as $v){
                if(!$vote->hasItem($v))
                    $this->error("未知的选项，投票失败");
            }
            $vote->vote($u->userid, $items);
        }else{
            $this->error("错误的投票");
        }
    }

    public function ajax_delete(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        if(!isset($this->params['vid']))
            $this->error("未知的投票");
        $vid = intval($this->params['vid']);
        try{
            $vote = new Vote($vid);
        }catch(VoteNullException $e){
            $this->error("未知的投票");
        }
        $u = User::getInstance();
        if(!$u->isAdmin() && $u->userid != $vote->uid)
            $this->error("你无权删除此投票");
        $vote->delete();
    }
}
?>
