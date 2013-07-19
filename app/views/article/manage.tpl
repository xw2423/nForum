<style>
#a_manage samp{width:16px;height:17px}
.list-block {padding-top:6px;}
#a_manage .list-block ul{overflow:hidden;*zoom:1}
#a_manage .list-block li{float:left;margin-right:10px;width:220px;height:18px}
</style>
<script id="tmpl_manage" type="text/template">
<form id="a_manage" action="<%=action%>" method="post">
    <section class="list-block" id="manage_op">
        <header>单篇文章</header>
        <ul class="narrow">
            <li>
                <input name="i1" value="g" type="checkbox">&nbsp;<span>设置/取消g标记(<samp class="ico-pos-article-g"></samp>)</span>
            </li>
            <li>
                <input name="i2" value="m" type="checkbox">&nbsp;<span>设置/取消m标记(<samp class="ico-pos-article-m"></samp>)</span>
            </li>
            <li>
                <input name="i3" value=";" type="checkbox">&nbsp;<span>设置/取消不可回复标记(<samp class="ico-pos-article-lock"></samp>)</span>
            </li>
            <li>
                <input name="i4" value="top" type="checkbox">&nbsp;<span>主题帖设置/取消置顶(<samp class="ico-pos-article-top"></samp>)</span>
            </li>
            <li>
                <input name="i5" value="%" type="checkbox">&nbsp;<span>设置/取消％标记(％)</span>
            </li>
            <li>
                <input name="i6" value="x" type="checkbox">&nbsp;<span>设置/取消X标记(X)</span>
            </li>
            <li>
                <input name="i7" value="sharp" type="checkbox">&nbsp;<span>设置/取消﹟标记(﹟)</span>
            </li>
        </ul>
    </section>
    <section class="list-block" id="manage_top">
        <header>同主题(从当前文章开始)</header>
        <ul class="narrow">
            <li>
                <input name="i1" value="d" type="checkbox">&nbsp;<span>同主题删除</span>
            </li>
            <li>
                <input name="i2" value="dx" type="checkbox">&nbsp;<span>同主题X文章删除</span>
            </li>
            <li>
                <input name="i3" value="m" type="checkbox">&nbsp;<span>设置m标记(<samp class="ico-pos-article-m"></samp>)</span>
            </li>
            <li>
                <input name="i4" value="um" type="checkbox">&nbsp;<span>取消m标记(<samp class="ico-pos-article-m"></samp>)</span>
            </li>
            <li>
                <input name="i5" value=";" type="checkbox">&nbsp;<span>设置不可回复标记(<samp class="ico-pos-article-lock"></samp>)</span>
            </li>
            <li>
                <input name="i6" value="u;" type="checkbox">&nbsp;<span>取消不可回复标记(<samp class="ico-pos-article-lock"></samp>)</span>
            </li>
            <li>
                <input name="i7" value="x" type="checkbox">&nbsp;<span>设置X标记(X)</span>
            </li>
            <li>
                <input name="i8" value="ux" type="checkbox">&nbsp;<span>取消X标记(X)</span>
            </li>
        </ul>
    </section>
    <input type="hidden" id="a_manage_gid"name="gid" value="<%=gid%>" />
</form>
</script>
<script id="tmpl_deny" type="text/template">
<form id="a_deny" action="<%=action%>" method="post">
    <section class="list-block">
        <header>在<%=board%>版封禁<%=userid%></header>
        <ul class="narrow">
            <li>
                <span>封禁理由:</span><input type="text" class="input-text" name="reason" /><select id="a_deny_reasons"><option value="">选择封禁理由</option>
<%=_.reduce(reason,function(ret,item){
    ret += ('<option value="' + item.desc + '">' + item.desc + '</option>');
    return ret;
},'')%>
                </select>
            </li>
            <li>
                <span>封禁天数:</span><input type="text" class="input-text" name="day"><span>(1-<%=maxday%>天)</span>
            </li>
        </ul>
    </section>
</form>
</script>
