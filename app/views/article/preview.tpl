<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="发文预览"}>
	<div class="a-wrap">
	<div class="rc-head"><b style="background-color:#D4E6FC" class="rc1"></b><b style="background-color:#F3F5FC;border-color:#D4E6FC" class="rc2"></b><b style="background-color:#F3F5FC;border-color:#D4E6FC" class="rc3"></b></div>
	<table class="article">
		<tr class="a-head">
			<td class="a-no-bottom a-no-top">
				<ul class="a-func">
					<li>标题:&nbsp;<{$title}></li>
				</ul>
			</td>
		</tr>
		<tr class="a-body">
			<td class="a-content a-no-bottom a-no-top">
				<p><{$content}></p>
			</td>
		</tr>
	</table>
	<div class="rc-bottom"><b style="background-color:#fff;border-color:#D4E6FC" class="rc3"></b><b style="background-color:#fff;border-color:#D4E6FC" class="rc2"></b><b style="background-color:#D4E6FC" class="rc1"></b></div>
	</div>
<{include file="footer.tpl"}>
