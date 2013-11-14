<style type="text/css">
.b-head{text-align:center;}
.b-head span{ font-size:14px;}
.b-content{
    padding:30px;
}
.error{border-bottom:1px solid #CCC;}
.error h5{font-size:14px;}
.error div{margin-top:10px;}
.error ul{padding:12px 30px 28px;}
.error li{padding:5px 0 5px 20px}
.error li samp {
    height:14px;
    margin-right:6px;
    width:8px;
}
.error-op{text-align:center;padding-top:20px;}
.error-su{width:100px;padding:4px 0;*padding:4px 0 1px}
</style>
<{include file="s_nav.tpl" nav_left="论坛错误信息"}>
<div class="b-content corner">
    <div class="error">
        <h5>产生错误的可能原因：</h5>
            <ul>
                <li><samp class="ico-pos-dot"></samp><{$no_html_data.ajax_msg}></li>
            </ul>
    </div>
    <div class="error-op">
    <input class="button error-su" type="button" onclick="javascript:history.go(-1)" value="返回上一页" />
    </div>
</div>
