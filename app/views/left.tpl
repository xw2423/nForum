<!--menu start-->
<div id="menu" class="m-hide">
    <!--login start-->
<{if !($islogin)}>
	<div class="u-login corner">
		<form class="user_login_form" action="<{$base}>/login<{if isset($from)}>?from=<{$from}><{/if}>" method="post">
		<div><span>�ʺ�:</span><input type="text" id="id" class="input-text input" name="id"/></div>
		<div><span>����:</span><input type="password" id="pwd" class="input-text input" name="passwd"/></div>
		<div class="l-check"><input type="checkbox" id="c_auto" name="CookieDate" value="2"/><label for="c_auto">�´��Զ���¼</label></div>
		<div class="b-op">
			<input type="submit" id="bb_login" class="submit" value="��¼" /><input class="submit" type="button" value="ע��" id="bb_reg"/>
		</div>
        </form>  
    </div>
<{else}><{if $newNum != 0}>
	<bgsound src="<{$base}>/files/audio/mail.wav" /><{/if}>
    <div class="u-login-info corner">
    	<div><samp class="ico-pos-cdot"></samp>��ӭ<a href="<{$base}>/user/query/<{$id}>" title="<{$id}>"><{$id|truncate:11:"..."}></a></div>
        <ul>
        	<li><a href="<{$base}>/mail">�ҵ��ռ���<{if $newNum != 0}><span class="new_mail">(<{$newNum}>��)</span><{/if}></a></li>
            <!--<li><a href="#">�ҵĸ��˲���</a></li>-->
            <li><a href="<{$base}>/fav">�ҵ��ղؼ�</a></li>
        	<li><a href="<{$base}>/widget/add">������ҳ����</a></li>
        	<!--<li><a href="#">��̳��������</a></li>-->
            <li><a href="<{$base}>/logout">�˳���¼</a></li>
        </ul>
    </div>
<{/if}>
    <!--login end-->
	<div id="left-line">
		<samp class="ico-pos-hide"></samp>
	</div>
    <!--function list start -->
	<div id="xlist" class="corner">
    	<ul>
            <li class="slist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0);">ȫ��������</a></span>
                <ul class="x-child ajax"><li>{url:<{$base}>/slist?uid=<{$id}>&root=list-section}</li>
                </ul>
            </li>
            <!--
            <li class="nlist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0);">�·���������</a></span>
                <ul class="x-child ajax"><li>{url:<{$base}>/nlist?uid=<{$id}>&root=list-favor}</li>
                </ul>
            </li>
            -->
<{if $islogin}>
            <li class="flist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0);">�ҵ��ղؼ�</a></span>
                <ul id="list-favor" class="x-child ajax"><li>{url:<{$base}>/flist?uid=<{$id}>&root=list-favor}</li></ul>
            </li>
<{/if}>
            <li class="clist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0)">�������</a></span>
                <ul class="x-child" id="list-control">
            <{if $islogin}>
                <{if !$isReg}>
                    <li class="leaf"><span class="text"><a href="<{$base}>/reg/form"><samp class="ico-pos-dot"></samp>��дע�ᵥ</a></span></li>
                <{/if}>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/info" ><samp class="ico-pos-dot"></samp>���������޸�</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/passwd" ><samp class="ico-pos-dot"></samp>�ǳ������޸�</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/custom" ><samp class="ico-pos-dot"></samp>�û��Զ������</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/friend" ><samp class="ico-pos-dot"></samp>�����б�</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/fav" ><samp class="ico-pos-dot"></samp>�ղؼй���</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/online" ><samp class="ico-pos-dot"></samp>�����û�</a></span></li>
            <{/if}>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/query" ><samp class="ico-pos-dot"></samp>��ѯ�û�</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/s" ><samp class="ico-pos-dot"></samp>��������</a></span></li>
                </ul>
            </li>
			<li><span class="x-leaf"><span class="toggler"></span><a href="<{$base}>/vote">ͶƱϵͳ</a></span></li>
            <li><span class="x-leaf"><span class="toggler"></span><a href="<{$base}>/elite/path">������</a></span></li>
            <li><span class="x-leaf"><span class="toggler"></span><a href="telnet://#">Telnet��¼</a></span></li>
            <li><span class="x-leaf x-search"><span class="toggler"></span><input type="text" class="input-text" value="����������" id="b_search"/></span></li>
        </ul>
    </div>
    <!--function list end-->
	<div id="adv">
<{foreach from=$advs item=item}>
		<a href="<{$item.url}>"><img src="<{$item.path}>" /></a>
<{/foreach}>
	</div>
</div>
<!--menu end-->
