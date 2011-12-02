	<{include file="s_nav.tpl" nav_left="编辑主题"}>
        <div class="b-content corner">
            <form action="<{$base}>/article/<{$bName}>/ajax_edit/<{$eid}>.json" method="post" id="post_form">
            <ul class="post-list">
            	<li class="post-list-item">
                	<div class="post-m">标题:</div>
                    <input class="input-text post-title" type="text" name="subject" id="post_subject" value="<{$title}>"/>
                </li>
<{if ($isAtt)}>
            	<li class="upload post-list-item">
                	<div class="post-m">文件上传:</div>
                    <div id="upload">
                        <div id="upload_op">
                            <input id="upload_select" type="button" value="选择文件" class="submit" />
                            <input id="upload_upload" type="button" value="上传文件" class="submit" />
                            <span id="upload_info">
                                个数限制:<span class="upload-max-num"><{$maxNum}></span>
                                大小限制:<span class="upload-max-size"><{$maxSize}></span>
                                <span class="upload-msg"></span>
                            </span>
                        </div>
                        <table id="upload_result">
                            <thead>
                                <tr>
                                    <th style="width:40px;">序号</td>
                                    <th>文件名</td>
                                    <th style="width:60px;">大小</td>
                                    <th style="width:60px;">操作</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">共<span style="color:red" id="upload_num_count"></span>个文件，总大小<span style="color:red" id="upload_size_count"></span></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
<{include file="article/upload.tpl"}>
