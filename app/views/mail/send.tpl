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
            	<li><a href="<{$base}>/mail/inbox"><samp class="ico-pos-dot"></samp>收件箱</a></li>
                <li><a href="<{$base}>/mail/outbox" ><samp class="ico-pos-dot"></samp>发件箱</a></li>
                <li><a href="<{$base}>/mail/deleted" ><samp class="ico-pos-dot"></samp>废旧箱</a></li>
                <li><a href="<{$base}>/friend"><samp class="ico-pos-dot"></samp>地址薄</a></li>
                <li><a href="<{$base}>/mail/send" class="select"><samp class="ico-pos-dot"></samp>撰写邮件</a></li>
            </ul>
        </div>
        <div class="b-content">
			<form id="post_form" method="post" action="<{$base}>/mail/<{$type|default:'NULL'}>/ajax_send.json">
                <ul class="post-list" style="border-top:1px solid #c9d7f1;">
                    <li class="post-list-item">
						<div class="post-m">收件人：</div>
<{if !isset($rid)}>
						<input class="input-text post-title" type="text" name="id" id="post_id"style="width:300px" value=""/>  
						<select class="post-select" onchange="$('#post_id').val($(this).val())">
							<option value="">选择好友</option>
							<{html_options options=$friends}>
						</select>
<{else}>
						<input class="input-text post-title" type="text" id="id" name="id" style="width:300px" value="<{$rid}>" readonly="readonly"/>  
<{/if}>
					</li>
                    <li class="post-list-item">
						<div class="post-m">标题:</div>
						<input class="input-text post-title" type="text" name="title" id="post_subject" value="<{$title|default:""}>"/>
					</li>
                    <li class="post-list-item">
						<div class="post-m">内容：</div>
                        <div id="con_c_area">
                            <textarea id="post_content" class="post-textarea" name="content"><{$content|default:""}></textarea>
                    </div>
					</li>
                    <li class="post-list-item">
                        <div class="post-m">表情:(<span>单击标签选择表情</span>)</div>
                        <div id="em_img"></div>
                    </li>
                    <li class="post-list-item">
						<div class="post-m">选项:</div>
						<div class="post-op">
						签名档:<select class="post-select" name="signature">
						<{html_options options=$sigOption selected=$sigNow}>
						</select>   
						</div>
                        <div class="post-op"><input type="checkbox" name="backup"<{if $bak}> checked="checked"<{/if}>/>备份到发件箱中</div>
                    </li>
                </ul>
                <div class="post-su"><input type="submit" class="button" value="发送消息" /><input class="button" type="button" value="预览(无附件)" id="post_preview" /></div>
                <input type="hidden" name="num" value="<{$num|default:''}>" />
			</form>
		   <form id="f_preview" action="<{$base}>/mail/ajax_preview.json" method="post"></form>
    	</div>
<{include file="article/preview.tpl"}>
