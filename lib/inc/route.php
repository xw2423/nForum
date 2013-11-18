<?php
/**
 * nForum route
 * new NF_ROUTE($match, $route = array(), $verfiy = array(), $strict = false)
 *
 * $match: url format
 * $route: url params, support null
 * $verify: verify params in $match
 * $strict: route only when Request->url === $match
 */

class NF_Route implements Yaf_Route_Interface{

    private $_key_vars = array('module', 'controller', 'action');
    private $_routes = array();
    private $_match = array();
    private $_verify = array();
    private $_current = null;
    private $_strict = false;

    public function __construct ($match , $route = array(), $verify = array(), $strict = null){
        $route_add = array(array($match, $route, $verify));
        $var_null = $this->_get_null($route);
        foreach($var_null as $v){
            foreach($route_add as $r){
                $new_match = str_replace('/:' . $v, '', $r[0]);
                $new_route = $route;
                $new_verify = $verify;
                if(in_array($v, $this->_key_vars))
                    $new_route[$v] = 'index';
                unset($new_verify[$v]);
                $route_add[] = array($new_match, $new_route, $new_verify);
            }
        }
        foreach($route_add as $v){
            $this->_match[] = $v[0];
            $this->_verify[] = $v[2];
            $this->_routes[] = new Yaf_Route_Rewrite($v[0], $v[1], $v[2]);
        }

        if(null === $strict)
            $this->_strict = false === strpos($match, '/:');
        else
            $this->_strict = $strict;
    }

    public function route($request){
        foreach($this->_routes as $key=>$r){
            if($this->_strict && $this->_match[$key] !== $request->url) continue;
            if($r->route($request)){
                $params = $request->getParams();
                foreach($this->_verify[$key] as $k=>$v){
                    if(isset($params[$k]) && !preg_match("|^$v$|", $params[$k])){
                        foreach(array_keys($params) as $key) $request->setParam($key, null);
                        continue 2;
                    }
                }
                foreach($this->_key_vars as $v){
                    if(isset($params[$v]))
                        call_user_func_array(array($request, 'set' . ucfirst($v) . 'Name'), array($params[$v]));
                }
                $this->_current = $key;
                return true;
            }
        }
        return false;
    }

    private function _get_null($route){
        $tmp = array();
        foreach($route as $k=>$v){
            if($v === null) $tmp[] = $k;
        }
        return $tmp;
    }
}
