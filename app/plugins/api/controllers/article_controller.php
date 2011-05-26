<?php
class ArticleController extends ApiAppController {

    private $_board;

    public function beforeFilter(){
        parent::beforeFilter();
        App::import("vendor", "model/board");
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

        if(!$this->_board->hasReadPerm(User::getInstance()))
            $this->error(ECode::$BOARD_NOPERM);

        $this->_board->setOnBoard();
    }

    public function index(){
        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);
        $id = $this->params['id'];

        App::import('vendor', "model/article");
        App::import('vendor', 'api.wrapper');
        $wrapper = Wrapper::getInstance();
        try{
            $article = Article::getInstance($id, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        $this->set('data', $wrapper->article($article, array('single' => true, 'content' => true)));
    }

    public function threads(){
        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);
        $id = $this->params['id'];

        App::import('vendor', array("model/threads", "inc/pagination"));
        try{
            $threads = Threads::getInstance($id, $this->_board);
        }catch(ThreadsNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        $count = isset($this->params['url']['count'])?$this->params['url']['count']:Configure::read("pagination.article");
        if(($count = intval($count)) <= 0)
            $count = Configure::read("pagination.article");
        if($count > Configure::read('plugins.api.page_item_limit'))
            $count = Configure::read("pagination.article");
        $page = isset($this->params['url']['page'])?$this->params['url']['page']:1;
        $page = intval($page);
        $pagination = new Pagination($threads, $count);
        $articles = $pagination->getPage($page);
        App::import('vendor', 'api.wrapper');
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

    public function post(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if($this->_board->isReadOnly())
            $this->error(ECode::$BOARD_READONLY);

        if(!$this->_board->hasPostPerm(User::getInstance()))
            $this->error(ECode::$BOARD_NOPOST);

        App::import('vendor', "model/article");
        $reID = 0;
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
        }

        if(!isset($this->params['form']['title']))
            $this->error(ECode::$POST_NOSUB);
        if(!isset($this->params['form']['content']))
            $this->error(ECode::$POST_NOCON);
        $title = trim($this->params['form']['title']);
        $content = trim($this->params['form']['content']);

        $title = rawurldecode($title);
        $content = rawurldecode($content);
        if($this->encoding != Configure::read("App.encoding")){
            $title = @iconv($this->encoding, Configure::read("App.encoding"). '//IGNORE', $title);
            $content = @iconv($this->encoding, Configure::read("App.encoding"). '//IGNORE', $content);
        }
        if(strlen($title) > 60)
            $title = nforum_fix_gbk(substr($title,0,60));
        $sig = User::getInstance()->signature;
        $email = 0;$anony = null;
        if(isset($this->params['form']['signature']))
            $sig = intval($this->params['form']['signature']);
        if(isset($this->params['form']['email']) && $this->params['form']['email'] == '1')
            $email = 1;
        if(isset($this->params['form']['anonymous']) && $this->params['form']['anonymous'] == '1')
            $anony = 1;
        try{
            $id = Article::post($this->_board, $title, $content, $sig, $reID, $email, $anony);
            $new = Article::getInstance($id, $this->_board);
            App::import('vendor', 'api.wrapper');
            $wrapper = Wrapper::getInstance();
            $this->set('data', $wrapper->article($new, array('content' => true)));

        }catch(ArticlePostException $e){
            $this->error($e->getMessage());
        }
    }

    public function update(){
        if(!$this->RequestHandler->isPost())
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
            App::import('vendor', "model/article");
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
        if($this->encoding != Configure::read("App.encoding")){
            $title = @iconv($this->encoding, Configure::read("App.encoding"). '//IGNORE', $title);
            $content = @iconv($this->encoding, Configure::read("App.encoding"). '//IGNORE', $content);
        }
        if(strlen($title) > 60)
            $title = nforum_fix_gbk(substr($title,0,60));
        if(!$article->update($title, $content))
            $this->error(ECode::$ARTICLE_EDITERROR);
        App::import('vendor', 'api.wrapper');
        $new = Article::getInstance($id, $this->_board);
        $wrapper = Wrapper::getInstance();
        $this->set('data', $wrapper->article($new, array('content' => true)));
    }

    public function delete(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);

        $id = intval($this->params['id']);
        try{
            App::import('vendor', "model/article");
            $article = Article::getInstance($id, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        $u = User::getInstance();
        if(!$article->hasEditPerm($u))
            $this->error(ECode::$ARTICLE_NODEL);
        App::import('vendor', 'api.wrapper');
        $wrapper = Wrapper::getInstance();
        $this->set('data', $wrapper->article($article, array('content' => true)));
        if(!$article->delete())
            $this->error(ECode::$ARTICLE_NODEL);
    }
}
?>
