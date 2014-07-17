<div class="mbar">
    <ul>
        <li class="selected"><a href="<{$base}>/user/info">用户设置</a></li>
        <li><a href="<{$base}>/mail">用户信件</a></li>
        <li><a href="<{$base}>/refer">文章提醒</a></li>
        <li><a href="<{$base}>/friend">好友/黑名单</a></li>
        <li><a href="<{$base}>/fav">收藏版面</a></li>
    </ul>
</div>
<div class="c-mbar">
    <ul>
        <li><a href="<{$base}>/user/info"><samp class="ico-pos-dot"></samp>基本资料</a></li>
        <li><a href="<{$base}>/user/passwd"><samp class="ico-pos-dot"></samp>昵称密码</a></li>
        <li><a href="<{$base}>/user/custom" class="select"><samp class="ico-pos-dot"></samp>自定义参数</a></li>
    </ul>
</div>
<div class="b-content corner">
    <div id="c_content" class="corner">
        <form action="<{$base}>/user/ajax_custom.json" method="post">
        <table id="d_user" width="100%" cellpadding="0" cellspacing="0" border="0">
<{foreach from=$custom item=item}>
            <tr>
                <td class="u-title-1"><span><{$item.name}>:</span><{$item.desc}></td>
                <td class="u-title-2">
                    <input type="radio" <{if $item.val == 1}>checked="true"<{/if}> name="<{$item.id}>" value="1" /><{$item.yes}>
                    <input type="radio" <{if $item.val == 0}>checked="true"<{/if}> name="<{$item.id}>" value="0" /><{$item.no}></td>
            </tr>
<{/foreach}>
        </table>
        <div class="b-op u-op"><input type="submit" class="button" value="提交修改" /></div>
        </form>
    </div>
</div>
