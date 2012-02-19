<{include file="../plugins/mobile/views/header.tpl"}>
<div class="sec nav">
	<a href="<{$mbase}>/refer/at">@我的文章</a>|
	<a href="<{$mbase}>/refer/reply/">回复我的文章</a>|
	<a href="<{$mbase}>/refer/<{$type}>/read?index=all">全部已读</a>
</div>
<div class="sec nav">
<form action="<{$mbase}>/refer/<{$type}>" method="get">
<{if $curPage != 1}>
	<a href="<{$mbase}>/refer/<{$type}>?p=1">首页</a>|
	<a href="<{$mbase}>/refer/<{$type}>?p=<{$curPage-1}>">上页</a>|
<{/if}>
<{if $curPage != $totalPage}>
	<a href="<{$mbase}>/refer/<{$type}>?p=<{$curPage+1}>">下页</a>|
	<a href="<{$mbase}>/refer/<{$type}>?p=<{$totalPage}>">尾页</a>|
<{/if}>
	<a class="plant"><{$curPage}>/<{$totalPage}></a>|
	<a class="plant">转到&nbsp;<input type="text" name="p" size="2" />&nbsp;<input type="submit" value="GO" class="btn" /></a>
</form>
</div>
<ul class="list sec">
<{if isset($info)}>
<{foreach from=$info item=item}>
<li<{cycle values=', class="hla"'}>>
    <div>
		<a href="<{$mbase}>/refer/<{$type}>/read?index=<{$item.index}>"<{if !$item.read}> class="top"<{/if}>><{$item.title}></a>
    </div>
    <div>
		<a href="<{$mbase}>/refer/<{$type}>/delete?index=<{$item.index}>">删除</a>&nbsp;<{$item.time}>&nbsp;<a href="<{$mbase}>/user/query/<{$item.user}>"><{$item.user}></a>
    </div>
</li>
<{/foreach}>
<{else}>
<li>没有任何文章</li>
<{/if}>
</ul>
<div class="sec nav">
<form action="<{$mbase}>/refer/<{$type}>" method="get">
<{if $curPage != 1}>
	<a href="<{$mbase}>/refer/<{$type}>?p=1">首页</a>|
	<a href="<{$mbase}>/refer/<{$type}>?p=<{$curPage-1}>">上页</a>|
<{/if}>
<{if $curPage != $totalPage}>
	<a href="<{$mbase}>/refer/<{$type}>?p=<{$curPage+1}>">下页</a>|
	<a href="<{$mbase}>/refer/<{$type}>?p=<{$totalPage}>">尾页</a>|
<{/if}>
	<a class="plant"><{$curPage}>/<{$totalPage}></a>|
	<a class="plant">转到&nbsp;<input type="text" name="p" size="2" />&nbsp;<input type="submit" value="GO" class="btn" /></a>
</form>
</div>
<{include file="../plugins/mobile/views/footer.tpl"}>
