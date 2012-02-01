<{include file="../plugins/mobile/views/header.tpl"}>
<div class="sec nav">
<{if $canPost}>
	<a href="<{$mbase}>/article/<{$bName}>/post">发表</a>|
<{/if}>
	<a href="<{$mbase}>/board/<{$bName}>">返回</a>
</div>
<div class="sec nav">
<form action="<{$mbase}>/article/<{$bName}>/<{$reid}>" method="get">
<{if $curPage != 1}>
	<a href="<{$mbase}>/article/<{$bName}>/<{$gid}>?p=1">首页</a>|
	<a href="<{$mbase}>/article/<{$bName}>/<{$gid}>?p=<{$curPage-1}>">上页</a>|
<{/if}>
<{if $curPage != $totalPage}>
	<a href="<{$mbase}>/article/<{$bName}>/<{$gid}>?p=<{$curPage+1}>">下页</a>|
	<a href="<{$mbase}>/article/<{$bName}>/<{$gid}>?p=<{$totalPage}>">尾页</a>|
<{/if}>
	<a class="plant"><{$curPage}>/<{$totalPage}></a>|
	<a class="plant">转到&nbsp;<input type="text" name="p" size="2" />&nbsp;<input type="submit" value="GO" class="btn" /></a>
</form>
</div>
<ul class="list sec">
<li class="f">主题:<{$title}></li>
<{foreach from=$info item=item key=k}>
<li<{cycle values=', class="hla"'}>>
    <a name="a<{$item.pos}>"></a>
	<div class="nav hl">
        <div>
            <a class="plant"><{if $item.pos == 0}>楼主<{else}><{$item.pos}>楼<{/if}></a>
            |<a href="<{$mbase}>/user/query/<{$item.poster}>"><{$item.poster}></a>
            |<a class="plant"><{$item.time}></a>
        </div>
        <div>
        <{if $canPost}>
            <a href="<{$mbase}>/article/<{$bName}>/post/<{$item.id}>">回复</a>
        <{/if}>
        <{if $islogin}>
            |<a href="<{$mbase}>/mail/<{$bName}>/send/<{$item.id}>">发信</a>
            |<a href="<{$mbase}>/article/<{$bName}>/forward/<{$item.id}>">转寄</a>
        <{/if}>
        <{if $item.op}>
            |<a href="<{$mbase}>/article/<{$bName}>/edit/<{$item.id}>">编辑</a>
            |<a href="<{$mbase}>/article/<{$bName}>/delete/<{$item.id}>">删除</a>
        <{/if}>
        </div>
	</div>
	<div class="sp"><{$item.content}></div>
</li>
<{/foreach}>
</ul>
<div class="sec nav">
<form action="<{$mbase}>/article/<{$bName}>/<{$reid}>" method="get">
<{if $curPage != 1}>
	<a href="<{$mbase}>/article/<{$bName}>/<{$gid}>?p=1">首页</a>|
	<a href="<{$mbase}>/article/<{$bName}>/<{$gid}>?p=<{$curPage-1}>">上页</a>|
<{/if}>
<{if $curPage != $totalPage}>
	<a href="<{$mbase}>/article/<{$bName}>/<{$gid}>?p=<{$curPage+1}>">下页</a>|
	<a href="<{$mbase}>/article/<{$bName}>/<{$gid}>?p=<{$totalPage}>">尾页</a>|
<{/if}>
	<a class="plant"><{$curPage}>/<{$totalPage}></a>|
	<a class="plant">转到&nbsp;<input type="text" name="p" size="2" />&nbsp;<input type="submit" value="GO" class="btn" /></a>
</form>
</div>
<{if $canPost}>
<div class="sec sp">
<form action="<{$mbase}>/article/<{$bName}>/post/<{$reid}>" method="post">
	<textarea name="content" style="width:100%" rows="1"></textarea><br />
	<input type="submit" class="btn" value="快速回复" />
	<input type="hidden" name="subject" value="<{$reTitle}>" />
	<{if $anony}>
	<input type="hidden" name="anony" value="1" />
	<{/if}>
</form>
</div>
<{/if}>
<{include file="../plugins/mobile/views/footer.tpl"}>
