<?php
/**
 * section controller for nforum
 *
 * @author xw
 */
load(array("model/board", "model/section"));
class SectionController extends NF_Controller {

    private $_sec;

    public function indexAction(){
        $this->js[] = "forum.board.js";
        $this->css[] = "board.css";

        Forum::setUserMode(BBS_MODE_SELECT);

        if(!isset($this->params['num'])){
            $this->error(ECode::$SEC_NOSECTION);
        }
        try{
            $num = $this->params['num'];
            $this->_sec = Section::getInstance($num, Section::$NORMAL);
        }catch(SectionNullException $e){
            $this->error(ECode::$SEC_NOSECTION);
        }catch(BoardNullException $e){
            $this->error(ECode::$BOARD_NOBOARD);
        }
        $secs = $this->_sec->getAll();
        $ret = false;
        if(!$this->_sec->isNull()){
            $u = User::getInstance();
            foreach($secs as $brd){
                $last = array();
                $last["id"] = "";
                $last["title"] = $last["owner"] = $last["date"] = "无";
                if($brd->hasReadPerm($u)){
                    $threads = $brd->getTypeArticles(0, 1, Board::$ORIGIN);
                    if(!empty($threads)){
                        $threads = $threads[0];
                        $last = array(
                            "id" => $threads->ID,
                            "title" => nforum_html($threads->TITLE),
                            "owner" => $threads->isSubject()?$threads->OWNER:"原帖已删除",
                            "date" => date("Y-m-d H:i:s", $threads->POSTTIME)
                        );
                    }
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
                    "threadsNum" => $brd->getThreadsNum(),
                    "articleNum" => $brd->ARTCNT,
                    "last" => $last
                );
            }
        }
        $this->set("sec", $ret);
        $this->set("noBrd", ECode::msg(ECode::$SEC_NOBOARD));
        $this->set("secName", $this->_sec->getDesc());
        if(!$this->_sec->isRoot()){
            $parent = $this->_sec->getParent();
            $this->notice[] = array("url"=>"/section/{$parent->getName()}", "text"=>$parent->getDesc());
        }
        $this->title = c('site.name') . '-' . $this->_sec->getDesc();
        $this->notice[] = array("url"=>"/section/{$this->_sec->getName()}", "text"=>$this->_sec->getDesc());
        $this->notice[] = array("url"=>"", "text"=>$this->_sec->isRoot()?"分区列表":"目录列表");
    }

    //if url has key 'bo' return all boards name of root,it is for search now
    //if no 'bo' for left menu tree
    //the key 'root' is 'list-section' or 'sec-NAME'
    public function ajax_listAction(){
        $this->cache(true, strtotime(date("Y-m-d", time()+86400)));

        $ret = array();
        if(!isset($this->params['url']['root']))
            $this->_stop();
        $root = $this->params['url']['root'];
        if($root == "list-section"){
            $sections = c("section");
            $boardOnly = isset($this->params['url']['bo']);
            foreach($sections as $k=>$v){
                if($boardOnly){
                    $ret[] = array('name'=>$k, 'desc'=>$v[0]);
                }else{
                    $ret[] = array(
                        "t" => "<a href=\"{$this->base}/section/$k\">{$v[0]}</a>",
                        "id" => "sec-$k"
                        );
                }
            }
            $this->set('no_html_data', $ret);
            $this->set('no_ajax_info', true);
            return;
        }else{
            try{
                $boardOnly = isset($this->params['url']['bo']);
                $root = Section::getInstance(substr($root, 4), $boardOnly?Section::$ALL:Section::$NORMAL);
                $sections = $boardOnly?$root->getList():$root->getAll();
                foreach($sections as $v){
                    $tmp = array();
                    if($boardOnly){
                        $ret[] = array('name'=>$v->NAME, 'desc'=>$v->DESC);
                        continue;
                    }
                    if($v->isDir()){
                        $tmp['t'] = "<a href=\"{$this->base}/section/{$v->NAME}\" title=\"{$v->DESC}\">{$v->DESC}</a>";
                        $tmp['id'] = 'sec-' . $v->NAME;
                    }else{
                        $tmp['t'] = "<a href=\"{$this->base}/board/{$v->NAME}\" title=\"{$v->DESC}\">{$v->DESC}</a>";
                    }
                    $ret[] = $tmp;
                }
                $this->set('no_html_data', $ret);
                $this->set('no_ajax_info', true);
            }catch(SectionNullException $e){
                $this->_stop();
            }catch(BoardNullException $e){
                $this->_stop();
            }
        }
    }
}
