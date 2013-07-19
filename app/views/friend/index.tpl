    	<div class="mbar corner">
        	<ul>
                <li><a href="<{$base}>/user/info">基本资料修改</a></li>
                <li><a href="<{$base}>/user/passwd">昵称密码修改</a></li>
                <li><a href="<{$base}>/user/custom">用户自定义参数</a></li>
                <li><a href="<{$base}>/mail">用户信件</a></li>
                <li><a href="<{$base}>/refer">文章提醒</a></li>
                <li class="selected"><a href="<{$base}>/friend">好友列表</a></li>
                <li><a href="<{$base}>/fav">收藏版面</a></li>
            </ul>					
        </div>
        <div class="c-mbar">
			<ul>
            	<li><a href="<{$base}>/friend" class="select"><samp class="ico-pos-dot"></samp>我的好友</a></li>
                <li><a href="<{$base}>/friend/online"><samp class="ico-pos-dot"></samp>在线好友</a></li>
                <li><a href="<{$base}>/online"><samp class="ico-pos-dot"></samp>在线用户</a></li>
            </ul>
        </div>
        <div class="b-content">
            <div class="friend-list">
				<form method="get" action="<{$base}>/friend/ajax_add.json" id="friend_add">
					添加好友:<input type="text" class="input-text" name="id" value="" />
					<input type="submit" class="button" value="添加" />
				</form>
                <div class="t-pre">
                    <div class="t-btn">
						<input type="checkbox" class="friend-select" />
						<input type="button" class="button friend-del" value="删除" />
                    </div>
                    <div class="page">
                        <{include file="pagination.tpl" page_name='好友总数'}>
                    </div>
                </div>
			<form id="friend_form" action="<{$base}>/friend/ajax_delete.json" method="post">
                <table class="m-table">
<{if isset($friends)}>
<{foreach from=$friends item=item}>
                	<tr>
                    	<td class="title_1"><input type="checkbox" name="f_<{$item.fid}>" class="friend-item"/></td>
						<td class="title_2"><a href="<{$base}>/user/query/<{$item.fid}>"><{$item.fid}></a></td>
                        <td class="title_3"><{$item.desc|default:"&nbsp;"}></td>
                        <td class="title_6"><a href="<{$base}>/mail/send?id=<{$item.fid}>">发信问候</a></td>
                    </tr>
<{/foreach}>
<{else}>
					<tr>
						<td colspan="4" style="text-align:center">您没有任何好友</td>
					</tr>
<{/if}>
                </table>
			</form>
            <div class="t-pre-bottom">
            	<div class="t-btn">
						<input type="checkbox" class="friend-select" />
						<input type="button" class="button friend-del" value="删除" />
				</div>
				<div class="page">
                    <{include file="pagination.tpl" page_name='好友总数'}>
				</div>
             </div>
            </div>
    	</div>
