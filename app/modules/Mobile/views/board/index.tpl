<{include file="header.tpl"}>
<div class="sec nav">
<{if $canPost}>
	<a href="<{$mbase}>/article/<{$bName}>/post<{if !$threads}>?s=1<{/if}>">发表</a>
<{/if}>
<{if $threads}>
	<{if $canPost}>|<{/if}><a href="<{$mbase}>/board/<{$bName}>/0">经典</a>
<{else}>
	<{if $canPost}>|<{/if}><a href="<{$mbase}>/board/<{$bName}>">同主题</a>
<{/if}>
	|<a href="<{$mbase}>/board/<{$bName}>/1">文摘</a>
	|<a href="<{$mbase}>/board/<{$bName}>/3">保留</a>
<{if $isBM || $isAdmin}>
	|<a href="<{$mbase}>/board/<{$bName}>/4">回收站</a>
<{/if}>
<{if $isAdmin}>
	|<a href="<{$mbase}>/board/<{$bName}>/5">垃圾箱</a>
<{/if}>
</div>
<div class="sec nav">
<form action="<{$mbase}>/board/<{$bName}><{if !$threads}>/<{$mode}><{/if}>" method="get">
<{if $curPage != 1}>
	<a href="<{$mbase}>/board/<{$bName}><{if !$threads}>/<{$mode}><{/if}>?p=1">首页</a>|

	<a href="<{$mbase}>/board/<{$bName}><{if !$threads}>/<{$mode}><{/if}>?p=<{$curPage-1}>">上页</a>|
<{/if}>
<{if $curPage != $totalPage}>
	<a href="<{$mbase}>/board/<{$bName}><{if !$threads}>/<{$mode}><{/if}>?p=<{$curPage+1}>">下页</a>|
	<a href="<{$mbase}>/board/<{$bName}><{if !$threads}>/<{$mode}><{/if}>?p=<{$totalPage}>">尾页</a>|
<{/if}>
	<a class="plant"><{$curPage}>/<{$totalPage}></a>|
	<a class="plant">转到&nbsp;<input type="text" name="p" size="2" />&nbsp;<input type="submit" value="GO" class="btn" /></a>
</form>
</div>
<ul class="list sec">
<{if $info}>
<{foreach from=$info item=item key=k}>
<li<{cycle values=', class="hla"'}>>
<{if $threads}>
    <div>
		<a href="<{$mbase}>/article/<{$bName}>/<{$item.gid}>"<{if $item.tag}> class="<{$item.tag}>"<{/if}>><{$item.title}></a>(<{$item.num}>)
    </div>
    <div>
		<{$item.postTime}>&nbsp;<a href="<{$mbase}>/user/query/<{$item.poster}>"><{$item.poster}></a>|
		<{$item.replyTime}>&nbsp;<a href="<{$mbase}>/user/query/<{$item.last}>"><{$item.last}></a>
    </div>
<{else}>
    <div>
		<a href="<{$mbase}>/article/<{$bName}>/single/<{if $sort}><{$item.gid}><{else}><{$item.pos}><{/if}>/<{$mode}>"<{if $item.tag}> class="<{$item.tag}>"<{/if}>><{if $item.subject}>●&nbsp;<{/if}><{$item.title}></a><{if $threads}>(<{$item.num}>)<{/if}>
    </div>
    <div>
		<{if $item.top}>[提示]<{else}><{$item.pos}><{/if}>&nbsp;<{$item.postTime}>&nbsp;<a href="<{$mbase}>/user/query/<{$item.poster}>"><{$item.poster}></a>
    </div>
<{/if}>
</li>
<{/foreach}>
<{else}>
<li>该版面没有任何主题</li>
<{/if}>
</ul>
<div class="sec nav">
<form action="<{$mbase}>/board/<{$bName}><{if !$threads}>/<{$mode}><{/if}>" method="get">
<{if $curPage != 1}>
	<a href="<{$mbase}>/board/<{$bName}><{if !$threads}>/<{$mode}><{/if}>?p=1">首页</a>|

	<a href="<{$mbase}>/board/<{$bName}><{if !$threads}>/<{$mode}><{/if}>?p=<{$curPage-1}>">上页</a>|
<{/if}>
<{if $curPage != $totalPage}>
	<a href="<{$mbase}>/board/<{$bName}><{if !$threads}>/<{$mode}><{/if}>?p=<{$curPage+1}>">下页</a>|
	<a href="<{$mbase}>/board/<{$bName}><{if !$threads}>/<{$mode}><{/if}>?p=<{$totalPage}>">尾页</a>|
<{/if}>
	<a class="plant"><{$curPage}>/<{$totalPage}></a>|
	<a class="plant">转到&nbsp;<input type="text" name="p" size="2" />&nbsp;<input type="submit" value="GO" class="btn" /></a>
</form>
</div>
<{include file="footer.tpl"}>
