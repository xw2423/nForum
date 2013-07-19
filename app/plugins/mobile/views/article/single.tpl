<{include file="../plugins/mobile/views/header.tpl"}>
<div class="sec nav">
<{if $canPost}>
	<a href="<{$mbase}>/article/<{$bName}>/post?s=1">发表</a>|
<{/if}>
	<a href="<{$mbase}>/article/<{$bName}>/<{$gid}>?s=<{$aid}>">展开</a>|
	<a href="<{$mbase}>/article/<{$bName}>/single/<{$gid}>">楼主</a>|
	<a href="<{$mbase}>/article/<{$bName}>/<{$gid}>">同主题展开</a>|
<{if !$subject}>
    <a href="<{$mbase}>/article/<{$bName}>/single/<{$rid}>">溯源</a>|
<{/if}>
	<a href="<{$mbase}>/board/<{$bName}>/<{$mode}>">返回</a>
</div>
<div class="sec nav">
<{if $pre}>
	<a href="<{$mbase}>/article/<{$bName}>/single/<{$pre}>">上一篇</a>
<{/if}>
<{if $next}>
	<{if $pre}>|<{/if}><a href="<{$mbase}>/article/<{$bName}>/single/<{$next}>">下一篇</a>
<{/if}>
<{if $tPre}>
	|<a href="<{$mbase}>/article/<{$bName}>/single/<{$tPre}>">同主题上篇</a>
<{/if}>
<{if $tNext}>
	|<a href="<{$mbase}>/article/<{$bName}>/single/<{$tNext}>">同主题下篇</a>
<{/if}>
</div>
<ul class="list sec">
<li class="f">主题:<{$title}></li>
<li>
	<div class="nav hl">
        <div>
            <a href="<{$mbase}>/user/query/<{$poster}>"><{$poster}></a>|
            <a class="plant"><{$time}></a>|
        </div>
        <div>
        <{if $canPost}>
            <a href="<{$mbase}>/article/<{$bName}>/post/<{$aid}>?s=1">回复</a>
        <{/if}>
        <{if $islogin}>
            |<a href="<{$mbase}>/mail/<{$bName}>/send/<{$aid}>">发信</a>
            |<a href="<{$mbase}>/article/<{$bName}>/forward/<{$aid}>?s=1">转寄</a>
        <{/if}>
        <{if $op}>
            |<a href="<{$mbase}>/article/<{$bName}>/edit/<{$aid}>?s=1">编辑</a>
            |<a href="<{$mbase}>/article/<{$bName}>/delete/<{$aid}>?s=1">删除</a>
        <{/if}>
        </div>
	</div>
	<div class="sp"><{$content}></div>
</li>
</ul>
<div class="sec nav">
<{if $pre}>
	<a href="<{$mbase}>/article/<{$bName}>/single/<{$pre}>">上一篇</a>
<{/if}>
<{if $next}>
	<{if $pre}>|<{/if}><a href="<{$mbase}>/article/<{$bName}>/single/<{$next}>">下一篇</a>
<{/if}>
<{if $tPre}>
	|<a href="<{$mbase}>/article/<{$bName}>/single/<{$tPre}>">同主题上篇</a>
<{/if}>
<{if $tNext}>
	|<a href="<{$mbase}>/article/<{$bName}>/single/<{$tNext}>">同主题下篇</a>
<{/if}>
</div>
<{include file="../plugins/mobile/views/footer.tpl"}>
