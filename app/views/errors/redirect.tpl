<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="论坛信息"}>
        <div class="b-content">
        	<div class="error">
            	<h5><{$msg}>,本页面将在<{$time}>秒后自动跳转至:</h5>
                	<ul>
<{if empty($url.url)}>
						<li><samp class="ico-pos-dot"></samp><a href="javascript:history.go(-1);">返回上一页</a></li>
<{else}>
						<li><samp class="ico-pos-dot"></samp><a href="<{$base}><{$url.url}>"><{$url.text}></a></li>
<{/if}>
                    </ul>
<{if isset($list)}>
            	<h5>您还可以去:&nbsp;</h5>
                	<ul>
<{foreach from=$list item=item}>
						<li><samp class="ico-pos-dot"></samp><a href="<{$base}><{$item.url}>"><{$item.text}></a></li>
<{/foreach}>
                    </ul>
<{/if}>
            </div>
        </div>   
<{include file="footer.tpl"}>
