<?php
load('model/favor');
class FavoriteController extends NF_ApiController {

    protected $_method = array('post' => array('add', 'delete'));

    public function indexAction(){
        $level = $this->params['num'];
        try{
            $fav = Favor::getInstance($level);
        }catch(FavorNullException $e){
            $this->error(ECode::$USER_FAVERROR);
        }

        $this->set('data', $this->_favor($fav));
    }

    public function addAction(){
        if(!isset($this->params['form']['dir']) || !isset($this->params['form']['name']))
            $this->error();
        $dir = ($this->params['form']['dir'] == '1');
        $val = trim($this->params['form']['name']);
        $level = $this->params['num'];
        try{
            $fav = Favor::getInstance($level);
        }catch(FavorNullException $e){
            $this->error(ECode::$USER_FAVERROR);
        }
        if($val == "")
            $this->error();
        if($dir){
            $val = nforum_iconv($this->encoding, 'GBK', $val);
            if(!$fav->add($val, Favor::$DIR))
                $this->error();
        }else{
            load("model/board");
            try{
                $val = Board::getInstance($val);
                if(!$fav->add($val, Favor::$BOARD))
                    $this->error();
            }catch(BoardNullException $e){
                $this->error(ECode::$Board_UNKNOW);
            }
        }
        try{
            $fav = Favor::getInstance($level);
        }catch(FavorNullException $e){
            $this->error(ECode::$USER_FAVERROR);
        }
        $this->set('data', $this->_favor($fav));
    }

    public function deleteAction(){
        if(!isset($this->params['form']['dir']) || !isset($this->params['form']['name']))
            $this->error();
        $dir = ($this->params['form']['dir'] == '1');
        $val = trim($this->params['form']['name']);
        $level = $this->params['num'];
        try{
            $fav = Favor::getInstance($level);
        }catch(FavorNullException $e){
            $this->error(ECode::$USER_FAVERROR);
        }
        if($val == "")
            $this->error();
        if($dir){
            if(!$fav->delete($val, Favor::$DIR))
                $this->error();
        }else{
            load('model/board');
            try{
                $board = Board::getInstance($val);
                if(!$fav->delete($board, Favor::$BOARD))
                    $this->error();
            }catch(BoardNullException $e){
                $this->error();
            }
        }
        try{
            $fav = Favor::getInstance($level);
        }catch(FavorNullException $e){
            $this->error(ECode::$USER_FAVERROR);
        }
        $this->set('data', $this->_favor($fav));
    }

    private function _favor($fav){
        load('model/section');
        $wrapper = Wrapper::getInstance();
        $f = $s = $b = array();
        if(!$fav->isNull()){
            $brds = $fav->getAll();
            foreach($brds as $k=>$v){
                if($v->NAME == '')
                    $f[] = $wrapper->favorite($v);
                else if($v->isDir())
                    $s[] = $wrapper->section(Section::getInstance($v));
                else
                    $b[] = $wrapper->board($v);
            }
        }
        return array('sub_favorite' => $f, 'section' => $s, 'board' => $b);
    }
}
