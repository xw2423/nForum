    	<div class="mbar">
        	<ul>
                <li><a href="<{$base}>/user/info">基本资料修改</a></li>
                <li><a href="<{$base}>/user/passwd">昵称密码修改</a></li>
                <li><a href="<{$base}>/user/custom">用户自定义参数</a></li>
                <li class="selected"><a href="<{$base}>/mail">用户信件</a></li>
                <li><a href="<{$base}>/refer">文章提醒</a></li>
                <li><a href="<{$base}>/friend">好友列表</a></li>
                <li><a href="<{$base}>/fav">收藏版面</a></li>
            </ul>					
        </div>
        <div class="c-mbar">
			<ul>
            	<li><a href="<{$base}>/mail/inbox" <{if $type=="inbox"}>class="select"<{/if}>><samp class="ico-pos-dot"></samp>收件箱</a></li>
                <li><a href="<{$base}>/mail/outbox" <{if $type=="outbox"}>class="select"<{/if}>><samp class="ico-pos-dot"></samp>发件箱</a></li>
                <li><a href="<{$base}>/mail/deleted" <{if $type=="deleted"}>class="select"<{/if}>><samp class="ico-pos-dot"></samp>垃圾箱</a></li>
                <li><a href="<{$base}>/friend"><samp class="ico-pos-dot"></samp>地址薄</a></li>
                <li><a href="<{$base}>/mail/send"><samp class="ico-pos-dot"></samp>撰写邮件</a></li>
            </ul>
		</div>	
        <div class="b-content">
			<form id="mail_clear" action="<{$base}>/mail/<{$type}>/ajax_delete.json" method="post">
			<input type="hidden" name="all" value="all" />
			</form>
			<form id="mail_form" action="<{$base}>/mail/<{$type}>/ajax_delete.json" method="post">
            <div class="mail-list">
                <div class="t-pre">
                    <div class="t-btn">
						<input type="checkbox" class="mail-select" />
						<input type="button" class="button mail-del" value="删除" />
						<input type="button" class="button mail-clear" value="全部删除" />
                    </div>
                    <div class="page">
                        <{include file="pagination.tpl" page_name='邮件总数'}>
                    </div>
                </div>
                <table class="m-table">
<{if isset($info)}>
<{foreach from=$info item=item}>
                	<tr <{if !($item.read)}>class="no-read"<{/if}>>
                    	<td class="title_1"><input type="checkbox" name="m_<{$item.num}>" class="mail-item"/></td>
						<td class="title_2"><a href="<{$base}>/user/query/<{$item.sender}>"><{$item.sender}></a></td>
                        <td class="title_3"><a href="<{$base}>/mail/<{$type}>/<{$item.num}>.json" class="mail-detail"><{$item.title}></a></td>
                        <td class="title_4"><{$item.time}></td>
                    </tr>
<{/foreach}>
<{else}>
					<tr>
						<td colspan="4" style="text-align:center">您没有任何邮件</td>
					</tr>
<{/if}>
                </table>
                <div class="t-pre-bottom">
                    <div class="t-btn">
                        <input type="checkbox" class="mail-select" />
                        <input type="button" class="button mail-del" value="删除" />
                        <input type="button" class="button mail-clear" value="全部删除" />
                    </div>
                    <div class="page">
                        <{include file="pagination.tpl" page_name='邮件总数'}>
                    </div>
                 </div>
            </div>
			</form>
    	</div>
<script id="tmpl_mail_detail" type="text/template">
<div class="mail-list">
    <div class="m-op">
        <ul class="m-func"> 
            <li><samp class="ico-pos-reply"></samp><a href="mail/<%=type%>/reply/<%=num%>" class="mail-reply">回复</a></li> 
            <li><samp class="ico-pos-forward"></samp><a href="mail/<%=type%>/ajax_forward/<%=num%>.json" class="mail-forward">转寄</a></li> 
            <li><samp class="ico-pos-del"></samp><a href="mail/<%=type%>/ajax_delete/<%=num%>.json" class="mail-delete">删除</a></li> 
            <li><samp class="ico-pos-edit"></samp><a href="mail/send" class="mail-new">撰写</a></li> 
        </ul>
    </div>
    <div class="m-content">
        <%=content%>
    </div>
    <div class="m-op">
        <ul class="m-func"> 
            <li><samp class="ico-pos-reply"></samp><a href="mail/<%=type%>/reply/<%=num%>" class="mail-reply">回复</a></li> 
            <li><samp class="ico-pos-forward"></samp><a href="mail/<%=type%>/ajax_forward/<%=num%>.json" class="mail-forward">转寄</a></li> 
            <li><samp class="ico-pos-del"></samp><a href="mail/<%=type%>/ajax_delete/<%=num%>.json" class="mail-delete">删除</a></li> 
            <li><samp class="ico-pos-edit"></samp><a href="mail/send" class="mail-new">撰写</a></li>
        </ul>
    </div>
</div>
</script>
<script id="tmpl_forward" type="text/template">
<form id="a_forward" action="<%=action%>" method="post">
	<ul>
	<li><span>收件人:</span><input type="text" class="input-text" name="id"/>
        <select id="a_forward_list">
            <option value="">选择好友</option>
        <%_.each(friends,function(f){%>
            <option value="<%=f%>"><%=f%></option>
        <%});%>
        </select>
    </li>
</form>
</script>
<{if isset($syntax)}><{include file="syntax_high_lighter.tpl"}><{/if}>
