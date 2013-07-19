<{include file="../plugins/mobile/views/header.tpl"}>
<form action="<{$mbase}>/article/<{$bName}>/<{if isset($edit)}>edit<{else}>post<{/if}><{if $reid!=0}>/<{$reid}><{/if}>" method="post">
<ul class="sec list">
<li>标题:<br /><input type="text" name="subject" value="<{$title}>" style="width:100%" /></li>
<li>内容:<br /><textarea name="content" style="width:100%" rows="8"><{$content}></textarea></li>
<{if $email}>
<li><input type="checkbox" name="email" />回文转寄
<{/if}>
<{if $anony}>
&nbsp;<input type="checkbox" name="anony" checked="true"/>匿名
<{/if}>
<{if $outgo}>
&nbsp;<input type="checkbox" name="outgo" checked="true"/>转信
<{/if}>
</li>
<li><input type="submit" class="btn" value="提交"/>&nbsp;<input type="button" class="btn" value="返回" onclick="javascript:history.go(-1)" /></li>
</ul>
<{if $single}>
<input type="hidden" name="s" value="1" />
<{/if}>
</form>
<{include file="../plugins/mobile/views/footer.tpl"}>
