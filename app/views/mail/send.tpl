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
            	<li><a href="<{$base}>/mail/inbox"><samp class="ico-pos-dot"></samp>收件箱</a></li>
                <li><a href="<{$base}>/mail/outbox" ><samp class="ico-pos-dot"></samp>发件箱</a></li>
                <li><a href="<{$base}>/mail/deleted" ><samp class="ico-pos-dot"></samp>废旧箱</a></li>
                <li><a href="<{$base}>/friend"><samp class="ico-pos-dot"></samp>地址薄</a></li>
                <li><a href="<{$base}>/mail/send" class="select"><samp class="ico-pos-dot"></samp>撰写邮件</a></li>
            </ul>
        </div>
        <div class="b-content">
			<form id="f_mail" method="post" action="<{$base}>/mail/send">
                <ul class="post-list" style="border-top:1px solid #c9d7f1;">
                    <li>
						<div class="post-m">收件人：</div>
<{if !isset($rid)}>
						<input class="input-text post-title" type="text" name="id" id="id" style="width:300px" value=""/>  
						<select class="post-select" onchange="$('#id').val($(this).val())">
							<option>选择</option>
							<{html_options options=$friends}>
						</select>
<{else}>
						<input class="input-text post-title" type="text" id="id" style="width:300px" value="<{$rid}>" disabled="true"/>  
						<input type="hidden" name="id" value="<{$rid}>" />
<{/if}>
					</li>
                    <li>
						<div class="post-m">标题:</div>
						<input class="input-text post-title" type="text" name="title" value="<{$title|default:""}>"/>
					</li>
                    <li>
						<div class="post-m">内容：</div>
						<textarea id="a_content" class="c-textarea" name="content"><{$content|default:""}></textarea>
					</li>
                    <li>
						<div class="post-m">选项:&nbsp;</div>
						<div class="post-op">
						签名档:<select class="post-select" name="signature">
						<{html_options options=$sigOption selected=$sigNow}>
						</select>   
						</div>
                        <div class="post-op"><input type="checkbox" name="backup" checked="true"/>备份到发件箱中</div>
                    </li>
                </ul>
                <div class="post-su"><input type="submit" class="button b-submit" value="发送消息" /><input class="button b-submit" type="submit" value="重写" /></div>
			</form>
    	</div>
<{include file="footer.tpl"}>
