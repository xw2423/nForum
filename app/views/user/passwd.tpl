<{include file="header.tpl"}>
    	<div class="mbar">
        	<ul>
                <li><a href="<{$base}>/user/info">基本资料修改</a></li>
                <li class="selected"><a href="<{$base}>/user/passwd">昵称密码修改</a></li>
                <li><a href="<{$base}>/user/custom">用户自定义参数</a></li>
                <li><a href="<{$base}>/mail">用户信件服务</a></li>
                <li><a href="<{$base}>/friend">好友列表</a></li>
                <li><a href="<{$base}>/fav">收藏版面管理</a></li>
            </ul>					
        </div>
        <div class="b-content corner">
        	<div id="c_content" class="corner">
                <h6>昵称修改</h6>
                <form method="post" action="">
                <ul>
                    <li><span class="b-left-m">您的昵称：</span><input class="input-text" type="text" name="name" value="<{$name}>" size="30"/></span></li>
                </ul>
                <div class="b-op b-nc-change">
					<input type="submit" class="button" value="提交修改" />
					<input class="button" type="reset" value="重写" />
				</div>
                </form>
                <h6>密码修改</h6>
                <form method="post" action="" id="p_submit">
                <ul>
                    <li><span class="b-left-m">旧密码确认：</span><input class="input-text" type="password" name="pold"/><span class="b-right-m">输入当前密码</span></li>
                    <li><span class="b-left-m">新密码：</span><input class="input-text" type="password"  id="p_new1" name="pnew1"/><span class="b-right-m">输入新密码</span></li>
                    <li><span class="b-left-m">新密码确认：</span><input class="input-text" type="password" id="p_new2" name="pnew2" /><span class="b-right-m">重新输入新密码</span></li>
                </ul>
                <div class="b-op b-nc-change">
					<input type="submit" class="button" value="提交修改"/>
					<input class="button" type="reset" value="重写" />
				</div>
                </form>
            </div>
    	</div>
<{include file="footer.tpl"}>
