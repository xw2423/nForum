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
        switch(array_shift(split('-', $name))){
            case 'topten':
            case 'recommend':
                if(!is_array($list['v'])) break;
                App::import('vendor', array('model/board', 'model/threads'));
                $article = array();
                foreach($list['v'] as $v){
                    if(isset($v['url'])){
                        $ret = array();
                        preg_match("|^/article/(.*?)/(.*?)$|", $v['url'], $ret);
                        if(empty($ret[1]) || empty($ret[2]))
                            continue;
                        $board = rawurldecode($ret[1]);
                        $id = (int)$ret[2];
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
            case 'section':
                if(!is_array($list[0]['v']['v'])) break;
                App::import('vendor', array('model/board', 'model/threads'));
                $article = array();
                foreach($list[0]['v']['v'] as $v){
                    if(isset($v['text'])){
                        $ret = array();
                        preg_match("|/article/(.+?)/(\d+)|", $v['text'], $ret);
                        if(empty($ret[1]) || empty($ret[2]))
                            continue;
                        $board = rawurldecode($ret[1]);
                        $id = (int)$ret[2];
                        try{
                            $article[] = $wrapper->article(Threads::getInstance($id, Board::getInstance($board)), array('threads' => true));
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
