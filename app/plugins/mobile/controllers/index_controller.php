<?php
App::import("vendor", "model/widget");
class IndexController extends MobileAppController {
    public function index(){
        $top = Widget::getInstance("topTen");
        $res = $top->wGetList();
        $res = $res['v'];
        $this->set("top", $res);
        if($this->ByrSession->isLogin){
            $u = User::getInstance();
            $this->set("level", $u->getLevel());
            $this->set("postNum", $u->numposts);
        }
    }

    public function hot(){
        $this->notice = "热点推荐";
        $selected = $type = "topTen";

        $s = Configure::read("section");
        $secs["topTen"] = "十大";
        $secs["recommend"] = "活动";
        foreach($s as $k=>$v){
            if($k === 0)
                continue;
            $secs[$k] = $v[0];    
        }

        if(isset($this->params['t'])){
            $selected = trim($this->params['t']);
            if(!in_array($selected, array_keys($secs)))
                $this->error(ECode::$SEC_NOSECTION);
            if(!isset($selected[1]))
                $type = "section-" . $selected; 
            else
                $type = $selected;
        }
        try{
            $w = Widget::getInstance($type);
            $res = $w->wGetList();
            $res = (isset($res[0]['v']['v']))?$res[0]['v']['v']:$res['v'];
            if(!isset($selected[1])){
                foreach($res as $k=>$v){
                    $text = $v["text"];
                    $text = preg_replace("|\[.*?\]|", "", $text, 1);
                    if(preg_match("|href=\"(.*?)\"|", $text, $url))
                        $url = str_replace($this->base . "/article", "/article", $url[1]);
                    else
                        $url = "";
                    $text = trim(preg_replace("|<[^>]*?>|", "", $text));
                    $res[$k] = array(
                        "text" => $text,
                        "url" => $url
                    );
                }
            }
            $this->set("hot", $res);
        }catch(WidgetNullException $e){
            $this->error(ECode::$SEC_NOSECTION);
        }
        $this->set("secs", $secs);
        $this->set("selected", $selected);
    }

    public function searchBoard(){
        $bName = "";
        if(isset($this->params['url']['name']))
            $bName = trim($this->params['url']['name']);
        if($this->encoding != Configure::read("App.encoding")){
            $bName = iconv($this->encoding, Configure::read("App.encoding")."//IGNORE", $bName);
        }
        $boards = Board::search($bName);
        if(count($boards) == 1){
            $this->redirect($this->_mbase . "/board/". $boards[0]->NAME);
        }else{
            $ret = false;
            foreach($boards as $b){
                $ret[] = array(
                    "name" => $b->DESC,
                    "desc" => $b->NAME,
                    "url" => ($b->isDir()?"/section/":"/board/") . $b->NAME,
                    "dir" => $b->isDir()
                );
            }
            $this->set("boards", $ret);
            $this->set("parent", false);
            $this->autoRender = false;
            $this->render("index", "/section/");
        }
    }
}
?>
