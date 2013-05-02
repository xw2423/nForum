	<{include file="s_nav.tpl" nav_left="新主题"}>
        <div class="b-content corner">
            <form action="<{$base}>/article/<{$bName}>/ajax_post.json" method="post" id="post_form" >
            <ul class="post-list">
            	<li class="post-list-item">
                	<div class="post-m">标题:</div>
                    <input class="input-text post-title" type="text" name="subject" id="post_subject" value="<{$reTitle}>"/>
                </li>
<{if ($subject && $titKey && !empty($titKey))}>
                <li class="post-list-item">
                    <div class="post-m">关键字(可选):</div>
                    <ul class="post-tag">
<{foreach from=$titKey item=item}>
                    <li class="tab-normal"><{$item}></li>
<{/foreach}>
                    </ul>
                    <div class="clearfix"></div>
                </li>
<{/if}>
<{if ($isAtt)}>
            	<li class="upload post-list-item">
                	<div class="post-m">文件上传:</div>
<{include file="attachment/upload.tpl"}>
                </li>
<{/if}>
            	<li class="post-list-item">
                	<div class="post-m">内容:</div>
                    <div id="con_c_area">
                    	<textarea class="post-textarea" name="content" id="post_content"><{$reContent}></textarea>
                    </div>
                </li>
                <li class="post-list-item">
                	<div class="post-m">表情:(<span>单击标签选择表情</span>)</div>
                    <div id="em_img"></div>
                </li>
                <li class="post-list-item">
             		<div class="post-m">选项:</div>
					<div class="post-op">签名档:
						<select name="signature" class="post-select" id="post_sig">
						<{html_options options=$sigOption selected=$sigNow}>
						</select>   
					</div>
                    <div class="post-op"><input type="checkbox" value="0" name="email" id="post_email" /><span>有回复时使用邮件通知您</span></div>
					<{if $anony}>
                    <span class="post-op"><input type="checkbox" checked="true" value="1" name="anony" id="post_anony"/><span>匿名</span></span>
					<{/if}>
					<{if $outgo}>
                    <span class="post-op"><input type="checkbox" checked="true" value="1" name="outgo" id="post_outgo"/><span>转信</span></span>
					<{/if}>
                </li>
           </ul>
		   <input type="hidden" name="id" value="<{$reid|default:0}>" />
           <div class="post-su"><input type="submit" class="button" value="发表帖子" /><input class="button" type="button" value="预览(无附件)" id="post_preview" /></div>
           </form>
		   <form id="f_preview" action="<{$base}>/article/<{$bName}>/ajax_preview.json" method="post"></form>
    	</div>
<{include file="article/preview.tpl"}>
