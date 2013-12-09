<?php
class NF_Json{
    public static function encode($var){
        switch (gettype($var)){
        case 'boolean':
            return $var ? 'true' : 'false';

        case 'NULL':
            return 'null';

        case 'integer':
            return (int) $var;

        case 'double':
        case 'float':
            return (float) $var;

        case 'string':
            $l = strlen($var);
            $o = '';
            $i = 0;
            for(;$i < $l;){
                if(ord($var[$i]) > 0x81){
                    if($i === $l - 1) break;
                    $o .= $var[$i] . $var[$i+1];
                    $i += 2;
                }else if($var[$i] === "\\" &&(
                    ($i >= 2 && ord($var[$i - 2]) >= 0x81 && ord($var[$i - 1]) >= 0x40)
                    || ($i >= 1 && ord($var[$i - 1]) >= 0x1 && ord($var[$i - 1]) <= 0x7e)
                    || $i === 0)){
                    $o .= "\\" . $var[$i++];
                }else if($var[$i] === "\""){
                    $o .= '\"';
                    $i++;
                }else if($var[$i] === "\n"){
                    $o .= '\n';
                    $i++;
                }else if($var[$i] === "\r"){
                    $o .= '\r';
                    $i++;
                }else if($var[$i] === "\t"){
                    $o .= '\t';
                    $i++;
                }else if(ord($var[$i]) === 0x8){
                    $o .= '\b';
                    $i++;
                }else if(ord($var[$i]) <= 0x1f){
                    $o .= ('\u00' . bin2hex($var[$i]));
                    $i++;
                }else{
                    $o .= $var[$i++];
                }
            }
            return '"' . $o . '"';

        case 'array':
            if (is_array($var) && count($var) && (array_keys($var) !== range(0, count($var) - 1))) {
                $prop= array_map(array("self", "_name_value"), array_keys($var), array_values($var));
                return '{' . join(',', $prop) . '}';
            }
            $element = array_map(array("self", "encode"), $var);
            return '[' . join(',', $element) . ']';

        case 'object':
            $vars = get_object_vars($var);
            $prop= array_map(array("self", '_name_value'), array_keys($vars), array_values($vars));
            return '{' . join(',', $prop) . '}';
        }
    }

    private static function _name_value($name, $value){
        return self::encode(strval($name)) . ':' . self::encode($value);
    }
}
