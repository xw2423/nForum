<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="������"}>
        <div class="b-content corner">
            <form action="<{$base}>/article/<{$bName}>/post" method="post" id="f_article" >
            <ul class="post-list">
            	<li>
                	<div class="post-m">����:</div>
                    <input class="input-text post-title" type="text" name="subject" id="subject" value="<{$reTitle}>"/>
                </li>
<{if ($isAtt)}>
            	<li class="upload">
                	<div class="post-m">�ļ��ϴ�:</div>
					<iframe src="<{$base}>/att/upload/<{$bName}>" width="100%" frameborder="0" id="upload"></iframe>
                </li>
<{/if}>
            	<li>
                	<div class="post-m">����:</div>
                    <div id="con_c_area">
                    	<textarea class="c-textarea" name="content" id="ta_content"><{$reContent}></textarea>
                    </div>
                </li>
                <li>
                	<div class="post-m">����:(<span>������ǩѡ�����</span>)</div>
                    <div id="em_img"></div>
                </li>
                <li>
             		<div class="post-m">ѡ��:</div>
					<div class="post-op">ǩ����:
						<select name="signature" class="post-select">
						<{html_options options=$sigOption selected=$sigNow}>
						</select>   
					</div>
                    <div class="post-op"><input type="checkbox" value="0" name="email"/><span>�лظ�ʱʹ���ʼ�֪ͨ��</span></div>
					<{if $anony}>
                    <span class="post-op"><input type="checkbox" checked="true" value="1" name="anony"/><span>����</span></span>
					<{/if}>
                </li>
           </ul>
		   <input type="hidden" name="reid" value="<{$reID}>" />
           <div class="post-su"><input type="submit" class="button" value="��������" /><input class="button" type="button" value="Ԥ��(�޸���)" id="b_preview"/></div>
           </form>
		   <form id="f_preview" action="<{$base}>/article/<{$bName}>/preview" method="post" target="_blank">
		   		<input type="hidden" name="title" id="pre_t"/><input type="hidden" name="content" id="pre_c"/>
		   </form>
    	</div>
<{include file="footer.tpl"}>
