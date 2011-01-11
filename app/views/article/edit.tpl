<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="编辑主题"}>
        <div class="b-content">
            <form action="<{$base}>/article/<{$bName}>/edit/<{$eid}>" method="post">
            <ul class="post-list">
            	<li>
                	<div class="post-m">标题:</div>
                    <input class="input-text post-title" type="text" name="subject" id="subject" value="<{$title}>"/>
                </li>
<{if ($isAtt)}>
            	<li class="upload">
                	<div class="post-m">文件上传:</div>
					<iframe src="<{$base}>/att/upload/<{$bName}>/<{$eid}>" width="100%" frameborder="0" id="upload"></iframe>
                </li>
<{/if}>
            	<li>
                	<div class="post-m">内容:</div>
                    <div id="con_c_area">
                    	<textarea class="c-textarea" name="content" id="ta_content"><{$content}></textarea>
                    </div>
                </li>
                <li>
                	<div class="post-m">表情:(<span>单击标签选择表情</span>)</div>
                    <div id="em_img"></div>
                </li>
           </ul>
           <div class="post-su"><input type="submit" class="button b-submit" value="提交" /><input class="button b-submit" type="button" value="预览(无附件)" id="b_preview"/></div>
           </form>
		   <form id="f_preview" action="/article/<{$bName}>/preview" method="post" target="_blank">
		   		<input type="hidden" name="title" id="pre_t"/><input type="hidden" name="content" id="pre_c"/>
		   </form>
    	</div>
<{include file="footer.tpl"}>
