<?php
/****************************************************
 * FileName: app/vendors/inc/json.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/

/**
 * class BYRJOSN
 * use in gbk charset,actually do not parse string
 * only have encode function
 *
 * @author xw
 * @todo decode function, a little complex
 */
class BYRJSON {
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
                return '"' . str_replace(array("\\", "\"", "\n", "\b", "\r", "\t"), array('\\\\', '\"', '\n', '\b', '\r', '\t'), $var)  . '"';    

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
?>
