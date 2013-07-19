<!DOCTYPE html>
<html>
<head>
<meta charset="<{$encoding}>">
<meta name="keywords" content="<{$keywords}>" />
<meta name="description" content="<{$description}>" />
<meta name="author" content="xw2423@BYR" />
<title><{$webTitle}></title>
<link rel="shortcut icon" type="image/x-icon" href="<{$static}><{$base}>/favicon.ico">
<!--[if lt IE 9]>
<script src="<{$static}><{$base}>/js/html5.js"></script>
<![endif]-->
<{include file="css.tpl"}>
</head>
<body>
	<div id="wrap">
	<p class="title"><{$siteName}>广告管理系统</p>
	<div id="op" class="ui-corner-all" >
	<ul id="selection">
		<li><a href="<{$base}>/adv/1" <{if $advType==1}>class="selected"<{/if}>>进站</a></li>
		<li><a href="<{$base}>/adv/2" <{if $advType==2}>class="selected"<{/if}>>banner</a></li>
		<li><a href="<{$base}>/adv/3" <{if $advType==3}>class="selected"<{/if}>>进站广告</a></li>
		<li><a href="<{$base}>/adv/4" <{if $advType==4}>class="selected"<{/if}>>左侧广告</a></li>
	</ul>
	<div id="page">
        <input type="button" class="button" value="增加" id="b_add"/>
        <{include file="pagination.tpl" page_name='总数'}>
	</div>
	</div>
	<div id="adv_filter" class="ui-corner-all">
        <form action="" method="get">
        <ul>
            <li><span>备注:</span><input type="text" name="remark" class="input-text" value="<{$remark|default:''}>"/></li>
            <li><span>开始时间:</span><input type="text" name="sTime" class="input-text" value="<{$sTime|default:''}>"/></li>
            <li><span>结束时间:</span><input type="text" name="eTime" class="input-text" value="<{$eTime|default:''}>"/></li>
            <li><input type="submit" class="button" value="筛选"/></li>
        </ul>
        </form>
	</div>
	<div id="adv_main">
	<table class="ui-corner-all" cellpadding="0" cellspacing="0">
		<tr>
			<th class="ui-state-default c_1">序号</th>
			<th class="ui-state-default c_2">文件名</th>
			<th class="ui-state-default c_3">链接</th>
<{if ($type)}>
			<th class="ui-state-default c_4">开始时间</th>
			<th class="ui-state-default c_5">结束时间</th>
			<th class="ui-state-default c_6">特权</th>
<{else}>
			<th class="ui-state-default c_4">启用</th>
			<th class="ui-state-default c_5">顺序</th>
<{/if}>
			<th class="ui-state-default c_8">备注</th>
			<th class="ui-state-default c_7">操作</th>
		</tr>
<{if isset($info)}>
<{foreach from=$info item=item key=k}>
		<tr class="ui-widget-content<{if $hasPrivilege && $item.privilege == '1' || !$hasPrivilege && isset($item.used) && $item.used == '1' || $item.switch == '1'}> used<{/if}>">
			<td class="c_1" id="<{$item.aid}>"><{$k+1}></td>
			<td class="c_2"><a href="<{$base}>/<{$dir}>/<{$item.file}>"><{$item.file}></a></td>
			<td class="c_3"><a href="<{$item.url}>"><{$item.url|default:"&nbsp;"}></a></td>
<{if ($type)}>
			<td class="c_4"><{$item.sTime}></td>
			<td class="c_5"><{$item.eTime}></td>
			<td class="c_6"><{if $item.privilege == 1}>是<{else}>否<{/if}></td>
<{else}>
			<td class="c_4"><{if $item.switch == 1}>是<{else}>否<{/if}></td>
			<td class="c_5"><{$item.weight}></td>
<{/if}>
			<td class="c_8"><{$item.remark|default:"&nbsp;"}></td>
			<td class="c_7">
				<input type="button" class="button b_pre" value="预览" />
				<input type="button" class="button b_mod" _type="<{$type}>"value="修改" />
				<input type="button" class="button b_del" value="删除" />
			</td>
		</tr>
<{/foreach}>
<{else}>
		<tr class="ui-widget-content">
<{if ($type)}>
			<td colspan="8">没有文件</td>
<{else}>
			<td colspan="7">没有文件</td>
<{/if}>
		</tr>
<{/if}>
	</table> 
	</div>
</div>
<form id="adv_form_del" style="display:none" action="<{$base}>/adv/<{$advType}>/del" method="post">
	<input type="hidden" name="aid"/>
	<input type="hidden" name="p" value="<{$page}>"/>
</form>
</body>
<script id="tmpl_adv" type="text/template">
<form id="adv_form" action="<{$base}>/adv/<{$advType}>/<%=ac%>" method="post" class="dialog-form" ENCTYPE="multipart/form-data">
	<ul>
<%if(ac=='add'){%>
	<li><span>图片:</span><input type="file" name="img"/></li>
<%}%>
	<li><span>链接:</span><input type="text" value="<%=url%>" name="url" class="input-text"/></li>
<{if ($type)}>
	<li><span>开始时间:</span><input type="text" value="<%=stime%>" name="sTime" class="input-text"/></li>
	<li><span>结束时间:</span><input type="text" value="<%=etime%>" name="eTime" class="input-text"/></li>
	<li><span>特权:</span><input type="checkbox" name="privilege"<%if(priv){%> checked="checked"<%}%> /></li>
<{else}>
	<li><span>开启:</span><input type="checkbox" name="switch"<%if(sw){%> checked="checked"<%}%>/></li>
	<li><span>顺序:</span><input type="text" value="<%=weight%>" name="weight" class="input-text"/></li>
<{/if}>
	<li><span>备注:</span><input type="text" value="<%=remark%>" name="remark" class="input-text"/></li>
	</ul>
<%if(aid){%>
	<input type="hidden" name="aid" value="<%=aid%>" />
<%}%>
	<input type="hidden" name="p" value="<{$page}>"/>
</form>
</script>
<script id="tmpl_adv_preview" type="text/template">
<div id="fav_preview" style="text-align:center">
	<img src="<%=img%>" />
</div>
</script>
<script id="tmpl_adv_edit" type="text/template">
<form id="addform" action="<{$base}>/adv/<{$advType}>/add" method="post" class="dialog-form" ENCTYPE="multipart/form-data">
	<ul>
	<li><span>图片:</span><input type="file" name="img"/></li>
	<li><span>链接:</span><input type="text" name="url" class="input-text"/></li>
<{if ($type)}>
	<li><span>开始时间:</span><input type="text" name="sTime" class="input-text"/></li>
	<li><span>结束时间:</span><input type="text" name="eTime" class="input-text"/></li>
	<li><span>特权:</span><input type="checkbox" name="privilege" /></li>
<{else}>
	<li><span>开启:</span><input type="checkbox" name="switch" /></li>
	<li><span>顺序:</span><input type="text" name="weight" class="input-text"/></li>
<{/if}>
	<li><span>备注:</span><input type="text" name="remark" class="input-text"/></li>
	<li><input type="submit" class="submit" value="添加"/></li>
	</ul>
	<input type="hidden" name="p" value="<{$page}>"/>
</form>
</script>
<{include file="script.tpl"}>
</html>
