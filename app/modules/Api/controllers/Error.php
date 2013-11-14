<?php
class ErrorController extends NF_ApiController {
    public function errorAction($exception = null) {
        if(is_a($exception, 'NF_ERRORException')){
            $code = $exception->getMessage();
            $code = is_null($code)?ECode::$SYS_ERROR:$code;
            $msg = ECode::msg($code);
        }else if(is_a($exception, 'NF_ERROR404Exception')
            || is_a($exception, 'Yaf_Exception')
            || null === $exception){
            $code = ECode::$SYS_404;
            $msg = ECode::msg($code);
        }else{
            $code = ECode::$SYS_ERROR;
            $msg = 'sys exception: "' . $exception->getMessage() . '" in ' . $exception->getFile();
        }
        $req = str_replace($this->_abase, '', $this->getRequest()->url . '.' . $this->getRequest()->ext);
        $_error = array('request' => $req, 'code'=> $code, 'msg' => $msg);
        $this->set('data', $_error);
        $this->name = 'error';
    }
}
