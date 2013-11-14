<{include file="header.tpl"}>
<form action="" method="post">
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
<li><input type="submit" class="btn" value="转寄"/>&nbsp;<input type="button" class="btn" value="返回" onclick="javascript:history.go(-1)" /></li>
</ul>
</form>
<{include file="footer.tpl"}>
