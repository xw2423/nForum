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
        }catch(UserNullException $e){
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
        $ret = array();
        $ret['id'] = $user->userid;
        $ret['user_name'] = $user->username;
        $ret['face_url'] = $static . $base . $user->getFace();
        $ret['face_width'] = $user->userface_width;
        $ret['face_height'] = $user->userface_height;
        if($hide){
            $ret['gender'] = 'n';
            $ret['astro'] = '';
        }else{
            App::import("vendor", "inc/astro");
            $astro = Astro::getAstro($user->birthmonth, $user->birthday);
            $ret['gender'] = ($user->gender == 77)?'m':'f';
            $ret['astro'] = ($astro['name'] == 'δ֪')?'':$astro['name'];
        }
        $ret['life'] = $user->getLife();
        $ret['qq'] = $user->OICQ;
        $ret['msn'] = $user->MSN;
        $ret['home_page'] = $user->homepage;
        $ret['level'] = $user->getLevel();
        $ret['is_online'] = $user->isOnline();
        $ret['post_count'] = $user->numposts;
        $ret['last_login_time'] = $user->lastlogin;
        $ret['last_login_ip'] = $user->lasthost;
        if($u->userid == $user->userid || $u->isAdmin()){
            $ret['first_login_time'] = $user->firstlogin;
            $ret['login_count'] = $user->numlogins;
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
        $abase = Configure::read('plugins.api.base');
        if(!is_array($archive)){
            $list = $archive->getAttList(false);
            if(is_a($archive, 'Article')){
                $url_prefix = $domain  . $abase . '/attachment/' . $archive->getBoard()->NAME . '/' . $archive->ID . '/';
            }else if(is_a($archive, 'Mail')){
                $url_prefix = $domain  . $abase . '/attachment/' . $archive->getBox()->type . '/' . $archive->ID . '/';
            }
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
            $ret[] = array('name' => $v['name']
                ,'url' => ('' === $url_prefix)?'':($url_prefix . $v['pos'])
                ,'size' => $v['size']
            );
        }
        $upload = Configure::read("article");
        return array('file' => $ret, 'remain_space' => nforum_size_format($upload['att_size'] - $size), 'remain_count' => $upload['att_num'] - $num);
    }

    public function mailbox($mailbox){
        $ret = array();
        $ret['description'] = $mailbox->desc;
        $ret['unread_count'] = $mailbox->getNewNum();
        $ret['space_used'] = $mailbox->getSpace() . 'KB';
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
        }catch(UserNullException $e){
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
