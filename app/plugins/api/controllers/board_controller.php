<?php

class BoardController extends ApiAppController {

    private $_board;

    public function beforeFilter(){
        parent::beforeFilter();
        App::import("vendor", array("model/board", "inc/pagination"));
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

        if(!$this->_board->hasReadPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPERM);
        }
        $this->_board->setOnBoard();
    }

    public function index(){

        App::import('vendor', 'api.wrapper');
        $wrapper = Wrapper::getInstance();

        $data = array();
        $data = $wrapper->board($this->_board, array('status' => true));
        App::import('vendor', 'inc/pagination');

        $mode = isset($this->params['url']['mode'])?$this->params['url']['mode']:Board::$THREAD;
        $count = isset($this->params['url']['count'])?$this->params['url']['count']:Configure::read("pagination.threads");
        $page = isset($this->params['url']['page'])?$this->params['url']['page']:1;
        if(!in_array($mode, range(0, 6)))
            $this->error(ECode::$BOARD_MODEERROR);
        if(($count = intval($count)) <= 0)
            $count = Configure::read("pagination.threads");
        if($count > Configure::read('plugins.api.page_item_limit'))
            $count = Configure::read("pagination.article");
        $page = intval($page);
        $pagination = new Pagination($this->_board, $count);
        $this->_board->setMode($mode);
        $articles = $pagination->getPage($page);
        $data['pagination'] = $wrapper->page($pagination);
        foreach($articles as $v){
            $data['article'][] = $wrapper->article($v, array('threads'=> $mode == Board::$THREAD));
        }
        $this->set('data', $data);

    }
}
?>
