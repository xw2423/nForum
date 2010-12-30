<?php
/**
 * exif component for nforum 
 * @author xw       
 */
class ExifComponent extends Object {    
    public function initialize(&$controller) {
        $this->controller = $controller;
    }

    public function format($file){
        $info = $this->getInfo($file);
        if($info === false)
            return false;
        return '【器材信息】'.$this->_format($info[1]).$this->_format($info[14]).$this->_format($info[8]).'\n'.
            '【拍摄参数】'.$this->_format($info[3]).$this->_format($info[13]).$this->_format($info[4]).$this->_format($info[5]).'\n';
    }

    public function getInfo($file){
        @$exif = exif_read_data($file,"IFD0"); 
        if($exif === false){
            return false;
        }
        $exif = exif_read_data($file, 0, true);
        if(isset($exif['EXIF']['ExposureTime'])){
            $expTime = split("/", $exif['EXIF']['ExposureTime']);
            if(intval($expTime[1]) >= intval($expTime[0])*10)
                $expTime = "1/" . intval($expTime[1])/intval($expTime[0]) . "s";
            else
                $expTime = intval($expTime[0])/intval($expTime[1]) . "s";
        }
        if(isset($exif['EXIF']['ExposureBiasValue'])){
            $expbv = split("/", $exif['EXIF']['ExposureBiasValue']);
            $expbv = sprintf("%.2f", intval($expbv[0])/intval($expbv[1]))."EV";
        }
        if(isset($exif['EXIF']['FocalLength'])){
            $flen = split("/", $exif['EXIF']['FocalLength']);
            $flen = intval($flen[0])/intval($flen[1]) . "mm";
        }
        @$ret = array(
            array("制造商" , $exif['IFD0']['Make']), 
            array("型号" , $exif['IFD0']['Model']), 
            array("日期" , date("Y-m-d H:i:s"),$exif['FILE']['FileDateTime']),
            array("大小" , $exif['COMPUTED']['Width']."*".$exif['COMPUTED']['Height']),
            array("光圈值" , $exif['COMPUTED']['ApertureFNumber']), 
            array("曝光时间" , $expTime), 
            array("测光模式" , $this->_infoMap($exif['EXIF']['MeteringMode'],$this->_MeteringMode_arr)), 
            array("光源" , $this->_infoMap($exif['EXIF']['LightSource'], $this->_Lightsource_arr)), 
            array("闪光灯" , $this->_infoMap($exif['EXIF']['Flash'], $this->_Flash_arr)), 
            array("曝光模式" , ($exif['EXIF']['ExposureMode']==1?"手动":"自动")), 
            array("白平衡" , ($exif['EXIF']['WhiteBalance']==1?"手动":"自动")), 
            array("曝光程序" , $this->_infoMap($exif['EXIF']['ExposureProgram'], $this->_ExposureProgram)),
            array("曝光补偿" , $expbv), 
            array("ISO感光度" , $exif['EXIF']['ISOSpeedRatings']), 
            array("焦距" , $flen), 
            array("创建软件" , $exif['IFD0']['Software']) 
        );
        return $ret;

    }

    private function _format($item){
        if(!is_array($item) || empty($item[1]))
            return "";
        return '['.$item[0].']'.$item[1]." ";
    }

    private function _infoMap($key, $arr){
        if(array_key_exists($key, $arr))
            return $arr[$key];
        return "未知";
    }

    private $_MeteringMode_arr = array( "0" => "未知", "1" => "平均", "2" => "中央重点平均测光", "3" => "点测", "4" => "分区", "5" => "评估", "6" => "局部", "255" => "其他" ); 
    private $_Lightsource_arr = array( "0" => "未知", "1" => "日光", "2" => "荧光灯", "3" => "钨丝灯", "10" => "闪光灯", "17" => "标准灯光A", "18" => "标准灯光B", "19" => "标准灯光C", "20" => "D55", "21" => "D65", "22" => "D75", "255" => "其他" ); 
    private $_Flash_arr = array( "0" => "关", "1" => "开", "2" => "开", "5" => "flash fired but strobe return light not detected", "6" => "开", "7" => "flash fired and strobe return light detected", "16"=>"关"); 
    private $_ExposureProgram = array("未定义", "手动", "标准程序", "光圈优先", "快门优先", "景深优先", "运动模式", "肖像模式", "风景模式"); 
}
