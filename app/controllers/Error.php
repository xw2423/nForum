<?php
class ErrorController extends NF_Controller {

    public function errorAction($exception = null) {
        if(is_a($exception, 'NF_ERRORException')){
            $this->set('no_html_data', array(
                'ajax_st' => 0
                ,'ajax_code' => $exception->getMessage()
                ,'ajax_msg' => ECode::msg($exception->getMessage())
            ));
            $this->render('error');
        }else if(is_a($exception, 'NF_ERROR404Exception')
            || is_a($exception, 'Yaf_Exception')
            || !is_a($exception, 'Exception')){
            header('HTTP/1.1 404 Not Found');
            $this->set('siteName', c('site.name'));
            $this->render('error404');
        }else{
            exit('sys exception: "' . $exception->getMessage() . '" in ' . $exception->getFile());
        }
    }
}
