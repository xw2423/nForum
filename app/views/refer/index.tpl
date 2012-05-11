        <div class="mbar">
            <ul>
                <li><a href="<{$base}>/user/info">基本资料修改</a></li>
                <li><a href="<{$base}>/user/passwd">昵称密码修改</a></li>
                <li><a href="<{$base}>/user/custom">用户自定义参数</a></li>
                <li><a href="<{$base}>/mail">用户信件</a></li>
                <li class="selected"><a href="<{$base}>/refer">文章提醒</a></li>
                <li><a href="<{$base}>/friend">好友列表</a></li>
                <li><a href="<{$base}>/fav">收藏版面</a></li>
            </ul>
        </div>
        <div class="c-mbar">
            <ul>
                <li><a href="<{$base}>/refer/at" <{if $type=="at"}>class="select"<{/if}>><samp class="ico-pos-dot"></samp>@我的文章</a></li>
                <li><a href="<{$base}>/refer/reply" <{if $type=="reply"}>class="select"<{/if}>><samp class="ico-pos-dot"></samp>回复我的文章</a></li>
            </ul>
        </div>
        <div class="b-content">
			<form id="refer_read" action="<{$base}>/refer/<{$type}>/ajax_read.json" method='post'>
			<input type="hidden" name="index" value="all" />
            </form>
			<form id="refer_clear" action="<{$base}>/refer/<{$type}>/ajax_delete.json" method="post">
			<input type="hidden" name="all" value="all" />
			</form>
			<form id="refer_form" action="<{$base}>/refer/<{$type}>/ajax_delete.json" method="post">
            <div class="mail-list">
                <div class="t-pre">
                    <div class="t-btn">
						<input type="checkbox" class="mail-select" />
						<input type="button" class="button refer-del" value="删除" />
						<input type="button" class="button refer-clear" value="全部删除" />
						<input type="button" class="button refer-read" value="全部已读" />
                    </div>
                    <div class="page">
                        <{include file="pagination.tpl" page_name='文章总数'}>
                    </div>
                </div>
                <table class="m-table">
<{if isset($info)}>
<{foreach from=$info item=item}>
                	<tr <{if !($item.read)}>class="no-read"<{/if}>>
                    	<td class="title_1"><input type="checkbox" name="m_<{$item.index}>" class="mail-item"/></td>
                        <td class="title_2"><a href="<{$base}>/user/query/<{$item.user}>"><{$item.user}></a></td>
                        <td class="title_3"><a href="<{$base}>/article/<{$item.board}>/ajax_single/<{$item.id}>.json" class="m-single" _index="<{$item.index}>"><{$item.title}></a></td>
                        <td class="title_4"><{$item.time}></td>
                    </tr>
<{/foreach}>
<{else}>
                    <tr>
                        <td colspan="4" style="text-align:center">不存在任何文章</td>
                    </tr>
<{/if}>
                </table>
                <div class="t-pre-bottom">
                    <div class="t-btn">
                        <input type="checkbox" class="mail-select" />
                        <input type="button" class="button refer-del" value="删除" />
                        <input type="button" class="button refer-clear" value="全部删除" />
						<input type="button" class="button refer-read" value="全部已读" />
                    </div>
                    <div class="page">
                        <{include file="pagination.tpl" page_name='文章总数'}>
                    </div>
                 </div>
            </div>
            </form>
        </div>
<{include file="article/single.tpl"}>
