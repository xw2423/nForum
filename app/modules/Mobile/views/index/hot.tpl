<{include file="header.tpl"}>
<div class="nav sec">
<{foreach from=$secs item=item key=k}>
    <a<{if !strcmp($k,$selected)}> class="plant"<{else}> href="<{$mbase}>/hot/<{$k}>"<{/if}>><{$item}></a>|
<{/foreach}>
</div>
<ul class="slist sec">
<li class="f"><{$secs[$selected]}>热门话题</li>
<{if is_string($hot)}>
<li><{$hot}></li>
<{else}>
<{foreach from=$hot item=item key=k}>
    <li><{$k+1}>|<a href="<{$mbase}><{$item.url}>"><{$item.text}></a></li>
<{/foreach}>
<{/if}>
</ul>
<{include file="footer.tpl"}>
