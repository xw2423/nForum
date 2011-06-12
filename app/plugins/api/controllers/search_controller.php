<?php
class SearchController extends ApiAppController {

    public function board(){
        App::import('vendor', array('model/board', 'api.wrapper'));
        $b = isset($this->params['url']['board'])?$this->params['url']['board']:"";
        $boards = Board::search(trim($b));

        $wrapper = Wrapper::getInstance();

        $data = array();
        $data['board'] = $data['section'] = array();
        foreach($boards as $brd){
            if($brd->isDir())
                $data['section'][] = $brd->NAME;
            else
                $data['board'][] = $wrapper->board($brd, array('status'=>true));
        }
        $this->set('data', $data);
    }

    public function article(){
        App::import('vendor', array('model/board', 'model/article', 'api.wrapper', 'inc/pagination'));
        $day = 7;
        $title1 = $title2 = $title3 = $author = $o = '';

        if(isset($this->params['url']['title1']))
            $title1 = trim($this->params['url']['title1']);
        if(isset($this->params['url']['title2']))
            $title2 = trim($this->params['url']['title2']);
        if(isset($this->params['url']['titlen']))
            $title3 = trim($this->params['url']['titlen']);
        if(isset($this->params['url']['author']))
            $author = trim($this->params['url']['author']);
        if(isset($this->params['url']['day']))
            $day = intval($this->params['url']['day']);
        $m = isset($this->params['url']['m']) && $this->params['url']['m'] == '1';
        $a = isset($this->params['url']['a']) && $this->params['url']['a'] == '1';
        $o = isset($this->params['url']['o']) && $this->params['url']['o'] == '1';
        $res = array();
        if(!isset($this->params['url']['boards']))
            $this->error(ECode::$BOARD_UNKNOW);
        $boards = $this->params['url']['boards'];    
        foreach(explode('|', $boards) as $b){
            try{
                $brd = Board::getInstance($b);
                $res = array_merge($res, Article::search($brd, $title1, $title2, $title3, $author, $day, $m, $a, $o));
            }catch(BoardNullException $e){
            }
        }

        $count = isset($this->params['url']['count'])?$this->params['url']['count']:Configure::read("pagination.threads");
        if(($count = intval($count)) <= 0)
            $count = Configure::read("pagination.threads");
        if($count > Configure::read('plugins.api.page_item_limit'))
            $count = Configure::read("pagination.threads");
        $page = isset($this->params['url']['page'])?$this->params['url']['page']:1;
        $page = intval($page);
        $pagination = new Pagination(new ArrayPageableAdapter($res), $count);
        $articles = $pagination->getPage($page);
        $wrapper = Wrapper::getInstance();
        $data = array();
        $data['pagination'] = $wrapper->page($pagination);
        foreach($articles as $v){
            $data['article'][] = $wrapper->article($v, array('threads' => false));
        }
        $this->set('data', $data);
    }

    public function threads(){
        App::import('vendor', array('model/board', 'model/threads', 'api.wrapper', 'inc/pagination'));
        $day = 7;
        $title1 = $title2 = $title3 = $author = '';

        if(isset($this->params['url']['title1']))
            $title1 = trim($this->params['url']['title1']);
        if(isset($this->params['url']['title2']))
            $title2 = trim($this->params['url']['title2']);
        if(isset($this->params['url']['titlen']))
            $title3 = trim($this->params['url']['titlen']);
        if(isset($this->params['url']['author']))
            $author = trim($this->params['url']['author']);
        if(isset($this->params['url']['day']))
            $day = intval($this->params['url']['day']);
        $m = isset($this->params['url']['m']) && $this->params['url']['m'] == '1';
        $a = isset($this->params['url']['a']) && $this->params['url']['a'] == '1';
        $return =  Configure::read('search.max');
        $res = array();
        if(!isset($this->params['url']['boards']))
            $this->error(ECode::$BOARD_UNKNOW);
        $boards = $this->params['url']['boards'];    
        foreach(explode('|', $boards) as $b){
            try{
                $brd = Board::getInstance($b);
                $res = array_merge($res, Threads::search($brd, $title1, $title2, $title3, $author, $day, $m, $a, $return));
            }catch(BoardNullException $e){
            }
        }

        $count = isset($this->params['url']['count'])?$this->params['url']['count']:Configure::read("pagination.threads");
        if(($count = intval($count)) <= 0)
            $count = Configure::read("pagination.threads");
        if($count > Configure::read('plugins.api.page_item_limit'))
            $count = Configure::read("pagination.threads");
        $page = isset($this->params['url']['page'])?$this->params['url']['page']:1;
        $page = intval($page);
        $pagination = new Pagination(new ArrayPageableAdapter($res), $count);
        $articles = $pagination->getPage($page);
        $wrapper = Wrapper::getInstance();
        $data = array();
        $data['pagination'] = $wrapper->page($pagination);
        foreach($articles as $v){
            $data['threads'][] = $wrapper->article($v, array('threads' => true));
        }
        $this->set('data', $data);
    }

}
?>
