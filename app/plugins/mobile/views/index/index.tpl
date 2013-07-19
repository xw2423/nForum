<{include file="../plugins/mobile/views/header.tpl"}>
<form action="<{$mbase}>/user/login" method="post">
<{if $islogin}>
<ul class="sec slist">
<li class="f">当前用户:<{$id}></li>
<li>等级:<{$level}></li>
<li>发帖数:<{$postNum}></li>
<{else}>
<ul class="sec">
<li>
用户名:<br/> 
<input type="text" name="id" />
</li>
<li>
密码:<br/> 
<input type="password" name="passwd" />
</li>
<li>
<input type="checkbox" name="save" />记住我<br/>
<input type="submit" class="btn" value="登录" /> 
</li>
<{/if}>
</ul>
</form>
<ul class="slist sec">
<li class="f">十大热门话题</li>
<{foreach from=$top item=item key=k}>
<li><{$k+1}>|<a href="<{$mbase}><{$item.url}>"><{$item.text}></a></li>
<{/foreach}>
</ul>
<{include file="../plugins/mobile/views/footer.tpl"}>
