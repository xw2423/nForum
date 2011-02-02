<{include file="../plugins/mobile/views/header.tpl"}>
<{if !isset($noid)}>
<ul class="sec list">
<li class="f">ID:&nbsp;<{$uid}></li>
<li>昵称:&nbsp;<{$name}></li>
<{if !($hide) || $isAdmin}>
<li>性别:&nbsp;<{$gender}></li>
<li>星座:&nbsp;<{$astro}></li>
<{/if}>
<li>等级:&nbsp;<{$level}></li>
<li>贴数:&nbsp;<{$postNum}></li>
<{if $me || $isAdmin}>
<li>登陆次数:&nbsp;<{$loginNum}></li>
<{/if}>
<li>生命力:&nbsp;<{$life}></li>
<{if $me || $isAdmin}>
<li>注册时间:&nbsp;<{$first}></li>
<{/if}>
<li>上次登录:&nbsp;<{$lastTime}></li>
<li>最后访问IP:&nbsp;<{$lastIP}></li>
<li>当前状态:&nbsp;<{$status}></li>
</ul>
<{/if}>
<div class="sp sec">
查询ID:&nbsp;<input type="text" id="u_txt" size="10"/>&nbsp;<input type="button" value="GO" class="btn" onclick="window.location.href='<{$mbase}>/user/query/'+document.getElementById('u_txt').value"/>
</div>
<{include file="../plugins/mobile/views/footer.tpl"}>
