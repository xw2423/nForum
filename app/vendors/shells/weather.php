<?php
class WeatherShell extends Shell {
    public function main() {
        $week = array("��", "һ", "��", "��", "��", "��", "��");
        $date[] = "��".$week[intval(date("w"))];
        $date[] = "��".$week[intval(date("w",time() + 24*60*60))];
        $date[] = "��".$week[intval(date("w",time() + 2*24*60*60))];
        $file="http://php.weather.sina.com.cn/xml.php?city=%B1%B1%BE%A9&password=DJOYnieT8234jlsK&day=";
        $res = "";
        for($j = 0; $j <=2; $j++){
            $data=file_get_contents($file.$j);
            $xml_parser = xml_parser_create();
            xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 1);
            xml_parse_into_struct($xml_parser, $data, $vals);
            xml_parser_free($xml_parser);
            //print_r($vals);
            $encoding = Configure::read("App.encoding");
            for($i=0;$i<=count($vals)-1;$i++){
                if($vals[$i]['tag'] == "status1" && $vals[$i]['type'] == "complete"){
                    $curStatB = nforum_iconv("utf-8",$encoding,$vals[$i]['value']);
                }
                if($vals[$i]['tag'] == "status2" && $vals[$i]['type'] == "complete"){
                    $curStatE = nforum_iconv("utf-8",$encoding,$vals[$i]['value']);
                }
                if($vals[$i]['tag'] == "temperature1" && $vals[$i]['type'] == "complete"){
                    $curTemp1 = $vals[$i]['value'];
                }
                if($vals[$i]['tag'] == "temperature2" && $vals[$i]['type'] == "complete"){
                    $curTemp2 = $vals[$i]['value'];
                }
                if($vals[$i]['tag'] == "power1" && $vals[$i]['type'] == "complete"){
                    $curWind1 = nforum_iconv("utf-8",$encoding,$vals[$i]['value']);
                }
                if($vals[$i]['tag'] == "power2" && $vals[$i]['type'] == "complete"){
                    $curWind2 = nforum_iconv("utf-8",$encoding,$vals[$i]['value']);
                }
                if($vals[$i]['tag'] == "zwx_l" && $vals[$i]['type'] == "complete"){
                    $zwx = nforum_iconv("utf-8",$encoding,$vals[$i]['value']);
                }
                if($vals[$i]['tag'] == "Weather" && $vals[$i]['type'] == "open"){
                    $curDate = "";
                    $curStatB = "";
                    $curStatE = "";
                    $curTemp1 = "";
                    $curTemp2 = "";
                    $img = "";
                    $curWind1 = "";
                    $curWind2 = "";
                    $zwx = "";
                }
                if($vals[$i]['tag'] == "Weather" && $vals[$i]['type'] == "close"){
                    $img = $this->st2img($curStatB);
                    if($curStatB != $curStatE){
                        $curStatB .= ("ת".$curStatE);
                        $img .= ("" . $this->st2img($curStatE));
                    }
                    if($curWind1 != $curWind2){
                        $curWind1 .= ("��".$curWind2."��");
                    }else{
                        $curWind1 .= "��";
                    }
                    
                    $res .= "|".$date[$j]." ".$curStatB." ".$curTemp1."�棭".$curTemp2."��"."&����:".$curWind1.(empty($zwx)?"":" ������:".$zwx)."#".$img;
                }
            }
        }
        nforum_cache_write('weather_day', substr($res, 1));
    }

    public function st2img($st){
        if(preg_match("/��/", $st))
            return 1;
        if(preg_match("/����/", $st))
            return 2;
        if(preg_match("/��|��/", $st))
            return 3;
        if(preg_match("/����ѩ|С��/", $st))
            return 4;
        if(preg_match("/����|��/", $st))
            return 8;
        if(preg_match("/��/", $st))
            return 5;
        if(preg_match("/��ѩ|Сѩ/", $st))
            return 6;
        if(preg_match("/ѩ/", $st))
            return 7;
        return false;
    }
}
?>

