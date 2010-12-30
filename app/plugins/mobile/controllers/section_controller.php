<?php
App::import("vendor", "model/section");
class SectionController extends MobileAppController {
    public function index(){
        $name = trim($this->params['name']);
        $parent = $ret = false;
        if($name == ""){
            $this->notice = "讨论区列表";
            $secs = Configure::read("section");
            foreach($secs as $k=>$v){
                $ret[] = array(
                    "name" => $v[0],
                    "desc" => $v[1],
                    "url" => "/section/" . $k,
                    "hot" => "/hot/" . $k,
                    "dir" => true
                );
            }
        }else{
            try{
                $sec = Section::getInstance($name, Section::$NORMAL);    
                $this->notice = "讨论区-" . $sec->getDesc();
                $brds = $sec->getAll();
                $ret = array();
                foreach($brds as $b){
                    $ret[] = array(
                        "name" => $b->DESC,
                        "desc" => $b->NAME,
                        "url" => ($b->isDir()?"/section/":"/board/") . $b->NAME,
                        "dir" => $b->isDir()
                    );
                }
                if($sec->isRoot())
                    $parent = "/section";
                else{
                    $parent = $sec->getParent();
                    $parent = "/section/" . $parent->getName();
                }
            }catch(SectionNullException $e){
                $this->error(ECode::$SEC_NOSECTION);
            }
        }
        $this->set("boards", $ret);
        $this->set("parent", $parent);
    }
}
?>
