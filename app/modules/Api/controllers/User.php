<?php
class UserController extends NF_ApiController {

    public function queryAction(){
        $id = trim($this->params['id']);
        try{
            $u = User::getInstance($id);
        }catch(UserNullException $e){
            $this->error(ECode::$USER_NOID);
        }
        $wrapper = Wrapper::getInstance();
        $this->set('data', $wrapper->user($u));
    }

    public function loginAction(){
        $u = User::getInstance();
        $wrapper = Wrapper::getInstance();
        $data = $wrapper->user($u);
        $this->set('data', $data);
    }

    public function logoutAction(){
        $u = User::getInstance();
        $wrapper = Wrapper::getInstance();
        $this->set('data', $wrapper->user($u));
        NF_ApiSession::getInstance()->logout();
    }
}
