<?php
class BuildassetShell extends NF_Shell {
    public function main($argc, $argv) {
        load('inc/packer');
        $js_pack = c('view.pack.js');
        $css_pack = c('view.pack.css');
        $time = date('YmdHis', time());
        define('JS', WWW . DS . 'js');
        define('CSS', WWW . DS . 'css');
        $p = new Packer();
        $js_out = $css_out = '';

        //delete old file
        $old = nforum_cache_read('asset_pack');
        if(is_array($old)){
            @unlink(JS . DS . $old['js']);
            @unlink(CSS . DS . $old['css']);
        }

        /* handle js*/
        self::line('build js......', false);
        $js = array('jquery-1.7.2.min.js'
                ,'jquery-ui-1.8.20.min.js'
                ,'jquery-ui-timepicker-addon.js'
                ,'underscore-min.js'
                ,'backbone-min.js'
                ,'plupload.min.js'
                ,'jquery.tools.min.js'
                ,'jquery.placeholder.min.js'
                ,'jquery.cookie.js'
                ,'jquery.simpletree.js'
                ,'jquery.jplayer.min.js'
                ,'jquery.xslider.min.js'
                ,'forum.config.js'
                ,'forum.lib.js'
                ,'forum.xwidget.js'
                ,'forum.xubb.js'
                ,'forum.keyboard.js'
        );

        foreach($js as $v){
            $js_out .= ($js_pack&&!strstr($v,'min.js'))?$p->pack(JS . DS . $v, 'js', false):file_get_contents(JS . DS . $v);
        }

        $js_file = 'pack_' . $this->_hash($js_out) . '.js';
        file_put_contents(JS . DS . $js_file, $js_out);
        self::line('done');
        /* handle js end */

        /* handle css */
        self::line('build css......', false);
        $css = array('common.css'
            ,'jquery-ui-1.8.20.css'
            ,'jplayer.blue.monday.css'
            ,'ansi.css'
            ,'ubb.css'
            ,'keyboard.css'
        );
        foreach($css as $v){
            $css_out .= $css_pack?$p->pack(CSS . DS . $v, 'css', false):file_get_contents(CSS . DS . $v);
        }
        $css_file = 'pack_' . $this->_hash($css_out) . '.css';
        file_put_contents(CSS . DS . $css_file, $css_out);
        self::line('done');
        /* handle css end */

        $asset = array('js' => $js_file, 'css'=> $css_file);
        nforum_cache_write('asset_pack', $asset);
    }

    private function _hash($content){
        return substr(hash('md5', $content), 0, 10);
    }
}
