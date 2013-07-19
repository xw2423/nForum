    	<div class="mbar">
        	<ul>
                <li class="selected"><a href="<{$base}>/user/info">基本资料修改</a></li>
                <li><a href="<{$base}>/user/passwd">昵称密码修改</a></li>
                <li><a href="<{$base}>/user/custom">用户自定义参数</a></li>
                <li><a href="<{$base}>/mail">用户信件</a></li>
                <li><a href="<{$base}>/refer">文章提醒</a></li>
                <li><a href="<{$base}>/friend">好友列表</a></li>
                <li><a href="<{$base}>/fav">收藏版面</a></li>
            </ul>					
        </div>
        <div class="b-content corner">
        	<div id="c_content" class="corner">
				<form method="post" action="<{$base}>/user/ajax_info.json">
                <section class="list-block">
                <header>基本信息</header>
                <ul>
                    <li>
						<span class="b-left-m">性别:&ensp;</span>
						<input type="radio" name="gender" <{if $gender=="1"}>checked="true"<{/if}> value="1" />&ensp;男&emsp;
						<input type="radio" name="gender" <{if $gender=="2"}>checked="true"<{/if}> value="2" />&ensp;女
					</li>
                    <li>
						<span class="b-left-m">出生日期:&ensp;</span>
						<input class="input-text" size="3" type="text" name="year" value="<{$year}>" />年
						<input size="2" class="input-text" type="text" name="month" value="<{$month}>" />月
						<input size="2" class="input-text" size="2" type="text" name="day" value="<{$day}>" />日
						<span class="b-right-m">如果不想填，请全部留空</span></li>
                </ul>
                </section>
                <section class="list-block">
                <header>头像设置</header>
                <ul>
                    <li class="def">
						<span class="b-left-m">自定义头像:&ensp;</span>
                        <div id="face_upload">
                            <input id="face_upload_select" type="button" value="选择文件" class="submit" />
                            <span id="face_upload_info"></span>
                        </div>
					</li>
                    <li class="def">
						<span class="b-left-m">&ensp;</span>
                    	<div class="u-img-d">
                    		<div><span>头像位置:</span><input type="text" class="input-text" value="<{$myface_url}>" id="furl" name="furl"/></div>
							<div><span>宽&emsp;&emsp;度:</span><input type="text" class="input-text" value="<{$myface_w}>" id="fwidth" name="fwidth"/></div>
							<div><span>高&emsp;&emsp;度:</span><input type="text" class="input-text" value="<{$myface_h}>" id="fheight" name="fheight"/></div>
                    	</div>
                        <div class="u-img-show">
                            <img src="<{$static}><{$base}><{$myface}>" id="fpreview" <{if $myface_w != ""}>width="<{$myface_w}>px"<{/if}> <{if $myface_h != ""}>height="<{$myface_h}>px"<{/if}> />
                        </div>
                        <div class="clearfix"></div>
                    </li>
                </ul>
                </section>
                <section class="list-block">
                <header>联系信息</header>
                <ul>
                    <li><span class="b-left-m">QQ:&ensp;</span><input class="input-text" type="text" name="qq" value="<{$qq}>" /><span class="b-right-m">填写您的QQ，方便与他人的联系</span></li>
                    <li><span class="b-left-m">MSN:&ensp;</span><input class="input-text" type="text" name="msn" value="<{$msn}>" /><span class="b-right-m">填写您的MSN地址，方便与他人的联系</span></li>
                    <li><span class="b-left-m">主页:&ensp;</span><input class="input-text" type="text" name="homepage" value="<{$homepage}>" /><span class="b-right-m">填写您的个人主页地址，展示您的网上风采</span></li>
                    <li><span class="b-left-m">Email:&ensp;</span><input class="input-text" type="text" name="email" value="<{$email}>"/><span class="b-right-m">您的有效电子邮件地址</span></li>
                    <li><span class="b-left-m">签名档:&ensp;</span><textarea class="input-text b-textarea" name="signature"><{$sig}></textarea></li>
                    <li class="b-op"><input type="submit" class="button" value="提交修改" /><input class="button" type="reset" value="重写" /></li>
                </ul>
                </section>
                </form>
            </div>
    	</div>
