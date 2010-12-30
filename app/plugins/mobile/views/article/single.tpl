<{include file="../plugins/mobile/views/header.tpl"}>
<div class="sec nav">
<{if $canPost}>
	<a href="<{$mbase}>/article/<{$bName}>/post?m=1">发表</a>|
<{/if}>
	<a href="<{$mbase}>/article/<{$bName}>/<{$gid}>">展开</a>|
	<a href="<{$mbase}>/article/<{$bName}>/single/<{$gid}>">溯源</a>|
	<a href="<{$mbase}>/board/<{$bName}>/0">返回</a>|
</div>
<ul class="list sec">
<li class="f">主题:<{$title}></li>
<li>
	<div class="nav hl">
	<a href="<{$mbase}>/user/query/<{$poster}>"><{$poster}></a>|
	<a class="plant"><{$time}></a>|
	<br />
	<a href="<{$mbase}>/article/<{$bName}>/post/<{$aid}>?m=1">回复</a>|
	<a href="<{$mbase}>/mail/send/<{$poster}>">发信</a>|
<{if $subject}>
	<a href="<{$mbase}>/article/<{$bName}>/focus/<{$aid}>">推荐</a>|
<{/if}>
<{if $op}>
	<a href="<{$mbase}>/article/<{$bName}>/edit/<{$aid}>">编辑</a>|
	<a href="<{$mbase}>/article/<{$bName}>/delete/<{$aid}>">删除</a>|
<{/if}>
	</div>
	<div class="sp"><{$content}></div>
</li>
</ul>
<div class="sec nav">
<{if $pre}>
	<a href="<{$mbase}>/article/<{$bName}>/single/<{$pre}>">上一篇</a>|
<{/if}>
<{if $next}>
	<a href="<{$mbase}>/article/<{$bName}>/single/<{$next}>">下一篇</a>|
<{/if}>
<{if $tPre}>
	<a href="<{$mbase}>/article/<{$bName}>/single/<{$tPre}>">同主题上篇</a>|
<{/if}>
<{if $tNext}>
	<a href="<{$mbase}>/article/<{$bName}>/single/<{$tNext}>">同主题下篇</a>|
<{/if}>
</div>
<{include file="../plugins/mobile/views/footer.tpl"}>
