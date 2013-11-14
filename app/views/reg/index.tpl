    <div id="columns" class="corner">
        <div class="mbar">
            <ul>
                <li class="selected"><a href="javascript:void(0)">用户注册</a></li>
            </ul>
        </div>
        <div class="b-content corner">
            <div id="c_content" class="corner">
                <form action="<{$base}>/reg/ajax_reg.json" method="post" id="f_reg">
                <table id="r_table" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="r_cell_1"><span>用户ID</span>2-12字符，可用英文字母或数字，首字符必须是字母</td>
                        <td class="r_cell_2"><input type="text" class="input-text" name="id" id="t_id" /></td>
                    </tr>
                    <tr>
                        <td class="r_cell_1"><span>密码</span>请输入密码，4-39字符，区分大小写<br />请不要使用任何类似 '*'、' ' 或 HTML 字符</td>
                        <td class="r_cell_2"><input type="password" class="input-text" name="passwd1" id="t_pwd1" /></td>
                    </tr>
                    <tr>
                        <td class="r_cell_1"><span>密码确认</span>请再输一遍密码</td>
                        <td class="r_cell_2"><input type="password" class="input-text" name="passwd2" id="t_pwd2" /></td>
                    </tr>
                    <tr>
                        <td class="r_cell_1"><span>昵称</span>您在BBS上的昵称，2-39字符，中英文不限</td>
                        <td class="r_cell_2"><input type="text" class="input-text" name="name" id="t_name"/></td>
                    </tr>
                    <tr>
                        <td class="r_cell_1"><span>真实姓名</span>请用中文, 至少2个汉字</td>
                        <td class="r_cell_2"><input type="text" class="input-text" name="tname" id="t_tname"/></td>
                    </tr>
                    <tr>
                        <td class="r_cell_1"><span>性别</span>请选择您的性别</td>
                        <td class="r_cell_2"><input type="radio" name="gender" checked value="1"/><samp class="ico-pos-online-man"></samp>男&emsp;&emsp;<input type="radio" name="gender" value="2"/><samp class="ico-pos-online-woman" ></samp>女</td>
                    </tr>
                    <tr>
                        <td class="r_cell_1"><span>学校系级或工作单位</span>在读生请填写学校\年级\专业\班级<br />社会人士请填写单位全称\部门</td>
                        <td class="r_cell_2"><input type="text" class="input-text" name="dept" id="t_dept"/></td>
                    </tr>
                    <tr>
                        <td class="r_cell_1"><span>详细住址</span>在读生请填写宿舍地址具体到宿舍号<br />社会人士请填写居住地址具体到房门号</td>
                        <td class="r_cell_2"><input type="text" class="input-text" name="address" id="t_address"/></td>
                    </tr>
                    <tr>
                        <td class="r_cell_1"><span>生日</span>生日格式为XXXX-XX-XX</td>
                        <td class="r_cell_2"><input type="text" class="input-text" name="year" id="t_year" size="4"/>年&emsp;<input type="text" class="input-text" name="month" id="t_month" size="2"/>月&emsp;<input type="text" class="input-text" name="day" id="t_day" size="2"/>日</td>
                    </tr>
                    <tr>
                        <td class="r_cell_1"><span>联系电话</span>请填写您的方式</td>
                        <td class="r_cell_2"><input type="text" class="input-text" name="phone" id="t_phone"/></td>
                    </tr>
                    <tr>
                        <td class="r_cell_1"><span>电子邮件</span>请填写有效的电子邮件</td>
                        <td class="r_cell_2"><input type="text" class="input-text" name="email" id="t_email"/></td>
                    </tr>
                    <tr>
                        <td class="r_cell_1"><span>验证码</span><font color="red">请输入右边图片中的等式的结果(运算符只包括'+','-')</font></td>
                        <td class="r_cell_2"><input type="text" class="input-text" name="auth" id="t_auth"/><img id="authimg" _src="<{$base}>/authimg" alt="auth code"/></td>
                    </tr>
                </table>
                <div class="r_su"><input type="submit" class="button r_submit" value="我要注册" /></div>
                </form>
            </div>
        </div>
    </div>
