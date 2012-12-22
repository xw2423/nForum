<?php
class WidgetController extends ApiAppController {
    public function index(){
        if(!isset($this->params['name']))
            $this->error();

        $name = $this->params['name'];
        App::import('vendor', 'model/widget');
        try{
            $widget = Widget::getInstance($name);
        }catch(WidgetNullException $e){
            $this->error($e->getMessage());
        }

        $wrapper = Wrapper::getInstance();
        $data = $wrapper->widget($widget);
        $list = $widget->wGetList();
        if(!is_array($list['v'])){
            $this->error('no such widget');
        }
        switch($name){
            case 'topten':
            case 'recommend':
                App::import('vendor', array('model/board', 'model/threads'));
                $article = array();
                foreach($list['v'] as $v){
                    if(isset($v['url'])){
                        $ret = array();
                        preg_match("|^/article/(.*?)/(.*?)$|", $v['url'], $ret);
                        if(empty($ret[1]) || empty($ret[2]))
                            continue;
                        $board = $ret[1];
                        $id = $ret[2];
                        if($widget->wGetName() == 'topten'){
                            $text = $v['text'];
                            $text = preg_replace("|<[^>]*?>|", '', $text);
                            if(preg_match("/\((\d+)\)$/", $text, $c)){
                                $c = $c[1];
                            }else{
                                $c = 0;
                            }
                        }
                        try{
                            $t = $wrapper->article(Threads::getInstance($id, Board::getInstance($board)), array('threads' => true));
                            if($widget->wGetName() == 'topten'){
                                $t['id_count'] = $c;
                                $t['title'] .= "($c)";
                            }
                            $article[] = $t;
                        }catch(Exception $e){
                            continue;
                        }
                    }
                }
                $data['article'] = $article;
                break;
            default:
                $this->error('no such widget');
        }
        $this->set('data', $data);
    }
}
