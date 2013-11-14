<?php
class ArticleController extends NF_ApiController {

    private $_board;

    public function init(){
        parent::init();
        load("model/board");
        if(!isset($this->params['name']))
            $this->error(ECode::$BOARD_NONE);

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

        if(isset($this->params['url']['mode'])){
            $mode = (int)trim($this->params['url']['mode']);
            $this->_board->setMode($mode);
        }
        if(!$this->_board->hasReadPerm(User::getInstance()))
            $this->error(ECode::$BOARD_NOPERM);

        $this->_board->setOnBoard();
    }

    public function indexAction(){
        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);
        $id = $this->params['id'];

        load("model/article");
        $wrapper = Wrapper::getInstance();
        try{
            $article = Article::getInstance($id, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        $this->set('data', $wrapper->article($article, array('single' => true, 'content' => true)));
    }

    public function threadsAction(){
        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);
        $id = $this->params['id'];

        load(array("model/threads", "inc/pagination"));
        try{
            $threads = Threads::getInstance($id, $this->_board);
        }catch(ThreadsNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }

        //filter author
        $auF = $au = false;
        if(isset($this->params['url']['au'])){
            $tmp = $threads->getRecord(1, $threads->getTotalNum());
            $auF = array();$au = trim($this->params['url']['au']);
            foreach($tmp as $v){
                if($v->OWNER == $au)
                    $auF[] = $v;
            }
            $auF = new ArrayPageableAdapter($auF);
        }

        $count = isset($this->params['url']['count'])?$this->params['url']['count']:c("pagination.article");
        if(($count = intval($count)) <= 0)
            $count = c("pagination.article");
        if($count > c('modules.api.page_item_limit'))
            $count = c("pagination.article");
        $page = isset($this->params['url']['page'])?$this->params['url']['page']:1;
        $page = intval($page);
        $pagination = new Pagination(false !== $au?$auF:$threads, $count);
        $articles = $pagination->getPage($page);
        $wrapper = Wrapper::getInstance();
        $info = array();
        foreach($articles as $v){
            $info[] = $wrapper->article($v, array('content' => true));
        }
        $data = $wrapper->article($threads, array('threads'=> true));
        $data['pagination'] = $wrapper->page($pagination);
        $data['article'] = $info;
        $this->set('data', $data);
        $this->set('root', 'threads');
    }

    public function postAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if($this->_board->isReadOnly())
            $this->error(ECode::$BOARD_READONLY);

        if(!$this->_board->hasPostPerm(User::getInstance()))
            $this->error(ECode::$BOARD_NOPOST);

        if(!isset($this->params['form']['title']))
            $this->error(ECode::$POST_NOSUB);
        if(!isset($this->params['form']['content']))
            $this->error(ECode::$POST_NOCON);
        $title = trim($this->params['form']['title']);
        $content = trim($this->params['form']['content']);

        $title = rawurldecode($title);
        $content = rawurldecode($content);
        $title = nforum_iconv($this->encoding, 'GBK', $title);
        $content = nforum_iconv($this->encoding, 'GBK', $content);
        if(strlen($title) > 60)
            $title = nforum_fix_gbk(substr($title,0,60));
        $sig = User::getInstance()->signature;
        $email = 0;$anony = null;$outgo = 0;
        if(isset($this->params['form']['signature']))
            $sig = intval($this->params['form']['signature']);
        if(isset($this->params['form']['email']) && $this->params['form']['email'] == '1')
            $email = 1;
        if(isset($this->params['form']['anonymous']) && $this->params['form']['anonymous'] == '1' && $this->_board->isAnony())
            $anony = 1;
        if(isset($this->params['form']['outgo']) && $this->params['form']['outgo'] == '1' && $this->_board->isOutgo())
            $outgo = 1;

        load("model/article");
        try{
            if(isset($this->params['form']['reid'])){
                if($this->_board->isNoReply())
                    $this->error(ECode::$BOARD_NOREPLY);
                $reID = intval($this->params['form']['reid']);
                try{
                    $reArticle = Article::getInstance($reID, $this->_board);
                }catch(ArticleNullException $e){
                    $this->error(ECode::$ARTICLE_NOREID);
                }
                if($reArticle->isNoRe())
                    $this->error(ECode::$ARTICLE_NOREPLY);
                $id = $reArticle->reply($title, $content, $sig, $email, $anony, $outgo);
            }else{
                $id = Article::post($this->_board, $title, $content, $sig, $email, $anony, $outgo);
            }
            $new = Article::getInstance($id, $this->_board);
            $wrapper = Wrapper::getInstance();
            $this->set('data', $wrapper->article($new, array('content' => true)));
        }catch(ArticlePostException $e){
            $this->error($e->getMessage());
        }
    }

    public function forwardAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        $this->requestLogin();
        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);
        if(!isset($this->params['form']['target']))
            $this->error(ECode::$USER_NONE);
        $id = intval($this->params['id']);
        $target = trim($this->params['form']['target']);
        $threads = (isset($this->params['form']['threads']) && $this->params['form']['threads'] == 1);
        $noref = (isset($this->params['form']['noref']) && $this->params['form']['noref'] == 1);
        $noatt = (isset($this->params['form']['noatt']) && $this->params['form']['noatt'] == 1);
        $noansi = (isset($this->params['form']['noansi']) && $this->params['form']['noansi'] == 1);
        $big5 = (isset($this->params['form']['big5']) && isset($this->params['form']['big5']) ==1);
        try{
            load(array('model/article', 'model/threads'));
            $article = Article::getInstance($id, $this->_board);
            if($threads){
                $t = Threads::getInstance($article->GROUPID, $this->_board);
                $t->forward($target, $t->ID, $noref, $noatt, $noansi, $big5);
            }else{
                $article->forward($target, $noatt, $noansi, $big5);
            }
            $wrapper = Wrapper::getInstance();
            $this->set('data', $wrapper->article($article, array('content' => true)));
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }catch(ArticleForwardException $e){
            $this->error($e->getMessage());
        }
    }

    public function updateAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if($this->_board->isReadOnly()){
            $this->error(ECode::$BOARD_READONLY);
        }
        if(!$this->_board->hasPostPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPOST);
        }

        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);
        $id = intval($this->params['id']);
        try{
            load("model/article");
            $article = Article::getInstance($id, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        $u = User::getInstance();
        if(!$article->hasEditPerm($u))
            $this->error(ECode::$ARTICLE_NOEDIT);

        if(!isset($this->params['form']['title']))
            $this->error(ECode::$POST_NOSUB);
        if(!isset($this->params['form']['content']))
            $this->error(ECode::$POST_NOCON);
        $title = trim($this->params['form']['title']);
        $content = trim($this->params['form']['content']);
        $title = rawurldecode($title);
        $content = rawurldecode($content);
        $title = nforum_iconv($this->encoding, 'GBK', $title);
        $content = nforum_iconv($this->encoding, 'GBK', $content);
        if(strlen($title) > 60)
            $title = nforum_fix_gbk(substr($title,0,60));
        if(!$article->update($title, $content))
            $this->error(ECode::$ARTICLE_EDITERROR);
        $new = Article::getInstance($id, $this->_board);
        $wrapper = Wrapper::getInstance();
        $this->set('data', $wrapper->article($new, array('content' => true)));
    }

    public function deleteAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);

        $id = intval($this->params['id']);
        try{
            load("model/article");
            $article = Article::getInstance($id, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        $u = User::getInstance();
        if(!$article->hasEditPerm($u))
            $this->error(ECode::$ARTICLE_NODEL);
        $wrapper = Wrapper::getInstance();
        $this->set('data', $wrapper->article($article, array('content' => true)));
        if(!$article->delete())
            $this->error(ECode::$ARTICLE_NODEL);
    }
}
