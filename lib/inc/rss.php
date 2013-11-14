<?php
/**
 * class Rss
 * Rss generator
 *
 * @author xw
 */
class Rss {

    /**
     * rss channel array
     * @var array $channel
     * @access public
     */
    public $channel = array();

    /**
     * rss item array
     * @var array $items
     * @access public
     */
    public $items = array();

    /**
     * required channel elements
     * @var array $_channel
     * @access private
     */
    private $_channel = array("title", "link", "description");

    /**
     * optional channel elements
     * @var array $_channelTag
     * @access private
     */
    private $_channelTag = array("language", "generator", "lastBuildDate", "copyright", "managingEditor", "webMaster", "pubDate", "category", "docs", "cloud", "ttl", "image", "rating", "textInput", "skipHours", "skipDays");

    /**
     * optional item elements
     * @var array $_itemTag
     * @access private
     */
    private $_itemTag = array("title", "link", "author", "pubDate", "guid", "comments", "description", "enclosure", "source");

    /**
     * rss version
     * @var string $_version
     * @access private
     */
    private $_version = "2.0";

    /**
     * rss encode
     * @var string $_encoding
     * @access private
     */
    private $_encoding = "gb2312";

    /**
     * function __construct
     *
     * @param array $c channel data
     * @param array $i item data
     * @return Rss object
     * @access public
     * @throws RssException
     */
    public function __construct($c, $i = array()){
        if(!is_array($c) || !is_array($i))
            throw new RssException();
        $keys = array_keys($c);
        foreach($this->_channel as $v){
            if(!in_array($v, $keys))
                throw new RssException();
        }
        $this->channel = $c;
        $this->items = $i;
    }

    public function getRss(){
        $ret = "";
        $ret .= $this->_getHeader();
        $ret .= $this->_getChannel();
        $ret .= $this->_getItems();
        $ret .= $this->_getFooter();
        return $ret;
    }

    private function _getChannel(){
        $ret = "";
        foreach($this->channel as $k => $v){
            if(!in_array($k, $this->_channelTag) && !in_array($k, $this->_channel))
                continue;
            if($k === "lastBuildDate" || $k === "pubDate")
                $v = gmdate("D, d M Y H:i:s", $v) . " GMT";
            $ret .= "\n\t\t<$k>$v</$k>";
        }
        return $ret;
    }

    private function _getItems(){
        $ret = "";
        foreach($this->items as $item){
            if(is_array($item)){
                $ret .= "\n\t\t<item>";
                foreach($item as $k => $v){
                    if(!in_array($k, $this->_itemTag))
                        continue;
                    if($k === "pubDate")
                        $v = gmdate("D, d M Y H:i:s", $v) . " GMT";
                    $ret .= "\n\t\t\t<$k>$v</$k>";
                }
                $ret .= "\n\t\t</item>";
            }
        }
        return $ret;
    }

    private function _getHeader(){
        return <<<EOT
<?xml version="1.0" encoding="{$this->_encoding}" ?>
<rss version="{$this->_version}">
    <channel>
EOT;
    }

    private function _getFooter(){
        return <<<EOD
\n    </channel>
</rss>
EOD;
    }
}
class RssException extends Exception{}
