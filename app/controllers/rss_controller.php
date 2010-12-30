<?php
/**
 * rss controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/board", "model/article", "inc/rss", "inc/ubb"));
class RssController extends AppController {

    private $_domain;
    private $_siteName;

    public function beforeFilter(){
        parent::beforeFilter();
        $this->_domain =Configure::read("site.domain");
        $this->_siteName = Configure::read("site.name");
    }

    public function board(){
        try{
            $brd = Board::getInstance($this->params['name']);
            if(!$brd->hasReadPerm(User::getInstance()) || $brd->isDir())
                $this->_stop();
            $mTime = @filemtime('boards/' . $brd->NAME . '/.ORIGIN');
            $this->cache(true, $mTime);
            $this->header("Content-Type: text/xml; charset=" . $this->encoding);
            $this->header("Content-Disposition: inline;filename=rss.xml");
            $channel = $items = array();
            $channel['title'] = $brd->DESC;
            $channel['description'] = $this->_siteName . " " . $brd->DESC . " 版面主题索引";
            $channel['link'] = $this->_domain . $this->base . '/board/' . $brd->NAME;
            $channel['language'] = "zh-cn";
            $channel['generator'] = "nForum RSS Generator By xw";
            if($mTime > 0){
                $channel['lastBuildDate'] =  $mTime;
            }
            $rssNum = Configure::read("rss.num");
            $items = array();
            $articles = array_reverse($brd->getTypeArticles(0, $rssNum, Board::$ORIGIN));
            App::import('Sanitize');
            foreach($articles as $v){
                $link = $this->_domain . $this->base . '/article/' . $brd->NAME . '/' . $v->GROUPID;
                $item = array(
                    "title" => Sanitize::html($v->TITLE),
                    "link" => $link,
                    "author" => $v->OWNER,
                    "pubDate" => $v->POSTTIME,
                    "guid" => $link,
                    "comments" => $link,
                    "description" => "<![CDATA[" . XUBB::parse($v->getHtml()) . "]]>"
                );
                $items[] = $item;
            }
            $rss = new Rss($channel, $items);
            echo $rss->getRss();
            $this->_stop();
        }catch(Exception $e){
            $this->_stop();
        }
    }

    public function topten(){
        $file = BBS_HOME . '/xml/day.xml';
        if (!file_exists($file)) {
            $this->_stop();
        }
        $mTime = @filemtime($file);
        $this->cache(true, $mTime, 3600);
        $this->header("Content-Type: text/xml; charset=" . $this->encoding);
        $this->header("Content-Disposition: inline;filename=topten.xml");
        $channel = $items = array();
        $channel['title'] = "十大热门话题";
        $channel['description'] = $this->_siteName . " 十大热门话题";
        $channel['link'] = $this->_domain . $this->base;
        $channel['language'] = "zh-cn";
        $channel['generator'] = "nForum RSS Generator By xw";
        if($mTime > 0){
            $channel['lastBuildDate'] =  $mTime;
        }
        $ret = array();
        $xml = simplexml_load_file($file);
        if($xml == false)
            return $ret;
        foreach($xml->hotsubject as $v){
            $link = $this->_domain . $this->base . '/article/' . $v->board . '/' . $v->groupid;
            $item = array(
                "title" => nforum_fix_gbk(urldecode($v->title)),
                "link" => $link,
                "author" => $v->author,
                "pubDate" => intval($v->time),
                "guid" => $link,
                "comments" => $link
            );
            try{
                $article = Article::getInstance(intval($v->groupid), Board::getInstance($v->board));
                $item['description'] = "<![CDATA[" . XUBB::parse($article->getHtml()) . "]]>";
            }catch(Exception $e){}
            $items[] = $item;
        }
        $rss = new Rss($channel, $items);
        echo $rss->getRss();
        $this->_stop();
    }

    public function classic(){
        $map = array(
            "recommend"=>array("commend.xml","近期热点活动","/board/recommend"),
            "bless"=>array("bless.xml","十大祝福", "/board/Blessing")
        );
        if(!isset($this->params['file']))
            $this->_stop();
        $key = strtolower(trim($this->params['file']));
        if(!array_key_exists($key, $map))
            $this->_stop();
        $file = BBS_HOME . "/xml/" . $map[$key][0];
        if (!file_exists($file)) {
            $this->_stop();
        }
        $mTime = @filemtime($file);
        $this->cache(true, $mTime);
        $this->header("Content-Type: text/xml; charset=" . $this->encoding);
        $this->header("Content-Disposition: inline;filename=$key.xml");
        $channel = $items = array();
        $channel['title'] = $map[$key][1];
        $channel['description'] = $this->_siteName . $map[$key][1];
        $channel['link'] = $this->_domain . $this->base . $map[$key][2];
        $channel['language'] = "zh-cn";
        $channel['generator'] = "nForum RSS Generator By xw";
        if($mTime > 0){
            $channel['lastBuildDate'] =  $mTime;
        }
        $ret = array();
        $xml = simplexml_load_file($file);
        if($xml == false)
            return $ret;
        foreach($xml->hotsubject as $v){
            $board = ($v->o_board=="")?$v->board:$v->o_board;
            $id = ($v->o_groupid==0)?$v->groupid:$v->o_groupid;
            $link = $this->_domain . $this->base . '/article/' . $board . '/' . $id;
            $item = array(
                "title" => nforum_fix_gbk(urldecode($v->title)),
                "link" => $link,
                "author" => $v->owner,
                "pubDate" => intval($v->time),
                "guid" => $link,
                "comments" => $link
            );
            try{
                $article = Article::getInstance(intval($v->groupid), Board::getInstance($v->board));
                $item['description'] = "<![CDATA[" . XUBB::parse($article->getHtml()) . "]]>";
            }catch(Exception $e){}
            $items[] = $item;
        }
        $rss = new Rss($channel, $items);
        echo $rss->getRss();
        $this->_stop();
    }
}
?>
