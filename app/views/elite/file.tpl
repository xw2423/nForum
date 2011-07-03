<{include file="header.tpl"}>
    <{capture name=n_right}><a href="<{$base}>/elite/path<{if !empty($parent)}>?v=<{$parent}><{/if}>" style="color:#fff;font-size:12px" >返回</a><{/capture}>
	
	<{include file="s_nav.tpl" nav_left="精华区文章阅读" nav_right=$smarty.capture.n_right}>
	<div class="a-wrap">
	<div class="rc-head"><b style="background-color:#D4E6FC" class="rc1"></b><b style="background-color:#fff;border-color:#D4E6FC" class="rc2"></b><b style="background-color:#fff;border-color:#D4E6FC" class="rc3"></b></div>
	<table class="article">
		<tr class="a-body">
			<td class="a-content a-no-bottom a-no-top">
				<p><{$content}></p>
			</td>
		</tr>
	</table>
	<div class="rc-bottom"><b style="background-color:#fff;border-color:#D4E6FC" class="rc3"></b><b style="background-color:#fff;border-color:#D4E6FC" class="rc2"></b><b style="background-color:#D4E6FC" class="rc1"></b></div>
	</div>
<{include file="footer.tpl"}>
