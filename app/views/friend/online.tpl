<{include file="header.tpl"}>
    	<div class="mbar corner">
        	<ul>
                <li><a href="<{$base}>/user/info">基本资料修改</a></li>
                <li><a href="<{$base}>/user/passwd">昵称密码修改</a></li>
                <li><a href="<{$base}>/user/custom">用户自定义参数</a></li>
                <li><a href="<{$base}>/mail">用户信件服务</a></li>
                <li class="selected"><a href="<{$base}>/friend">好友列表</a></li>
                <li><a href="<{$base}>/fav">收藏版面管理</a></li>
            </ul>					
        </div>
        <div class="c-mbar">
			<ul>
            	<li><a href="<{$base}>/friend"><samp class="ico-pos-dot"></samp>我的好友</a></li>
                <li><a href="<{$base}>/friend/online" class="select"><samp class="ico-pos-dot"></samp>在线好友</a></li>
                <li><a href="<{$base}>/online"><samp class="ico-pos-dot"></samp>在线用户</a></li>
            </ul>
        </div>
        <div class="b-content">
            <div class="mail-list">
                <table class="m-table">
                	<tr class="title">
						<td class="title_7">序号</td>
						<td class="title_2">ID</td>
                        <td class="title_3">状态</td>
                        <td class="title_5">登陆IP</td>
                        <td class="title_6">发呆</td>
                        <td class="title_6">操作</td>
                    </tr>
<{if isset($friends)}>
<{foreach from=$friends item=item key=k}>
                	<tr>
						<td class="title_7"><{$k+1}></td>
						<td class="title_2"><a href="<{$base}>/user/query/<{$item.fid}>"><{$item.fid}></a></td>
                        <td class="title_3"><{$item.mode}></td>
                        <td class="title_5"><{$item.from}></td>
                        <td class="title_6"><{$item.idle}></td>
                        <td class="title_6"><a href="<{$base}>/mail/send?id=<{$item.fid}>">发信问候</a></td>
                    </tr>
<{/foreach}>
<{else}>
					<tr>
						<td colspan="6" style="text-align:center">您没有任何好友</td>
					</tr>
<{/if}>
                </table>
            </div>
    	</div>
<{include file="footer.tpl"}>
