<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="精华区列表"}>
        <div class="b-content corner">
			<table class="board-title" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="title_13">序号</th>
                    <th class="title_14">类型</th>
                    <th class="title_15 middle">标题</th>
                    <th class="title_16">整理</th>
                    <th class="title_17">编辑日期</th>
            	</tr>
            </table>
            <table class="board-list tiz" cellpadding="0" cellspacing="0">
<{if isset($parent)}>
				<tr>
					<td class="title_13">0.</td>
					<td class="title_14"><samp class="tag ico-pos-article-top"></samp></td>
					<td class="title_15">
                        <a href="<{$base}>/elite/path<{if !empty($parent)}>?v=<{$parent}><{/if}>">上级目录</a>
					</td>
					<td class="title_16">&nbsp;</td>
					<td class="title_17">&nbsp;</td>
				</tr>
<{/if}>
<{if ($info)}>
<{foreach from=$info item=item key=k}>
				<tr>
					<td class="title_13"><{$k+$start}>.</td>
					<td class="title_14"><samp class="tag <{if $item.dir == "file"}>ico-pos-article-normal<{else}>ico-pos-article-dir<{/if}>"></samp></td>
					<td class="title_15">
						<a href="<{$base}>/elite/<{$item.dir}>?v=<{$item.path}>"><{$item.title}></a>
					</td>
					<td class="title_16"><{if $item.bm != ""}><a href="<{$base}>/user/query/<{$item.bm}>"><{$item.bm}></a><{else}>&nbsp;<{/if}></td>
					<td class="title_17"><{$item.time|default:"&nbsp;"}></td>
				</tr>
<{/foreach}>
			</table>
<{else}>
				<tr>
					<td colspan="5" style="text-align:center">该目录为空</td>
				</tr>
			</table>
<{/if}>
    	</div>
<{include file="footer.tpl"}>
