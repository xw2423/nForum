<?php

class Wrapper {

    private static $_instance = null;

    public static function getInstance(){
        if(self::$_instance == null){
            self::$_instance = new Wrapper();
        }
        return self::$_instance;
    }

    public function article($article, $options = array()){
        $default = array('threads' => false
            ,'single' => false
            ,'content' => false
        );
        $options = $this->_init_options($options, $default);

        $u = User::getInstance();
        $ret = array();
        $ret['id'] = $article->ID;
        $ret['group_id'] = $article->GROUPID;
        $ret['reply_id'] = $article->REID;
        $ret['flag'] = $this->_article_get_flag($article);
        $ret['position'] = $article->getPos();
        $ret['is_top'] = $article->isTop();
        $ret['is_subject'] = $article->isSubject();
        $ret['has_attachment'] = $article->hasAttach();
        $ret['is_admin'] = $article->hasEditPerm($u);
        $ret['title'] = $article->TITLE;
        try{
            $ret['user'] = $this->user(User::getInstance($article->OWNER));
        }catch(Exception $e){
            $ret['user'] = $article->OWNER;
        }
        $ret['post_time'] = $article->POSTTIME;
        $ret['board_name'] = $article->getBoard()->NAME;
        if($options['content']){
            $ret['content'] = $article->getContent();
            $ret['attachment'] = $this->attachment($article);
        }
        if($options['single']){
            $ret['previous_id'] = ($tmp = $article->pre())?$tmp->ID:null;
            $ret['next_id'] = ($tmp = $article->next())?$tmp->ID:null;
            $ret['threads_previous_id'] = ($tmp = $article->tPre())?$tmp->ID:null;
            $ret['threads_next_id'] = ($tmp = $article->tNext())?$tmp->ID:null;
        }
        if($options['threads']){
            $ret['id'] = $article->LAST->GROUPID;
            if(!$article->isSubject())
                //the threads deleted, user is null
                $ret['user'] = null;
            $ret['reply_count'] = $article->articleNum;
            $ret['last_reply_user_id'] = $article->LAST->OWNER;
            $ret['last_reply_time'] = $article->LAST->POSTTIME;
        }

        return $ret;
    }

    public function user($user){
        $static = Configure::read('site.static');
        $base = Configure::read('site.prefix');
        $u = User::getInstance();
        $hide = ($user->getCustom('userdefine0', 29) == 0);
        $super = ($u->userid == $user->userid || $u->isAdmin());
        $ret = array();
        $ret['id'] = $user->userid;
        $ret['user_name'] = $user->username;
        $ret['face_url'] = $static . $base . $user->getFace();
        $ret['face_width'] = $user->userface_width;
        $ret['face_height'] = $user->userface_height;
        if($hide && !$super){
            $ret['gender'] = 'n';
            $ret['astro'] = '';
        }else{
            App::import("vendor", "inc/astro");
            $astro = Astro::getAstro($user->birthmonth, $user->birthday);
            $ret['gender'] = ($user->gender == 77)?'m':'f';
            $ret['astro'] = ($astro['name'] == '')?'':$astro['name'];
        }
        $ret['life'] = $user->getLife();
        $ret['qq'] = $user->OICQ;
        $ret['msn'] = $user->MSN;
        $ret['home_page'] = $user->homepage;
        $ret['level'] = $user->getLevel();

        //whether online or not for all user may be the user hide
        $ret['is_online'] = $user->isOnline();

        $ret['post_count'] = $user->numposts;
        $ret['last_login_time'] = $user->lastlogin;
        $ret['last_login_ip'] = $user->lasthost;
        $ret['is_hide'] = $hide;
        $ret['is_register'] = $user->isReg();
        if($super){
            $ret['is_admin'] = $user->isAdmin();
            $ret['first_login_time'] = $user->firstlogin;
            $ret['login_count'] = $user->numlogins;
            $ret['stay_count'] = $user->stay;
        }

        return $ret;
    }

    public function board($board, $options = array()){
        $default = array('status' => false
        );
        $options = $this->_init_options($options, $default);

        $u = User::getInstance();
        $ret = array();
        $ret['name'] = $board->NAME;
        $ret['manager'] = $board->BM;
        $ret['description'] = $board->DESC;
        $ret['class'] = $board->CLASS;
        $ret['section'] = $board->SECNUM;
        if($options['status']){
            $ret['post_today_count'] = $board->getTodayNum();
            $ret['post_threads_count'] = $board->getTypeNum();
            $ret['post_all_count'] = $board->getTypeNum(Board::$NORMAL);
            $ret['user_online_count'] = $board->CURRENTUSERS;
            $ret['is_read_only'] = $board->isReadOnly();
            $ret['is_no_reply'] = $board->isNoReply();
            $ret['allow_attachment'] = $board->isAttach();
            $ret['allow_anonymous'] = $board->isAnony();
            $ret['allow_outgo'] = $board->isOutgo();
            $ret['allow_post'] = $board->hasPostPerm($u);
        }

        return $ret;
    }

    public function section($section){
        $ret = array();
        $ret['name'] = $section->getName();
        $ret['description'] = $section->getDesc();
        $ret['is_root'] = $section->isRoot();
        $parent = $section->getParent();
        $ret['parent'] = (null === $parent)?null:$parent->getName();

        return $ret;
    }

    public function attachment($archive){
        $domain = Configure::read('site.domain');
        $base = Configure::read('site.prefix');
        $abase = Configure::read('plugins.api.base');
        if(!is_array($archive)){
            $list = $archive->getAttList(false);
            $url_prefix = $domain  . $base . $abase . '/attachment';
        }else{
            $list = $archive;
            $url_prefix = '';
        }
        $ret = array();
        $size = 0;
        $num = count($list);
        foreach($list as $v){
            $size += intval($v['size']);
            $v['size'] = nforum_size_format($v['size']);
            $tmp = array('name' => $v['name']
                ,'url' => ('' === $url_prefix)?'':($url_prefix . $archive->getAttLink($v['pos']))
                ,'size' => $v['size']
                ,'thumbnail_small' => ''
                ,'thumbnail_middle' => ''
            );
            if('' !== $tmp['url'] && in_array(strtolower(substr(strrchr($v['name'], "."), 1)), array('jpg', 'jpeg', 'png', 'gif'))){
                $tmp['thumbnail_small'] = $tmp['url'] . '/small';
                $tmp['thumbnail_middle'] = $tmp['url'] . '/middle';
            }
            $ret[] = $tmp;
        }
        $upload = Configure::read("article");
        return array('file' => $ret, 'remain_space' => nforum_size_format($upload['att_size'] - $size), 'remain_count' => $upload['att_num'] - $num);
    }

    public function mailbox(){
        App::import("vendor", "model/mail");
        $ret = array();
        $info = MailBox::getInfo(User::getInstance());
        $ret['new_mail'] = $info['newmail'];
        $ret['full_mail'] = $info['full'];
        $ret['space_used'] = MailBox::getSpace() . 'KB';
        $ret['can_send'] = Mail::canSend();

        return $ret;
    }

    public function mail($mail, $options = array()){
        $default = array('content' => false);
        $options = $this->_init_options($options, $default);

        $u = User::getInstance();
        $ret = array();
        $ret['index'] = $mail->num;
        $ret['is_m'] = $mail->isM();
        $ret['is_read'] = $mail->isRead();
        $ret['is_reply'] = $mail->isReply();
        $ret['has_attachment'] = $mail->hasAttach();
        $ret['title'] = $mail->TITLE;
        try{
            $ret['user'] = $this->user(User::getInstance($mail->OWNER));
        }catch(Exception $e){
            $ret['user'] = $mail->OWNER;
        }
        $ret['post_time'] = $mail->POSTTIME;
        $ret['box_name'] = $mail->getBox()->desc;
        if($options['content']){
            $ret['content'] = $mail->getContent();
            $ret['attachment'] = $this->attachment($mail);
        }

        return $ret;
    }

    public function favorite($board){
        $ret = array();
        $ret['level'] = $board->BID;
        $ret['description'] = $board->DESC;
        $ret['position'] = $board->NPOS;

        return $ret;

    }

    public function refer($refer){
        App::import("vendor", "model/refer");
        $ret = array();
        $ret['index'] = $refer['INDEX'];
        $ret['id'] = $refer['ID'];
        $ret['group_id'] = $refer['GROUP_ID'];
        $ret['reply_id'] = $refer['RE_ID'];
        $ret['board_name'] = $refer['BOARD'];
        try{
            $ret['user'] = $this->user(User::getInstance($refer['USER']));
        }catch(Exception $e){
            $ret['user'] = $refer['USER'];
        }
        $ret['title'] = $refer['TITLE'];
        $ret['time'] = $refer['TIME'];
        $ret['is_read'] = $refer['FLAG'] === Refer::$FLAG_READ;
        return $ret;
    }

    public function widget($widget){
        $ret = array();
        $ret['name'] = $widget->wGetName();
        $title = $widget->wGetTitle();
        $ret['title'] = $title['text'];
        $ret['time'] = $widget->wGetTime();
        return $ret;
    }

    public function page($page){
        $ret = array();
        $ret['page_all_count'] = $page->getTotalPage();
        $ret['page_current_count'] = $page->getCurPage();
        $ret['item_page_count'] = $page->getCurNum();
        $ret['item_all_count'] = $page->getTotalNum();
        return $ret;
    }

    private function _init_options($options, $default = array()){
        foreach(array_keys($default) as $k){
            if(isset($options[$k]))
                $default[$k] = $options[$k];
        }
        return $default;
    }

    private function _article_get_flag($v){
        if($v->is8())
            return '8';
        if($v->isO())
            return 'o';
        if($v->isU())
            return 'u';
        if($v->isB())
            return 'b';
        if($v->isNoRe())
            return ';';
        if($v->isG())
            return 'g';
        if($v->isM())
            return 'm';
        return '';
    }
}
?>
