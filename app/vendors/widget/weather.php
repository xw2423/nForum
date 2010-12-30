<?php
App::import("vendor", array("model/iwidget", "inc/db"));

class weatherWidget extends WidgetAdapter{
    public function wGetTitle(){ return array("text"=>"北京天气", "url"=>"/board/weather");}
    public function wGetTime(){
        if (!file_exists(CACHE . 'nforum/weather_day')) {
            return time();
        }
        return filemtime(CACHE . 'nforum/weather_day');
    }

    public function wGetList(){
        $prefix = configure::read('site.prefix');
        $res = nforum_cache_read('weather_day');
        $w = explode("|", (string)$res);
        if(!is_array($w) || count((array)$w) != 3)
            return $this->_error('该应用数据错误');
        $res = "";
        $color = array("red", "blue", "green");
        foreach($w as $k=>$v){
            $res .= '<ul><li style="height:40px;line-height:18px;padding-bottom:3px;border-bottom:1px solid #dce9f5">';
            $img = explode("#", $v);
            $content = explode("&", $img[0]);
            if(!isset($img[1]) || !isset($content[1]))
                return $this->_error('该应用数据错误');
            $date = $content[0];
            $content = $content[1];
            $img = $img[1];
            if(isset($img[1]))
                $res .= '<img src="'.$prefix.'/img/app/weather/'.$img[1].'.png"  style="float:right"/>';
            $res .= '<img src="'.$prefix.'/img/app/weather/'.$img[0].'.png" style="float:right;"/>';
            $res .= '<div style="color:'.$color[$k].';padding-top:5px">';
            $res .= $date . '<br />' . $content.'</div>';
            $res .= "</li></ul>";
        }
        return array("s"=>parent::$S_FREE, "v"=>array(
            array("text" => $res, "url"=>"")
        ));
    }
}
?>
