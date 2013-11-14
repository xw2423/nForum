<?php
/**
 * plz add uaacl plugin
 */
$export['global'] = array();
$export['attachment']['att'] = array(
    //xunlei
    array("|^Mozilla/4\.0\s\(compatible;\sMSIE\s6\.0;Windows\sNT\s5\.1\)$|", false),
    array("|^Mozilla/4\.0\s\(compatible;\sMSIE\s6\.0;Windows\sNT\s5\.0\)$|", false),
    array("|^Mozilla/5\.0\s\(compatible;\sMSIE\s6\.0;Windows\sNT\s5\.0\)$|", false),
    array("|^Mozilla/4\.0\s\(compatible;\sMSIE\s6\.0;Windows\sNT\s5\.1;\s\)$|", false),
    array("|^Mozilla/4\.0 (compatible; MSIE 6\.0; Windows NT 5\.0; \.NET CLR 3\.5\.20706)$|", false),
    array("|^Mozilla/4.0\s\(compatible;\sMSIE\s6.0;\sWindows\sNT\s5.1;\sSV1;\s\.NET\sCLR\s1\.1\.4322;\s\.NET\sCLR\s2\.0\.50727\)$|")
);

$export['spider'] = array(
    array("|googlebot|i", false)
    ,array("|baiduspider|i", false)
    ,array("|bingbot|i", false)
    ,array("|youdaobot|i", false)
    ,array("|sogou.*spider|i", false)
    ,array("|soso.*spider|i", false)
    ,array("|voluniabot|i", false)
    ,array("|jikespider|i", false)
);
