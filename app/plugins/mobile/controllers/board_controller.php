<?php
App::import("vendor", array("inc/pagination", "model/board"));
class BoardController extends MobileAppController {

    private $_board;

    public function beforeFilter(){
        parent::beforeFilter();
        if(isset($this->params['name'])){
            $bName = trim($this->params['name']);
        }else{
            $this->error(ECode::$BOARD_NONE);
        }

        try{
            $this->_board = Board::getInstance($bName);
            if($this->_board->isDir())
                throw new BoardNullException();
        }catch(BoardNullException $e){
            $this->error(ECode::$BOARD_NONE);
        }

        if(isset($this->params['mode'])){
            $mode = (int)trim($this->params['mode']);
            if(!$this->_board->setMode($mode))
                $this->error(ECode::$BOARD_NOPERM);
        }
        if(!$this->_board->hasReadPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPERM);
        }
        $this->_board->setOnBoard();
    }

    public function index(){
        $u = User::getInstance();
        try{
            App::import('Sanitize');
            $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;
            $this->notice = "°æÃæ-{$this->_board->DESC}({$this->_board->NAME})";
            $page = new Pagination($this->_board, Configure::read("pagination.threads"));
            $threads = $page->getPage($p);
            $info = false;
            $curTime = strtotime(date("Y-m-d", time()));
            $pageArticle = Configure::read("pagination.article");
            if($this->_board->getMode() === Board::$THREAD){
                foreach($threads as $v){
                    $pages = ceil($v->articleNum / $pageArticle);
                    $last = $v->LAST;
                    $postTime = ($curTime > $v->POSTTIME)?date("Y-m-d", $v->POSTTIME):(date("H:i:s", $v->POSTTIME));
                    $replyTime = ($curTime > $last->POSTTIME)?date("Y-m-d", $last->POSTTIME):(date("H:i:s", $last->POSTTIME));
                    $info[] = array(
                        "tag" => $v->isTop()?"top":(($v->isM() || $v->isG())?"m":false),
                        "title" => Sanitize::html($v->TITLE),
                        "poster" => $v->isSubject()?$v->OWNER:"Ô­ÌûÒÑÉ¾³ý",
                        "postTime" => $postTime,
                        "gid" => $v->ID,
                        "last" => $last->OWNER,
                        "replyTime" => $replyTime,
                        "num" => $v->articleNum - 1,
                        "page" => $pages
                    );
                }
                $threads = true;
            }else{
                foreach($threads as $v){
                    $postTime = ($curTime > $v->POSTTIME)?date("Y-m-d", $v->POSTTIME):(date("H:i:s", $v->POSTTIME));
                    $info[] = array(
                        "tag" => $v->isTop()?"top":(($v->isM() || $v->isG())?"m":false),
                        "title" => Sanitize::html($v->TITLE),
                        "poster" => $v->OWNER,
                        "postTime" => $postTime,
                        "gid" => $v->ID,
                        "subject" => $v->isSubject(),
                        "top" => $v->isTop(),
                        "m" => $v->isM(),
                        "pos" => $v->getPos()
                    );
                }
                $threads = false;
            }
            $this->set("threads", $threads);
            $this->set("info", $info);
            $this->set("totalPage", $page->getTotalPage());
            $this->set("curPage", $page->getCurPage());
            $this->set("bName", $this->_board->NAME);
            $this->set("canPost", $this->_board->hasPostPerm($u)?1:0);
            $this->set("mode", $this->_board->getMode());
            $this->set("sort", $this->_board->isSortMode());
            $this->set("isBM", $u->isBM($this->_board));
            $this->set("isAdmin", $u->isAdmin());
        }catch(BoardNullException $e){
            $this->error(Board::$BOARD_NONE);
        }
    }
}
?>
