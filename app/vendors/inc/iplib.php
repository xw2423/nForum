<?php
/****************************************************
 * FileName: app/vendors/inc/iplib.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
define('MASK_NUM_V4', '32');
define('MASK_NUM_V6', '64');

/**
 * exchange ipv6 text to array(int[high], int[low]) for 64-bit system
 * @param $ipv6_address(string) ipv6 text
 * @return array(high(64bit), low(64bit)) 
 * @author xw
 */
function ipv62long($ipv6_address){/*{{{*/
    $res = array();
    $num = 0;
    $i = 0;

    $str = inet_pton($ipv6_address);
    while($i <= 15){
        $num = $num  << 8;
        $num += ord($str[$i]);
        if(($i + 1) % 8 == 0){
            $res[] = $num;
            $num = 0;
        }
        $i ++;
    }
    return $res;
}/*}}}*/

/**
 * exchange array(int[high], int[low]) to ipv6 text for 64-bit system
 * @param array(high(64bit), low(64bit)) 
 * @return String ipv6 text
 * @author xw
 */
function long2ipv6($ipv6_num = array()){/*{{{*/
    $i = $j = 0;
    $res = "";
    while($i <= 1){
        $j = 0;
        $str = "";
        $num = $ipv6_num[$i];
        while($j <= 7){
            $str = chr($num % 256) . $str;     
            $num = $num >> 8;
            $j ++;
        }
        $res .= $str;
        $i ++;
    }
    return inet_ntop($res);
}/*}}}*/

/**
 * get a mask with $n-1
 * @param $n(int) number of 1 from left side
 * @param $t(int) max number of mask MASK_NUM_V4 default
 * @return int of mask value
 * @author xw
 */
function get_mask($n, $t = MASK_NUM_V4){/*{{{*/
    $ret = 0;
    $n = intval($n);
    while($t > 0){
        $ret = $ret << 1;
        if($n > 0){
            $ret = $ret | 1;
            $n --;
        }
        $t --;
    }
    return $ret;
}/*}}}*/

/**
 * get a ipv6 mask with $n-1
 * @param $n(int) number of 1 from left side
 * @return int of mask value
 * @author xw
 */
function get_mask_v6($n){/*{{{*/
    return get_mask($n, MASK_NUM_V6);
}/*}}}*/

/**
 * test whether is equal for two ip number under the mask
 * @param $num1(int) number of ip
 * @param $num2(int) number of ip
 * @param $mask number of ip
 * @param $type(int) max number of mask MASK_NUM_V4 default
 * @return boolean true if equal
 * @author xw
 */
function mask_equal($num1, $num2, $mask, $type = MASK_NUM_V4){/*{{{*/
    $num1 = intval($num1);
    $num2 = intval($num2);
    $mn = get_mask($mask,$type);
    return (($num1 & $mn) === ($num2 & $mn));
}/*}}}*/

/**
 * test whether is equal for two ip number under the mask
 * @param $num1(int) number of ip
 * @param $num2(int) number of ip
 * @param $mask number of ip
 * @return boolean true if equal
 * @author xw
 */
function mask_equal_v6($num1, $num2, $mask){/*{{{*/
    return mask_equal($num1, $num2, $mask, MASK_NUM_V6);
}/*}}}*/

/**
 * test whether $ip1 and $ip2 will include each other return the wide one
 * @param $ip1(array("ip", "mask")) number of ip
 * @param $ip2(array("ip", "mask")) number of ip
 * @param $type(int) max number of mask MASK_NUM_V4 default
 * @return the wide one or false
 * @author xw
 */
function ip_conflict($ip1, $ip2, $type = MASK_NUM_V4){/*{{{*/
    if($ip1['mask'] > $ip2['mask']){
        $mask = $ip2['mask'];
        if(mask_equal($ip1['ip'], $ip2['ip'], $mask, $type)){
            return $ip1;
        }
    }else if($ip1['mask'] < $ip2['mask']){
        $mask = $ip1['mask'];
        if(mask_equal($ip1['ip'], $ip2['ip'], $mask, $type)){
            return $ip2;
        }
    }else{
        if($ip1['ip'] == $ip2['ip'])
            return $ip1;
    }
    return false;
}/*}}}*/

/**
 * test whether $ip1 and $ip2 will include each other return the wide one
 * @param $ip1(array("ip", "mask")) number of ip
 * @param $ip2(array("ip", "mask")) number of ip
 * @return the wide one or false
 * @author xw
 */
function ip_conflict_v6($ip1, $ip2){/*{{{*/
    return ip_conflict($ip1, $ip2, MASK_NUM_V6);
}/*}}}*/
?>
