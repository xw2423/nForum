<{include file="../plugins/mobile/views/header.tpl"}>
<{if $parent}>
<div class="sec sp">
	<a href="<{$mbase}><{$parent}>">上一层</a>
</div>
<{/if}>
<ul class="slist sec">
<{if $info}>
<{foreach from=$info item=item}>
<li<{cycle values=', class="hl"'}>><{if $item.dir}><font color="#f60">目录</font><{else}>版面<{/if}>|<a href="<{$mbase}><{$item.url}>"><{$item.desc}><{if $item.name != ""}>(<{$item.name}>)<{/if}></a></li>
<{/foreach}>
<{else}>
<li>没有任何版面</li>
<{/if}>
</ul>
<{include file="../plugins/mobile/views/footer.tpl"}>
