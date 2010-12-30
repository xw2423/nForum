<{include file="header.tpl"}>
    	<div class="mbar">
        	<ul>
                <li class="selected"><a href="<{$base}>/user/info">基本资料修改</a></li>
                <li><a href="<{$base}>/user/passwd">昵称密码修改</a></li>
                <li><a href="<{$base}>/user/custom">用户自定义参数</a></li>
                <li><a href="<{$base}>/mail">用户信件服务</a></li>
                <li><a href="<{$base}>/friend">好友列表</a></li>
                <li><a href="<{$base}>/fav">收藏版面管理</a></li>
            </ul>					
        </div>
        <div class="b-content corner">
        	<div id="c_content" class="corner">
				<form method="post" action="">
                <h6>基本信息</h6>
                <ul>
                    <li>
						<span class="b-left-m">性别：</span>
						<input type="radio" name="gender" <{if $gender=="1"}>checked="true"<{/if}> value="1" />&nbsp;男&nbsp;
						<input type="radio" name="gender" <{if $gender=="2"}>checked="true"<{/if}> value="2" />&nbsp;女
					</li>
                    <li>
						<span class="b-left-m">出生日期：</span>
						<input class="input-text" size="3" type="text" name="year" value="<{$year}>" />年
						<input size="2" class="input-text" type="text" name="month" value="<{$month}>" />月
						<input size="2" class="input-text" size="2" type="text" name="day" value="<{$day}>" />日
						<span class="b-right-m">如果不想填，请全部留空</span></li>
                </ul>
                <h6>头像设置</h6>
                <ul>
                    <li class="def">
						<span class="b-left-m">自定义头像：</span>
						<iframe src="<{$base}>/user/face" width="420px" frameborder="0" id="upload"></iframe>
					</li>
                    <li class="def">
                    	<div class="u-img-d">
                    		<div><span>图像位置:</span><input type="text" class="input-text" value="<{$myface_url}>" id="furl" name="furl"/></div>
							<div><span>宽度:</span><input type="text" class="input-text" value="<{$myface_w}>" id="fwidth" name="fwidth"/></div>
							<div><span>高度:</span><input type="text" class="input-text" value="<{$myface_h}>" id="fheight" name="fheight"/></div>
                    	</div>
                        <div class="u-img-show">
							<div class="imgss">
								<img src="<{$static}><{$base}><{$myface}>" id="fpreview" <{if $myface_w != ""}>width="<{$myface_w}>px"<{/if}> <{if $myface_h != ""}>height="<{$myface_h}>px"<{/if}> />
							</div>
							<div class="maxDiv"></div>
                        </div>
                    </li>
                </ul>
                <h6>联系信息</h6>
                <ul>
                    <li><span class="b-left-m">OICQ号码：</span><input class="input-text" type="text" name="qq" value="<{$qq}>" /><span class="b-right-m">填写您的QQ地址，方便与他人的联系</span></li>
                    <li><span class="b-left-m">MSN：</span><input class="input-text" type="text" name="msn" value="<{$msn}>" /><span class="b-right-m">填写您的MSN地址，方便与他人的联系</span></li>
                    <li><span class="b-left-m">主页：</span><input class="input-text" type="text" name="homepage" value="<{$homepage}>" /><span class="b-right-m">填写您的个人主页地址，展示您的网上风采</span></li>
                    <li><span class="b-left-m">Email：</span><input class="input-text" type="text" name="email" value="<{$email}>"/><span class="b-right-m">您的有效电子邮件地址</span></li>
                    <li><span class="b-left-m">签名档：</span><textarea class="input-text b-textarea" name="signature"><{$sig}></textarea></li>
                </ul>
                <div class="b-op"><input type="submit" class="button" value="提交修改" /><input class="button" type="reset" value="重写" /></div>
                </form>
            </div>
    	</div>
<{include file="footer.tpl"}>
