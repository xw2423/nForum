<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="版内投票"}>
	<div class="b-content corner">
	<div style="padding:10px;">
		<form action="" method="post">
		<table class="m-table" style="text-align:center">
		<tr>
			<th style="width:180px">标题</th>
			<td style="width:auto"><{$title|default:"&nbsp;"}></td>
		</tr>
		<tr>
			<th>描述</th>
			<td><{$desc|default:"&nbsp;"}></td>
		</tr>
		<tr>
			<th>类型</th>
			<td><{$type|default:"&nbsp;"}></td>
		</tr>
		<tr>
			<th>开始时间</th>
			<td><{$start|default:"&nbsp;"}></td>
		</tr>
		<tr>
			<th>持续时间</th>
			<td><{$day|default:"&nbsp;"}></td>
		</tr>
<{if $type!='问答'}> 
		<tr>
			<th><{$type}>
<{if $type=='复选'}> 
                <br /><span style="color:red">(最多投<{$limit}>票)</span>
<{elseif $type=='数字'}> 
                <br /><span style="color:red">(最大不超过<{$limit}>)</span>
<{/if}> 
            </th>
			<td>
<{if $type=='数字'}> 
                <input type="text" name="v" class="input-text" style="width:90%;height:40px" value="<{$val}>"/>
<{else}>
            <table style="width:100%">
<{if $type=='复选'}> 
    <{foreach from=$val item=item key=k}>
            <tr>
                <td style="text-align:right;padding-right:15px"><{$item[0]}></td>
                <td style="text-align:left;padding-left:15px;width:40%"><input type="checkbox" name="v[<{$k}>]"<{if $item[1]}>  checked="checked"<{/if}>"/></td>
            </tr>
    <{/foreach}>
<{else}> 
    <{foreach from=$val item=item key=k}>
            <tr>
                <td style="text-align:right;padding-right:15px"><{$item[0]}></td>
                <td style="text-align:left;padding-left:15px;width:40%"><input type="radio" name="v" value="<{$k}>"<{if $item[1]}> checked="checked"<{/if}>/></td>
            </tr>
    <{/foreach}>
<{/if}> 
            </table>
<{/if}> 
            </td>
		</tr>
<{/if}> 
		<tr>
			<th>您想说的话:<br />(限3行)</th>
			<td><textarea name="msg" style="width:90%" rows="3"><{$msg}></textarea></td>
        </tr>
        <tr class="tmpl-op">
				<td colspan="2" style="padding:4px 0"><input type="submit" class="button" value="提交" />&nbsp;&nbsp;<input type="button" class="button" value="返回" onclick="javascript:history.go(-1)" /></td>
			</tr>
			</table>
			</form>
		</div>
    	</div>
<{include file="footer.tpl"}>
