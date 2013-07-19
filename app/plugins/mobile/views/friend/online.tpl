<{include file="../plugins/mobile/views/header.tpl"}>
<ul class="sec slist">
<{if isset($friends)}>
<li class="f">序号|
ID|
状态|
登陆IP|
发呆|
操作
</li>
<{foreach from=$friends item=item key=k}>
<li><{$k+1}>.|
<a href="<{$mbase}>/user/query/<{$item.fid}>"><{$item.fid}></a>|
<{$item.mode}>|
<{$item.from}>|
<{$item.idle}>|
<a href="<{$mbase}>/mail/send?id=<{$item.fid}>">发信问候</a></li>
<{/foreach}>
<{else}>
<li class="f">您没有任何好友</li>
<{/if}>
</ul>
<{include file="../plugins/mobile/views/footer.tpl"}>
