<?php
class BuildassetShell extends Shell {
    public function main() {
        App::import('vendor', 'inc/packer');
        $js_pack = Configure::read("Asset.filter.js");
        $time = date('YmdHis', time());
        $p = new Packer();
        $js_out = $css_out = '';

        /* handle js*/
        $js = array('jquery-1.7.min.js'
                ,'jquery-ui-1.8.16.min.js'
                ,'underscore-min.js'
                ,'backbone-min.js'
                ,'jquery.tools.min.js'
                ,'jquery.cookie.js'
                ,'jquery.simpletree.js'
                ,'forum.config.js'
                ,'forum.lib.js'
                ,'forum.xwidget.js'
                ,'forum.xubb.js'
        );

        foreach($js as $v){
            $js_out .= $js_pack?$p->pack(APP . 'www/js/' . $v, 'js', false):file_get_contents(APP . 'www/js/' . $v);
        }

        $js_file = 'pack' . $time . '.js';
        file_put_contents(APP . 'www/js/' . $js_file, $js_out);
        /* handle js end */

        /* handle css */
        $css = array('common.css'
            ,'jquery-ui-1.8.16.css'
            ,'ansi.css'
            ,'ubb.css'
        );
        foreach($css as $v){
            $css_out .= $p->pack(APP . 'www/css/' . $v, 'css', false);
        }
        $css_file = 'pack' . $time . '.css';
        file_put_contents(APP . 'www/css/' . $css_file, $css_out);
        /* handle css end */

        $old = nforum_cache_read('asset_pack');
        if(is_array($old)){
            @unlink(APP . 'www/js/' . $old['js']);
            @unlink(APP . 'www/css/' . $old['css']);
        }
        $asset = array('js' => $js_file, 'css'=> $css_file);
        nforum_cache_write('asset_pack', $asset);
    }
}
?>
