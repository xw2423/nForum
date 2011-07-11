	<div class="sec sp">
<form action="<{$mbase}>/go" method="get">
	<span class="f">选择讨论区</span><br /><input type="text" name="name" />&nbsp;<input type="submit" value="GO" class="btn" />
</form>
	</div>
	</div>
	<div class="menu nav">
	<a href="<{$mbase}>/" accesskey="0">首页</a>
	|<a href="<{$mbase}>/section" accesskey="2">分区</a>
	|<a href="<{$mbase}>/hot" accesskey="3">热推</a>
<{if $islogin}>
	|<a href="<{$mbase}>/favor" accesskey="4">收藏</a>
	|<a href="<{$mbase}>/mail" accesskey="5">邮箱<{if $newNum != 0}>(<{$newNum}>新)<{/if}></a>
	|<a href="<{$mbase}>/friend/online" accesskey="6">好友</a>
	|<a href="<{$mbase}>/user/logout" accesskey="7">注销(<{$id}>)</a>
<{/if}>
	</div>
	<div class="logo sp">BYR-Team&copy;2010</div>
</div>
</body>
</html>

