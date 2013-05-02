	<{include file="s_nav.tpl" nav_left="编辑主题"}>
        <div class="b-content corner">
            <form action="<{$base}>/article/<{$bName}>/ajax_edit/<{$eid}>.json" method="post" id="post_form">
            <ul class="post-list">
            	<li class="post-list-item">
                	<div class="post-m">标题:</div>
                    <input class="input-text post-title" type="text" name="subject" id="post_subject" value="<{$title}>"/>
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
                    	<textarea class="post-textarea" name="content" id="post_content"><{$content}></textarea>
                    </div>
                </li>
                <li class="post-list-item">
                	<div class="post-m">表情:(<span>单击标签选择表情</span>)</div>
                    <div id="em_img"></div>
                </li>
           </ul>
           <div class="post-su"><input type="submit" class="button b-submit" value="提交" /><input class="button b-submit" type="button" value="预览(无附件)" id="post_preview"/></div>
           </form>
		   <form id="f_preview" action="<{$base}>/article/<{$bName}>/ajax_preview.json" method="post"></form>
    	</div>
<{include file="article/preview.tpl"}>
