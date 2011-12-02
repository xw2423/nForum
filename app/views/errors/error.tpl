	<{include file="s_nav.tpl" nav_left="论坛错误信息"}>
        <div class="b-content corner">
        	<div class="error">
            	<h5>产生错误的可能原因：</h5>
                	<ul>
						<li><samp class="ico-pos-dot"></samp><{$ajax_msg}></li>
                    </ul>
            </div>
			<div class="error-op">
			<input class="button error-su" type="button" onclick="javascript:history.go(-1)" value="返回上一页" />
			</div>
        </div>   
