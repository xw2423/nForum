<?php
/****************************************************
 * FileName: app/vendors/model/archive.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
App::import("vendor", array("model/overload"));

/**
 * class Archive is the base of all the files in kbs include article and mail.
 * The base structure of archive is
 * array(15) {
 *         ["FILENAME"]=> string(17) "Y/M.1224551810.U0"
 *         ["O_BOARD"]=> string(0) ""
 *         ["O_BID"]=> int(0)
 *         ["O_ID"]=> int(0)
 *         ["ID"]=> int(471179)
 *         ["GROUPID"]=> int(471179)
 *         ["REID"]=> int(471179)
 *         ["POSTTIME"]=> int(1224551810)
 *         ["INNFLAG"]=> string(2) "LL"
 *         ["OWNER"]=> string(9) "wfzyl2007"
 *         ["TITLE"]=> string(28) ""
 *         ["FLAGS"]=> string(5) "mnn m "
 *         ["ATTACHPOS"]=> int(0)
 *         ["EFFSIZE"]=> int(0)
 *         ["IS_TEX"]=> int(0)
 * }
 *
 * @abstract
 * @author xw
 */
abstract class Archive extends OverloadObject{

    //accessed0
    public static $SIGN = 0x1;
    public static $TOTAL = 0x2;
    public static $PERCENT = 0x4;
    public static $MARKED = 0x8;
    public static $DIGEST = 0x10;
    public static $REPLIED = 0x20;
    public static $FORWARDED = 0x40;
    public static $IMPORTED = 0x80;

    //accessed1
    public static $READ = 0x1;
    public static $DEL = 0x2;
    public static $MAILBACK = 0x4;
    public static $COMMEND = 0x8;
    public static $CENSOR = 0x20;
    public static $TEX = 0x80;

    private $_att = null;

    public function __construct($info){
        if(!is_array($info))
            throw new ArchiveNullException();
        $this->_info = $info;
    }

    /**
     * function getContent get the raw content of archive without html escape
     * attention, it has attachment data
     *
     * @return string the content of archive
     * @access public
     * @throws ArchiveFileNullException
     */
    public function getContent(){
        $file = $this->getFileName();
        if(!file_exists($file))
            throw new ArchiveFileNullException("can't find file:$file");
        return bbs_originfile($file);
    }

    /**
     * function getPlant get the content of archive
     *
     * @param boolean $color only true will get unlimit attachment
     * @param int $len if default get all the content $color is false avaliable
     * @param boolean $escape $color is false avaliable
     * @return string the content of archive
     * @access public
     */
    public function getPlant($color = false, $len = 0, $escape = false){
        $escape = $escape?1:0;
        $fullName = $this->getFileName();
        if(!file_exists($fullName))
            throw new ArchiveFileNullException();
        $ret = ($color)?bbs_printansifile_nforum($fullName):bbs2_readfile_nforum($fullName, $len, $escape);
        if(is_array($ret)){
            $this->_att = $ret['attachment'];
            $ret = $ret['content'];
        }else
            $ret = '';
        return $ret;
    }

    /**
     * function getHtml get the content of archive with html format
     * use the phplib function without attachments parse
     * it will return attachment ubb code
     *
     * @param boolean $color
     * @return string html
     * @access public
     * @throws ArchiveFileNullException
     */
    public function getHtml($color = false){
        try{
            $content = $this->getPlant($color);
        }catch(ArchiveFileNullException $e){
            $content = '';
        }
        $content = preg_replace("/&nbsp;/", " ", $content);
        $content = preg_replace("/  /", "&nbsp;&nbsp;", $content);
        return $this->parseAtt($content);
    }

    /**
     * function getRef get the reference of archive
     * it used in reply, forward
     * config.article.ref_line is the line for reference
     *
     * @return $string the simple reference format of archive
     * @access public
     */
    public function getRef(){
        $qlev = Configure::read("article.quote_level");
        $refline = Configure::read("article.ref_line");
        $ret = "\n【 在 {$this->OWNER} 的大作中提到: 】\n";
        $con = trim($this->getContent());
        $qPrefix = str_repeat(": ", $qlev);
        $pattern = array(
            "/^发信人([^\n]*\n){4}|^寄信人([^\n]*\n){5}|(?<=\n|^){$qPrefix}(?:【|: )[^\n]*\n|(?<=\n|^)--\n?(?![\s\S]*?\n--\n[\s\S]*?)[\s\S]*?$/"
            ,"/\n\s*?\n/"
            ,"/^((?:[^\n]*\n){" . $refline ."})[\s\S]+$/"
            ,"/(.*\n)/"
        );
        $replace = array("", "\n", "\$1...................\n", ": \$1");
        $con = preg_replace($pattern, $replace, $con);
        return $ret . $con;
    }

    /**
     * function getAttList get list of attachments
     *
     * @param boolean $limit
     * @return array
     * @access public
     */
    public function getAttList($limit = true){
        if($limit){
            $res = bbs_file_attachment_list($this->getFileName());
            return is_array($res)?$res:array();
        }
        //no att limit for display archvie
        if(null === $this->_att)
            $this->getPlant(true, 1, false);
        return is_array($this->_att)?$this->_att:array();
    }

    /**
     * function getAttHtml parse the ubb code of attachment to html
     * the ubb code like [upload=\d][/upload]
     *
     * @param string $thumbnail
     * @return string html
     * @access public
     */
    public function getAttHtml($thumbnail = ''){
        $base = Configure::read('site.prefix');
        $list = $this->getAttList(false);
        $ret = array();
        foreach($list as $v){
            $v['size'] = nforum_size_format($v['size']);
            switch(strtolower(substr(strrchr($v['name'], "."), 1))){
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    $ret[] = $this->_getImg($base . '/att' . $this->getAttLink($v['pos']), $v['size'], $thumbnail);
                    break;
                case 'swf':
                    $ret[] = $this->_getSwf($base . '/att' . $this->getAttLink($v['pos']), $v['name'], $v['size']);
                    break;
                case 'mp3':
                case 'wma':
                    $ret[] = $this->_getMp3($base . '/att' . $this->getAttLink($v['pos']), $v['name'], $v['size']);
                    break;
                default:
                    $ret[] = $this->_getCommon($base . '/att' . $this->getAttLink($v['pos']), $v['name'], $v['size']);
            }
        }
        return $ret;
    }

    /**
     * function parseAtt parse the "[upload][/upload]" tag in content
     *
     * @param string $content
     * @param string $thumbnail
     * @return string
     * @access public
     */
    public function parseAtt($content, $thumbnail = ''){
        $attList = $this->getAttHtml($thumbnail);
        $num = count($attList);
        if($num > 0){
            for($i = 1; $i <= $num; $i++){
                $upload[] = "/\[upload=$i\]\[\/upload\]|(?![\s\S])/";
            }
            $content =  preg_replace($upload, $attList, $content, 1);
        }
        return $content;
    }

    public function hasAttach(){
        return ($this->ATTACHPOS > 0);
    }

    public function getAttach($pos){
        @bbs_file_output_attachment($this->getFileName(), $pos);
    }

    protected function _getCommon($link, $name, $size){
        $templete = '<br /><font color="blue">附件(%size%)</font>&nbsp;<a href="%link%" target="_blank">%name%</a>';
        return str_replace(array("%link%", "%name%", "%size%"), array($link, $name, $size), $templete);
    }

    protected function _getImg($link, $size, $thumbnail = ''){
        $templete = '<br /><a target="_blank" href="%link%"><img border="0" title="单击此在新窗口浏览图片" src="%link%" class="resizeable" /></a>';
        if('' !== $thumbnail)
            $templete = '<br /><a target="_blank" href="%link%">单击此查看原图(' . $size . ')</a><br /><img border="0" src="%link%/' . $thumbnail . '" class="resizeable" />';
        return str_replace("%link%", $link, $templete);
    }

    protected function _getSwf($link, $name, $size){
        $pre = $this->_getCommon($link, $name . "(在新窗口打开)", $size);
        $templete = '<br /><br /><div class="a-swf" _src="%link%"><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="560px" height="420px"><param name="allowScriptAccess" value="never" /><param name="allowFullScreen" value="true" /><param name="movie" value="%link%" /><param name="quality" value="high" /><embed src="%link%" quality="high" allowScriptAccess="never" allowFullScreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" width="560px" height="420px"/></object></div>';
        return $pre . str_replace("%link%", $link, $templete);
    }

    protected function _getMp3($link, $name, $size){
        $pre = $this->_getCommon($link, $name . "(在新窗口打开)", $size);
        $templete = '<br /><br /><div class="a-audio" _src="%link%"></div>';
        return $pre . str_replace("%link%", $link, $templete);
    }

    /**
     * function delete remove the archive from kbs
     *
     * @return boolean true|false
     * @access public
     * @abstract
     */
    abstract public function delete();

    /**
     * function update update the title&conent of archive
     *
     * @return boolean true|false
     * @access public
     * @abstract
     */
    abstract public function update($title, $content);

    /**
     * function getFileName get the path of archive base BBS_HOME
     *
     * @return string
     * @access public
     * @abstract
     */
    abstract public function getFileName();

    /**
     * function getAttLink get the url for attachment
     *
     * @param string $pos
     * @return string
     * @access public
     * @abstract
     */
    abstract public function getAttLink($pos);

    /**
     * function addAttach add attachment to archive
     * there is not function for mail archive now
     * so it will be implemented in article
     *
     * @param string $file path of file
     * @param string $file name of file
     * @return array info of current attachment list
     * @access public
     * @throws ArchiveAttException
     * @abstract
     */
    abstract public function addAttach($file, $fileName);

    /**
     * function delAttach delete attachment from archive
     * there is not function for mail archive now
     * so it will be implemented in article
     *
     * @param int $num postion of attachmnet
     * @return array info of current attachment list
     * @access public
     * @throws ArchiveAttException
     * @abstract
     */
    abstract public function delAttach($num);

    public function isM(){
        return ($this->ACCESSED0 & self::$MARKED) !== 0;
    }

    public function isG(){
        return ($this->ACCESSED0 & self::$DIGEST) !== 0;
    }

    public function isNoRe(){
        return ($this->ACCESSED1 & self::$READ) !== 0;
    }

    public function isB(){
        return $this->isM() && $this->isG();
    }

    public function isU(){
        return $this->isM() && $this->isNoRe();
    }

    public function isO(){
        return $this->isG() && $this->isNoRe();
    }

    public function is8(){
        return $this->isB() && $this->isNoRe();
    }

    public function isSharp(){
        return ($this->ACCESSED0 & self::$SIGN) !== 0;
    }

    public function isPercent(){
        return ($this->ACCESSED0 & self::$PERCENT) !== 0;
    }

    public function isX(){
        return ($this->ACCESSED1 & self::$DEL) !== 0;
    }

    public function isCommend(){
        return ($this->ACCESSED1 & self::$COMMEND) !== 0;
    }

    public function isCensor(){
        return ($this->ACCESSED1 & self::$CENSOR) !== 0;
    }

    public function isTex(){
        return ($this->ACCESSED1 & self::$TEX) !== 0;
    }
}

class ArchiveNullException extends Exception {}
class ArchiveFileNullException extends Exception {}
class ArchiveAttException extends Exception {}
?>
