<?php
/**
 * search controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/board"));
class SearchController extends AppController {

    public function search(){
        $this->js[] = "forum.search.js";
        $this->css[] = "search.css";
        $this->notice[] = array("url"=>"javascript:void(0);", "text"=>"搜索文章");

        $secs = Configure::read("section");
        foreach($secs as $k=>&$v){
            $v = $k . "区:" . $v[0];
        }
        $this->set("sec", $secs);
        $this->set("selected", 0);

        //for default search day 
        $this->set("searchDay", Configure::read("search.day"));
    }

    public function doSearch(){
        $this->js[] = "forum.board.js";
        $this->css[] = "board.css";
        $this->notice[] = array("url"=>"javascript:void(0);", "text"=>"搜索结果");

        App::import('Sanitize');
        $day = $title1 = $title2 = $title3 = $author = $t = "";

        if(isset($this->params['url']['t1']))
            $title1 = trim($this->params['url']['t1']);
        if(isset($this->params['url']['t2']))
            $title2 = trim($this->params['url']['t2']);
        if(isset($this->params['url']['tn']))
            $title3 = trim($this->params['url']['tn']);
        if(isset($this->params['url']['au']))
            $author = trim($this->params['url']['au']);
        if(isset($this->params['url']['d']))
            $day = intval($this->params['url']['d']);
         $m = isset($this->params['url']['m']);
         $a = isset($this->params['url']['a']);
        if(isset($this->params['url']['t']))
            $t = $this->params['url']['t'];
        $return =  Configure::read("search.max");

        $res = array();
        switch($t){
            case 'xw':
                break;
            case 'm':
                break;
            default:
                $b = @$this->params['url']['b'];    
                try{
                    $brd = Board::getInstance($b);
                }catch(BoardNullException $e){
                    $this->error(ECode::$BOARD_NONE);
                }
                $res = Threads::search($brd, $title1, $title2, $title3, $author, $day, $m,$a, $return);
                break;
        }

        $p = 1;
        if(isset($this->params['url']['p']))
            $p = $this->params['url']['p'];

        App::import("vendor", "inc/pagination");
        $page = new Pagination(new ArrayPageableAdapter($res), Configure::read("pagination.search"));

        $threads = $page->getPage($p);
        $info = false;
        $curTime = strtotime(date("Y-m-d", time()));
        $pageArticle = Configure::read("pagination.article");
        foreach($threads as $v){
            $tabs = ceil($v->articleNum / $pageArticle);
            $last = $v->LAST;
            $postTime = ($curTime > $v->POSTTIME)?date("Y-m-d", $v->POSTTIME):(date("H:i:s", $v->POSTTIME)."&nbsp;&nbsp;");
            $replyTime = ($curTime > $last->POSTTIME)?date("Y-m-d", $last->POSTTIME):(date("H:i:s", $last->POSTTIME)."&nbsp;&nbsp;");
            $info[] = array(
                "title" => Sanitize::html($v->TITLE),
                "poster" => $v->isSubject()?$v->OWNER:"原帖已删除",
                "postTime" => $postTime,
                "gid" => $v->ID,
                "last" => $last->OWNER,
                "replyTime" => $replyTime,
                "page" => $tabs,
                "num" => $v->articleNum - 1
            );
        }
        $this->set("info", $info);
        $query = $this->params['url'];
        unset($query['url']);
        unset($query['p']);
        foreach($query as $k=>&$v)
            $v = $k . '=' . $v;
        $query[] = "p=%page%";
        $link = "?". join("&", $query);
        $pageBar = $page->getPageBar($p, $link);

        $this->set("bName", $brd->NAME);
        $this->set("totalPage", $page->getTotalPage());
        $this->set("totalNum", count($res));
        $this->set("curPage", $page->getCurPage());
        $this->set("pageBar", $pageBar);
    }

    public function board(){
        $this->css[] = "board.css";
        $this->js[] = "forum.board.js";
        $this->notice[] = array("url"=>"javascript:void(0);", "text"=>"搜索结果");

        App::import('Sanitize');
        $b = isset($this->params['url']['b'])?$this->params['url']['b']:"";
        $ret = false;
        $boards = Board::search(trim($b));
        if(count($boards) == 1)
            $this->redirect("/board/". $boards[0]->NAME);
        foreach($boards as $brd){
            $threads = $brd->getTypeArticles(0, 1, Board::$ORIGIN);
            if(!empty($threads)){
                $threads = $threads[0];
                $last = array(
                    "id" => $threads->ID,
                    "title" => Sanitize::html($threads->TITLE),
                    "owner" => $threads->isSubject()?$threads->OWNER:"原帖已删除",
                    "date" => date("Y-m-d H:i:s", $threads->POSTTIME)
                );
            }else{
                $last["id"] = "";
                $last["title"] = $last["owner"] = $last["date"] = "无";
            }
            $bms = split(" ", $brd->BM);
            foreach($bms as &$bm){
                if(preg_match("/[^0-9a-zA-Z]/", $bm)){
                    $bm = array($bm, false);
                }else{
                    $bm = array($bm, true);
                }
            }
            $ret[] = array(
                "name" => $brd->NAME,
                "desc" => $brd->DESC,
                "type" => $brd->isDir()?"section":"board",
                "bms" => $bms,
                "curNum" => $brd->CURRENTUSERS,
                "todayNum" => $brd->getTodayNum(),
                "threadsNum" => $brd->threadsNum,
                "articleNum" => $brd->ARTCNT,
                "last" => $last
            );
        }
        $this->set("sec", $ret);
        $this->set("noBrd", ECode::msg(ECode::$SEC_NOBOARD));
        $this->render("index", "section/");
    }
}
?>
