<?php
load(array("model/iwidget", "inc/db"));

class voteWidget extends WidgetAdapter{
    public function wGetTitle(){ return array("text"=>"投票", "url"=>"/vote");}
    public function wGetTime(){
        $file = BBS_HOME . '/boards/nVote/.ORIGIN';
        if(!in_array("vote", c("plugins.install")) || !file_exists($file))
            return time();
        $fTime = @filemtime($file);
        $loop = strtotime(date("Y-m-d H:00:00", time()));
        return ($fTime > $loop)?$fTime:$loop;
    }

    public function wGetList(){
        if(!in_array("vote", c("modules.install")))
            return $this->_error("该应用已停用");
        $ret = array();

        $db = DB::getInstance();

        //new
        $res = $db->all("select vid,subject,num from pl_vote where status=1 order by vid desc limit 10");
        $list = array();
        foreach($res as $v){
            $list[] = array("text"=>nforum_html($v['subject'])."(<font color=\"red\">{$v['num']}</font>)", "url"=>"/vote/view/{$v['vid']}");
        }
        if(empty($list))
            $ret[] = array("t"=>"最新投票", "v"=>array("s"=>parent::$S_LINE, "v"=>array(array("text" => "暂无任何投票", "url" => ""))));
        else
            $ret[] = array("t"=>"最新投票", "v"=>array("s"=>parent::$S_LINE, "v"=>$list));

        //hot
        $yes = time() - 86400;
        $res = $db->all("select vid,subject,num from pl_vote where status=1 and end>$yes order by num desc, vid desc limit 10");
        $list = array();
        foreach($res as $v){
            $list[] = array("text"=>nforum_html($v['subject'])."(<font color=\"red\">{$v['num']}</font>)", "url"=>"/vote/view/{$v['vid']}");
        }
        if(empty($list))
            $ret[] = array("t"=>"最热门", "v"=>array("s"=>parent::$S_LINE, "v"=>array(array("text" => "暂无任何投票", "url" => ""))));
        else
            $ret[] = array("t"=>"最热门", "v"=>array("s"=>parent::$S_LINE, "v"=>$list));
/*
        $votes = nforum_cache_read("vote_week");
        if($votes === false){
            $list = array(array("text" => "应用数据错误", "url" => ""));
        }else{
            $list = array();
            foreach($votes as $v){
                $list[] = array("text"=>nforum_html($v['subject'])."(<font color=\"red\">{$v['num']}</font>)", "url"=>"/vote/view/{$v['vid']}");
            }
            if(empty($list))
                $list = array(array("text" => "暂无任何投票", "url" => ""));
        }
        $ret[] = array("t"=>"周热门", "v"=>array("s"=>parent::$S_LINE, "v"=>$list));
*/
        $votes = nforum_cache_read("vote_month");
        if($votes === false){
            $list = array(array("text" => "应用数据错误", "url" => ""));
        }else{
            $list = array();
            foreach($votes as $v){
                $list[] = array("text"=>nforum_html($v['subject'])."(<font color=\"red\">{$v['num']}</font>)", "url"=>"/vote/view/{$v['vid']}");
            }
            if(empty($list))
                $list = array(array("text" => "暂无任何投票", "url" => ""));
        }
        $ret[] = array("t"=>"月热门", "v"=>array("s"=>parent::$S_LINE, "v"=>$list));
/*
        $votes = nforum_cache_read("vote_year");
        if($votes === false){
            $list = array(array("text" => "应用数据错误", "url" => ""));
        }else{
            $list = array();
            foreach($votes as $v){
                $list[] = array("text"=>nforum_html($v['subject'])."(<font color=\"red\">{$v['num']}</font>)", "url"=>"/vote/view/{$v['vid']}");
            }
            if(empty($list))
                $list = array(array("text" => "暂无任何投票", "url" => ""));
        }
        $ret[] = array("t"=>"年热门", "v"=>array("s"=>parent::$S_LINE, "v"=>$list));
*/
        return $ret;
    }
}
