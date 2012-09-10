<?php
class WeatherShell extends Shell {
    public function main() {
        $week = array("日", "一", "二", "三", "四", "五", "六");
        $date[] = "周".$week[intval(date("w"))];
        $date[] = "周".$week[intval(date("w",time() + 24*60*60))];
        $date[] = "周".$week[intval(date("w",time() + 2*24*60*60))];
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
                        $curStatB .= ("转".$curStatE);
                        $img .= ("" . $this->st2img($curStatE));
                    }
                    if($curWind1 != $curWind2){
                        $curWind1 .= ("至".$curWind2."级");
                    }else{
                        $curWind1 .= "级";
                    }

                    $res .= "|".$date[$j]." ".$curStatB." ".$curTemp1."℃－".$curTemp2."℃"."&风力:".$curWind1.(empty($zwx)?"":" 紫外线:".$zwx)."#".$img;
                }
            }
        }
        nforum_cache_write('weather_day', substr($res, 1));
    }

    public function st2img($st){
        if(preg_match("/晴/", $st))
            return 1;
        if(preg_match("/多云/", $st))
            return 2;
        if(preg_match("/阴|雾/", $st))
            return 3;
        if(preg_match("/雨夹雪|小雨/", $st))
            return 4;
        if(preg_match("/阵雨|雷/", $st))
            return 8;
        if(preg_match("/雨/", $st))
            return 5;
        if(preg_match("/阵雪|小雪/", $st))
            return 6;
        if(preg_match("/雪/", $st))
            return 7;
        return false;
    }
}
?>

