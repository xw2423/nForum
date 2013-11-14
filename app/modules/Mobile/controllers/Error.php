<?php
class ErrorController extends NF_MobileController {

    public function errorAction($exception = null) {
        if(is_a($exception, 'NF_ERRORException')){
            $code = $exception->getMessage();
        }else if(is_a($exception, 'NF_ERROR404Exception')
            || is_a($exception, 'Yaf_Exception')
            || null === $exception){
            header('HTTP/1.1 404 Not Found');
            $code = '该页面不存在';
        }else{
            echo 'sys exception: "' . $exception->getMessage() . '" in ' . $exception->getFile();
            exit();
        }
        $this->_msg = ECode::msg($code);
        $this->notice = "发生错误";
        $this->render('error');
    }
}
