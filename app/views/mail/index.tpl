<{include file="header.tpl"}>
    	<div class="mbar">
        	<ul>
                <li><a href="<{$base}>/user/info">基本资料修改</a></li>
                <li><a href="<{$base}>/user/passwd">昵称密码修改</a></li>
                <li><a href="<{$base}>/user/custom">用户自定义参数</a></li>
                <li class="selected"><a href="<{$base}>/mail">用户信件服务</a></li>
                <li><a href="<{$base}>/friend">好友列表</a></li>
                <li><a href="<{$base}>/fav">收藏版面管理</a></li>
            </ul>					
        </div>
        <div class="c-mbar">
			<ul>
            	<li><a href="<{$base}>/mail/inbox" <{if $type=="inbox"}>class="select"<{/if}>><samp class="ico-pos-dot"></samp>收件箱</a></li>
                <li><a href="<{$base}>/mail/outbox" <{if $type=="outbox"}>class="select"<{/if}>><samp class="ico-pos-dot"></samp>发件箱</a></li>
                <li><a href="<{$base}>/mail/deleted" <{if $type=="deleted"}>class="select"<{/if}>><samp class="ico-pos-dot"></samp>废旧箱</a></li>
                <li><a href="<{$base}>/friend"><samp class="ico-pos-dot"></samp>地址薄</a></li>
                <li><a href="<{$base}>/mail/send"><samp class="ico-pos-dot"></samp>撰写邮件</a></li>
            </ul>
		</div>	
        <div class="b-content">
			<form id="mail_clear" action="<{$base}>/mail/delete/<{$type}>" method="post">
			<input type="hidden" name="all" value="all" />
			</form>
			<form id="mail_form" action="<{$base}>/mail/delete/<{$type}>" method="post">
            <div class="mail-list">
                <div class="t-pre">
                    <div class="t-btn">
						<input type="checkbox" class="b-select" />选择所有
						<input type="button" class="button b-del" value="删除" />
						<input type="button" class="button b-clear" value="全部删除" />
                    </div>
                    <div class="page">
                        <ul class="pagination" title="分页列表">
                          <li class="page-pre">邮件总数:<i><{$totalNum}></i>&emsp;分页:</li>
						  <li>
							  <ol title="分页列表" class="page-main">
								<{$pageBar}>
							  </ol>
						  </li>
						  <li class="page-suf"></li>	
                        </ul>
                    </div>
                </div>
                <table class="m-table">
<{if isset($info)}>
<{foreach from=$info item=item}>
                	<tr <{if !($item.read)}>class="no_read"<{/if}>>
                    	<td class="title_1"><input type="checkbox" name="m_<{$item.num}>" class="b-mail"/></td>
						<td class="title_2"><a href="<{$base}>/user/query/<{$item.sender}>"><{$item.sender}></a></td>
                        <td class="title_3"><a href="<{$base}>/mail/<{$type}>/<{$item.num}>"><{$item.title}></a></td>
                        <td class="title_4"><{$item.time}></td>
                    </tr>
<{/foreach}>
<{else}>
					<tr>
						<td colspan="4" style="text-align:center">您没有任何邮件</td>
					</tr>
<{/if}>
                </table>
            <div class="t-pre-bottom">
            	<div class="t-btn">
					<input type="checkbox" class="b-select" />选择所有
					<input type="button" class="button b-del" value="删除" />
					<input type="button" class="button b-clear" value="全部删除" />
				</div>
				<div class="page">
					<ul class="pagination" title="分页列表">
					  <li class="page-pre">邮件总数:<i><{$totalNum}></i>&emsp;分页:</li>
					  <li>
						  <ol title="分页列表" class="page-main">
							<{$pageBar}>
						  </ol>
					  </li>
					  <li class="page-suf"></li>	
					</ul>
				</div>
             </div>
            </div>
			</form>
    	</div>
<{include file="footer.tpl"}>
