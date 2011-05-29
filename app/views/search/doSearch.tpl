<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="搜索结果"}>
        <div class="b-content">
			<table class="board-title" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="title_8">序号</th>
                    <th class="title_9 middle">主题</th>
                    <th class="title_10">发帖时间&emsp;&ensp;|&ensp;作者</th>
                    <th class="title_11 middle">回复</th>
                    <th class="title_12">最新回复&emsp;&ensp;|&ensp;作者</th>
            	</tr>
            </table>
            <table class="board-list tiz" cellpadding="0" cellspacing="0">
<{if ($info)}>
<{foreach from=$info item=item key=k}>
				<tr>
					<td class="title_8"><{$k+1}>.</td>
					<td class="title_9">
						<a href="<{$base}>/article/<{$bName}>/<{$item.gid}>"><{$item.title}></a>
		<{if $item.page>7}>
		<span class="threads-tab">[<{section name=temp loop=7 start=2}><a href="<{$base}>/article/<{$bName}>/<{$item.gid}>?p=<{$smarty.section.temp.index}>"><{$smarty.section.temp.index}></a><{/section}>..<a href="<{$base}>/article/<{$bName}>/<{$item.gid}>?p=<{$item.page}>"><{$item.page}></a>]</span>
		<{elseif $item.page>1}>
		<span class="threads-tab">[<{section name=temp loop=$item.page+1 start=2}><a href="<{$base}>/article/<{$bName}>/<{$item.gid}>?p=<{$smarty.section.temp.index}>"><{$smarty.section.temp.index}></a><{/section}>]</span>
		<{/if}>
					</td>
					<td class="title_10"><{$item.postTime}>&ensp;|&ensp;<a href="<{$base}>/user/query/<{$item.poster}>"><{$item.poster}></a></td>
					<td class="title_11 middle"><{$item.num}></td>
					<td class="title_12"><{$item.replyTime}>&ensp;|&ensp;<a href="<{$base}>/user/query/<{$item.poster}>"><{$item.last}></a></td>
				</tr>
<{/foreach}>
			</table>
<{else}>
				<tr>
					<td colspan="5" style="text-align:center">没有搜索到任何主题</td>
				</tr>
			</table>
<{/if}>
    	</div>
    <div class="t-pre-bottom">
        <div class="page">
            <ul class="pagination">
				<li class="page-pre">主题数:<i><{$totalNum}></i>&emsp;分页:</li>
				<li>
                  <ol title="分页列表" class="page-main">
					<{$pageBar}>
                  </ol>
				</li>
				<li class="page-suf"></li>	
            </ul>
        </div>
    </div>  
<{include file="footer.tpl"}>
