	<{include file="s_nav.tpl" nav_left="模板发文"}>
	<div class="b-content corner">
	<div class="tmpl">
		<form action="<{$base}>/article/<{$bName}>/ajax_tmpl.json" method="post" id="f_tmpl">
		<table class="m-table que">
            <tr>
                <th class="col5">模版<{$num}></th>
                <td class="col6"><{$tmplTitle}></td>
            </tr>
            <tr>
                <th>标题</th>
                <td><{$title|default:"<input type=\"text\" name=\"q[0]\" class=\"input-text\" style=\"width:90%\" id=\"tmpl_subject\" />"}></td>
            </tr>
<{foreach from=$info item=item key=k}>
            <tr>
                <th>问题<{$k+1}>:<{$item.text}>(长度:<{$item.len}>)</th>
                <td><textarea name="q[<{$k+1}>]" style="width:90%"></textarea></td>
			</tr>
<{/foreach}>
			<tr class="tmpl-op">
				<td colspan="2">
                <input type="submit" class="button" value="提交发文" />
                <input type="button" class="button" value="预览" id="que_preview"/>
                <input type="button" class="button" value="返回" onclick="history.go(-1)"/>
                <input type="hidden" value="0" name="pre"/>
                </td>
			</tr>
			</table>
            <input type="hidden" name="id" value="<{$reid|default:0}>" />
            <input type="hidden" name="tmplid" value="<{$tmplId}>" />
        </form>
    </div>
    </div>
<{include file="article/preview.tpl"}>
