<{include file="../plugins/mobile/views/header.tpl"}>
<form action="<{$mbase}>/article/<{$bName}>/forward/<{$gid}>" method="post">
<ul class="sec list">
<li>用户名:<input id="target" type="text" name="target" value="" style="width:100px" /></li>
<li>我的好友:
<select onclick="document.getElementById('target').value=this.value"> 
    <option value="">选择好友</option>
<{foreach from=$friends item=item}>
    <option value="<{$item}>"><{$item}></option>
<{/foreach}>
</select>
</li>
<li>合集转寄:<input type="checkbox" name="threads" /><br />
合集无引文:<input type="checkbox" name="noref" />
</li>
<li>不含附件:<input type="checkbox" name="noatt" /></li>
<li><input type="submit" class="btn" value="转寄"/>&nbsp;<input type="button" class="btn" value="返回" onclick="javascript:history.go(-1)" /></li>
</ul>
<{if $single}>
<input type="hidden" name="s" value="1" />
<{/if}>
</form>
<{include file="../plugins/mobile/views/footer.tpl"}>
