<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="�༭����"}>
        <div class="b-content">
            <form action="<{$base}>/article/<{$bName}>/edit/<{$eid}>" method="post">
            <ul class="post-list">
            	<li>
                	<div class="post-m">����:</div>
                    <input class="input-text post-title" type="text" name="subject" id="subject" value="<{$title}>"/>
                </li>
<{if ($isAtt)}>
            	<li class="upload">
                	<div class="post-m">�ļ��ϴ�:</div>
					<iframe src="<{$base}>/att/upload/<{$bName}>/<{$eid}>" width="100%" frameborder="0" id="upload"></iframe>
                </li>
<{/if}>
            	<li>
                	<div class="post-m">����:</div>
                    <div id="con_c_area">
                    	<textarea class="c-textarea" name="content" id="ta_content"><{$content}></textarea>
                    </div>
                </li>
                <li>
                	<div class="post-m">����:(<span>������ǩѡ�����</span>)</div>
                    <div id="em_img"></div>
                </li>
           </ul>
           <div class="post-su"><input type="submit" class="button b-submit" value="�ύ" /><input class="button b-submit" type="button" value="Ԥ��(�޸���)" id="b_preview"/></div>
           </form>
		   <form id="f_preview" action="<{$base}>/article/<{$bName}>/preview" method="post" target="_blank">
		   		<input type="hidden" name="title" id="pre_t"/><input type="hidden" name="content" id="pre_c"/>
		   </form>
    	</div>
<{include file="footer.tpl"}>
