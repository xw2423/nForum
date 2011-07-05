<{include file="header.tpl"}>
    	<div class="mbar">
        	<ul>
                <li><a href="<{$base}>/user/info">基本资料修改</a></li>
                <li><a href="<{$base}>/user/passwd">昵称密码修改</a></li>
                <li><a href="<{$base}>/user/custom">用户自定义参数</a></li>
                <li class="selected"><a href="<{$base}>/mail">用户信件服务</a></li>
                <li><a href="<{$base}>/friend">好友列表</a></li>
                <li><a href="<{$base}>/fav">收藏版面管理</a></li>
            </ul>					
        </div>
        <div class="c-mbar">
			<ul>
            	<li><a href="<{$base}>/mail/inbox" <{if $type=="inbox"}>class="select"<{/if}>><samp class="ico-pos-dot"></samp>收件箱</a></li>
                <li><a href="<{$base}>/mail/outbox" <{if $type=="outbox"}>class="select"<{/if}>><samp class="ico-pos-dot"></samp>发件箱</a></li>
                <li><a href="<{$base}>/mail/deleted" <{if $type=="deleted"}>class="select"<{/if}>><samp class="ico-pos-dot"></samp>废旧箱</a></li>
                <li><a href="<{$base}>/friend"><samp class="ico-pos-dot"></samp>地址薄</a></li>
                <li><a href="<{$base}>/mail/send"><samp class="ico-pos-dot"></samp>撰写邮件</a></li>
            </ul>
        </div>
        <div class="b-content">
            <div class="mail-list">
                <div class="m-op">
                    <ul class="m-func"> 
                        <li><samp class="ico-pos-reply"></samp><a href="<{$base}>/mail/reply/<{$type}>/<{$num}>">回复</a></li> 
                        <li><samp class="ico-pos-forward"></samp><a href="<{$base}>/mail/forward/<{$type}>/<{$num}>">转寄</a></li> 
                        <li><samp class="ico-pos-del"></samp><a href="<{$base}>/mail/delete/<{$type}>/<{$num}>">删除</a></li> 
                        <li><samp class="ico-pos-edit"></samp><a href="<{$base}>/mail/send">撰写</a></li> 
					</ul>
                </div>
                <div class="mail">
                	<p class="mail-content">
					<{$content}>
                    </p> 
                </div>
                <div class="m-op">
                    <ul class="m-func"> 
                        <li><samp class="ico-pos-reply"></samp><a href="<{$base}>/mail/reply/<{$type}>/<{$num}>">回复</a></li> 
                        <li><samp class="ico-pos-forward"></samp><a href="<{$base}>/mail/forward/<{$type}>/<{$num}>">转寄</a></li> 
                        <li><samp class="ico-pos-del"></samp><a href="<{$base}>/mail/delete/<{$type}>/<{$num}>">删除</a></li> 
                        <li><samp class="ico-pos-edit"></samp><a href="<{$base}>/mail/send">撰写</a></li> 
					</ul>
                </div>
            </div>
    	</div>
<{include file="footer.tpl"}>
