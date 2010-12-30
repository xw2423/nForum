<?php
/****************************************************
 * FileName: app/vendors/inc/astro.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
/**
 * Astro model for nforum 
 * 
 * @author xw
 */
class Astro {
    public static function getAstro($month, $day){
        if (($month == 0) || ($day == 0) ){
            return array("name" => "Î´Öª", "file" => "");
        }  
        switch ($month){
            case 1:
                if ($day >= 21)  {
                    $ret = array("name" => "Ë®Æ¿×ù", "file" => "");
                }else{
                    $ret = array("name" => "Ä§ôÉ×ù", "file" => "");
                }
                break;
            case 2:
                if ($day >= 20)  {
                    $ret = array("name" => "Ë«Óã×ù", "file" => "");
                }else{
                    $ret = array("name" => "Ë®Æ¿×ù", "file" => "");
                }
                break;
            case 3:
                if ($day >= 21)  {
                    $ret = array("name" => "°×Ñò×ù", "file" => "");
                }else{
                    $ret = array("name" => "Ë«Óã×ù", "file" => "");
                }
                break;
            case 4:
                if ($day >= 21)  {
                    $ret = array("name" => "½ðÅ£×ù", "file" => "");
                }else{
                    $ret = array("name" => "°×Ñò×ù", "file" => "");
                }
                break;
            case 5:
                if ($day >= 22)  {
                    $ret = array("name" => "Ë«×Ó×ù", "file" => "");
                }else{
                    $ret = array("name" => "½ðÅ£×ù", "file" => "");
                }
                break;
            case 6:
                if ($day >= 22)  {
                    $ret = array("name" => "¾ÞÐ·×ù", "file" => "");
                }else{
                    $ret = array("name" => "Ë«×Ó×ù", "file" => "");
                }   
                break;
            case 7:
                if ($day >= 23)  {
                    $ret = array("name" => "Ê¨×Ó×ù", "file" => "");
                }else{
                    $ret = array("name" => "¾ÞÐ·×ù", "file" => "");
                }   
                break;
            case 8:
                if ($day >= 24){
                    $ret = array("name" => "´¦Å®×ù", "file" => "");
                }else{
                    $ret = array("name" => "Ê¨×Ó×ù", "file" => "");
                }
                break;
            case 9:
                if ($day >= 24)  {
                    $ret = array("name" => "Ìì³Ó×ù", "file" => "");
                }else{
                    $ret = array("name" => "´¦Å®×ù", "file" => "");
                }
                break;
            case 10:
                if ($day >= 24){
                    $ret = array("name" => "ÌìÐ«×ù", "file" => "");
                }else{
                    $ret = array("name" => "Ìì³Ó×ù", "file" => "");
                }
                break;
            case 11:
                if ($day >= 23)  {
                    $ret = array("name" => "ÉäÊÖ×ù", "file" => "");
                }else{
                    $ret = array("name" => "ÌìÐ«×ù", "file" => "");
                }
                break;
            case 12:
                if ($day >= 22)  {
                    $ret = array("name" => "Ä§ôÉ×ù", "file" => "");
                }else{
                    $ret = array("name" => "ÉäÊÖ×ù", "file" => "");
                }
                break;
            default:
                $ret = array("name" => "", "file" => "");
                break;
        }
        return $ret;
    }
}
?>
