<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="模板发文"}>
        <div class="b-content corner">
		<div class="tmpl">
			<table class="m-table">
			<tr>
				<th class="col1">序号</th>
				<th class="col2">标题 </th>
				<th class="col3">问题个数</th>
				<th class="col4">操作</th>
			</tr>
<{if empty($info)}>
			<tr>
				<td colspan="4">该版面没有任何模版</td>
			</tr>
<{else}>
<{foreach from=$info item=item key=k}>
			<tr>
				<td><{$k+1}></td>
				<td><{$item.name}></td>
				<td><{$item.num}></td>
				<td><a href="<{$base}>/article/<{$bName}>/tmpl/<{$k+1}>">我要发文</a></td>
			</tr>
<{/foreach}>
<{/if}>
			</table>
		</div>
    	</div>
<{include file="footer.tpl"}>
