<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="模板发文"}>
	<div class="b-content corner">
	<div class="tmpl">
		<form action="" method="post" id="f_tmpl">
		<table class="m-table que">
		<tr>
			<th class="col5">模版<{$num}></th>
			<td class="col6"><{$tmplTitle}></th>
		</tr>
		<tr>
			<th>标题</th>
			<td><{$title|default:"<input type=\"text\" name=\"q[0]\" class=\"input-text\" style=\"width:90%\" />"}></th>
		</tr>
<{foreach from=$info item=item key=k}>
		<tr>
			<th>问题<{$k+1}>:<{$item.text}>(长度:<{$item.len}>)</th>
			<td><textarea name="q[<{$k+1}>]" style="width:90%"></textarea></th>
			</tr>
<{/foreach}>
			<tr class="tmpl-op">
				<td colspan="2"><input type="submit" class="button" value="提交发文" onclick="$('#h_pre').val('0');$('#f_tmpl').attr('target','_self')"/>&nbsp;<input type="submit" class="button" value="预览" onclick="$('#h_pre').val('1');$('#f_tmpl').attr('target','_blank')"/>&nbsp;<input type="button" class="button" value="返回"/><input type="hidden" value="0" name="pre" id="h_pre"/></td>
			</tr>
			</table>
			</form>
		</div>
    	</div>
<{include file="footer.tpl"}>
