	<{include file="s_nav.tpl" nav_left="友情链接"}>
        <div class="b-content">
			<div id="f_link" class="corner">
				<h6>文字链接</h6>	
				<{if !empty($plant)}>
				<ul>
				<{foreach from=$plant item=item}>
					<{if !empty($item[0])}>
					<li><a href="http://<{$item[1]}>" target="_blank"><{$item[0]}></a></li>
					<{else}>
					<li>&nbsp;</li>
					<{/if}>
				<{/foreach}>
				</ul>
				<{/if}>
				<div id="f_bottom">
					<a href="<{$base}>">欢迎和<{$siteName}>交换友情链接</a>
				</div>
			</div>
    	</div>
