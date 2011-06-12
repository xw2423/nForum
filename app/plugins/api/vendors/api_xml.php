<?php

class ApiXml {
    public static function encode($var, $tag = 'xml', $xml = null){
        if (is_null($xml))
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$tag />");

        self::_encode($var, $tag, $xml, 0);
        return $xml->asXML();

    }

    private static function _encode($var, $tag, &$xml, $dep){
        switch (gettype($var)){
            case 'boolean':
                $xml->addChild($tag, $var ? 'true' : 'false');
                break;

            case 'NULL':
                $xml->addChild($tag);
                break;

            case 'integer':
            case 'double':
            case 'float':
                $xml->addChild($tag, (string)$var);
                break;

            case 'string':
                if(strlen($var) == 0)
                    $xml->addChild($tag);
                else
                    $xml->addChild($tag, htmlspecialchars($var));
                break;

            case 'array':
                $child = ($dep === 0)?$xml:$xml->addChild($tag);
                if (is_array($var) && count($var) && (array_keys($var) !== range(0, count($var) - 1))) {
                    foreach($var as $k => $v){
                        self::_encode($v, $k, $child, $dep + 1);
                    }
                }else{
                    foreach($var as $v){
                        self::_encode($v, 'item', $child, $dep + 1);
                    }
                }
                break;

            case 'object':
                $vars = get_object_vars($var);
                foreach($vars as $k => $v){
                    $child = $xml->addChild($k);
                    $xml = self::_encode($v, $k, $child, $dep + 1);
                }
        }
    }
}
?>
