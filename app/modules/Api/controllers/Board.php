<?php
class BoardController extends NF_ApiController {

    private $_board;

    public function init(){
        parent::init();
        load(array("model/board", "inc/pagination"));
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

        if(isset($this->params['url']['mode'])){
            $mode = (int)trim($this->params['url']['mode']);
            $this->_board->setMode($mode);
        }
        if(!$this->_board->hasReadPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPERM);
        }
        $this->_board->setOnBoard();
    }

    public function indexAction(){

        $wrapper = Wrapper::getInstance();

        $data = array();
        $data = $wrapper->board($this->_board, array('status' => true));
        load('inc/pagination');

        $count = isset($this->params['url']['count'])?$this->params['url']['count']:c("pagination.threads");
        $page = isset($this->params['url']['page'])?$this->params['url']['page']:1;
        if(($count = intval($count)) <= 0)
            $count = c("pagination.threads");
        if($count > c('modules.api.page_item_limit'))
            $count = c("pagination.article");
        $page = intval($page);
        $pagination = new Pagination($this->_board, $count);
        $articles = $pagination->getPage($page);
        $data['pagination'] = $wrapper->page($pagination);
        foreach($articles as $v){
            $data['article'][] = $wrapper->article($v, array('threads'=> $this->_board->getMode() == Board::$THREAD));
        }
        $this->set('data', $data);

    }
}
