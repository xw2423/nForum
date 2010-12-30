<{include file="../plugins/mobile/views/header.tpl"}>
<div class="sec nav">
	<a href="<{$mbase}>/mail/send">写信</a>
</div>
<ul class="list sec">
<{if $info}>
<{foreach from=$info item=item key=k}>
<li<{cycle values=', class="hla"'}>>
		<{$k+1}>.<a href="<{$mbase}>/mail/<{$item.num}>"<{if !$item.read}> class="top"<{/if}>><{$item.title}></a><br />
		<a href="<{$mbase}>/user/query/<{$item.sender}>"><{$item.sender}></a>|<{$item.time}>
</li>
<{/foreach}>
<{else}>
<li>没有任何信件</li>
<{/if}>
</ul>
<div class="sec nav">
<form action="<{$mbase}>/mail" method="get">
<{if $curPage != 1}>
	<a href="<{$mbase}>/mail?p=1">首页</a>|
	<a href="<{$mbase}>/mail?p=<{$curPage-1}>">上页</a>|
<{/if}>
<{if $curPage != $totalPage}>
	<a href="<{$mbase}>/mail?p=<{$curPage+1}>">下页</a>|
	<a href="<{$mbase}>/mail?p=<{$totalPage}>">尾页</a>|
<{/if}>
	<a class="plant"><{$curPage}>/<{$totalPage}></a>|
	<a class="plant">转到<input type="text" name="p" size="2" /><input type="submit" value="GO" class="btn" /></a>|
</form>
</div>
<{include file="../plugins/mobile/views/footer.tpl"}>
