<?php
/********* system code ******************/
//SYS_ERROR             
$config['code']['0000'] = "系统内部错误";
//SYS_INDEX             
$config['code']['0001'] = "无法读取索引文件";
//SYS_NOLOGIN           
$config['code']['0002'] = "您未登录,请登录后继续操作";
//SYS_NOTMPFILE         
$config['code']['0003'] = "无法创建临时文件";
//SYS_REQUESTERROR      
$config['code']['0004'] = "请求方式错误";
//SYS_AJAXOK            
$config['code']['0005'] = "操作成功";
//SYS_AJAXERROR         
$config['code']['0006'] = "操作失败";
//SYS_NOFILE
$config['code']['0007'] = "找不到指定文件";
//SYS_IPBAN
$config['code']['0008'] = "您的IP地址不允许访问";
//SYS_PLUGINBAN
$config['code']['0009'] = "此应用已被停用";
//SYS_404
$config['code']['0010'] = "链接不存在";
//LOGIN_NOID            
$config['code']['0100'] = "请输入您的用户名";
//LOGIN_ERROR           
$config['code']['0101'] = "您的用户名并不存在，或者您的密码错误";
//LOGIN_MULLOGIN        
$config['code']['0102'] = "您已登录的账号过多，无法重复登录";
//LOGIN_IDBAN           
$config['code']['0103'] = "您的账号已被管理员禁用";
//LOGIN_IPBAN           
$config['code']['0104'] = "您所使用的IP已被本站禁用";
//LOGIN_FREQUENT        
$config['code']['0105'] = "请勿频繁登录";
//LOGIN_MAX             
$config['code']['0106'] = "系统在线人数已达上限，请稍后再访问本站";
//LOGIN_EPOS            
$config['code']['0107'] = "对不起,当前位置不允许登录该ID";
//LOGIN_OK              
$config['code']['0108'] = "登陆成功";
//LOGIN_OUT             
$config['code']['0109'] = "注销成功";
//BOARD_NONE            
$config['code']['0200'] = "未指定版面或链接错误";
//BOARD_UNKNOW          
$config['code']['0201'] = "指定的版面不存在";
//BOARD_NOPERM          
$config['code']['0202'] = "您无权阅读此版面";
//BOARD_READONLY        
$config['code']['0203'] = "本版为只读讨论区";
//BOARD_NOPOST          
$config['code']['0204'] = "您无权在本版发表文章";
//BOARD_NOREPLY         
$config['code']['0205'] = "本版只可发表文章,不可回复文章";
//BOARD_NOTHREADS
$config['code']['0206'] = "本版不存在任何主题";
//BOARD_VOTEFAIL
$config['code']['0207'] = "投票失败";
//BOARD_VOTESUCCUESS
$config['code']['0208'] = "投票成功";
//BOARD_MODEERROR
$config['code']['0209'] = "文章模式错误";
//ARTICLE_NONE          
$config['code']['0300'] = "指定的文章不存在或链接错误";
//ARTICLE_NOREID        
$config['code']['0301'] = "错误的 Re 文编号";
//ARTICLE_NOREPLY       
$config['code']['0302'] = "该文不可回复";
//ARTICLE_NODEL         
$config['code']['0303'] = "错误的文章或您无权删除该文章";
//ARTICLE_NOEDIT        
$config['code']['0304'] = "您无权编辑该文章";
//ARTICLE_EDITERROR     
$config['code']['0305'] = "修改文章失败";
//ARTICLE_EDITOK        
$config['code']['0306'] = "修改文章成功";
//ARTICLE_DELOK        
$config['code']['0307'] = "删除文章成功";
//ARTICLE_REERROR
$config['code']['0308'] = "推荐文章失败";
//ARTICLE_REOK
$config['code']['0309'] = "推荐文章成功";
//ARTICLE_FORWARDOK
$config['code']['0310'] = "转寄文章成功";
//ARTICLE_NOMANAGE
$config['code']['0311'] = "您无权进行管理操作";
//ARTICLE_NOTORIGIN
$config['code']['0312'] = "非主题帖或原主题已删除，操作失败";
//POST_NOSUB            
$config['code']['0400'] = "没有指定文章标题";
//POST_NOCON            
$config['code']['0401'] = "没有指定文章内容";
//POST_ISDIR            
$config['code']['0402'] = "本版为二级目录版";
//POST_FREQUENT         
$config['code']['0403'] = "两次发文/信间隔过密, 请休息几秒后再试";
//POST_BAN              
$config['code']['0404'] = "很抱歉, 你被版务人员停止了本版的发文权力";
//POST_WAIT             
$config['code']['0405'] = "发文成功，但本文可能含有不当内容，需经审核方可发表。请耐心等待站务人员的审核，不要多次尝试发表此文章。";
//POST_OK               
$config['code']['0406'] = "发表成功";
//TMPL_ERROR
$config['code']['0407'] = "该模板不存在或暂不可用";
//USER_NOID             
$config['code']['0500'] = "未知的论坛ID";
//USER_FAVERROR         
$config['code']['0501'] = "读取收藏版面错误";
//USER_EWIDTH
$config['code']['0502'] = "用户自定义图像宽度错误";
//USER_EHEIGHT            
$config['code']['0503'] = "用户自定义图像高度错误";
//USER_SAVEOK
$config['code']['0504'] = "用户资料修改成功";
//USER_NAMEOK
$config['code']['0505'] = "用户昵称修改成功";
//USER_NAMEERROR
$config['code']['0506'] = "用户昵称修改失败";
//USER_PWDOK
$config['code']['0507'] = "更新密码成功";
//USER_PWDERROR
$config['code']['0508'] = "用户密码修改失败";
//USER_OLDPWDERROR
$config['code']['0509'] = "旧密码不正确";
//MAIL_NOBOX
$config['code']['0600'] = "错误的邮箱";
//MAIL_NOMAIL           
$config['code']['0601'] = "不存在该邮件";
//MAIL_SENDERROR        
$config['code']['0602'] = "您的信箱超容，请删除部分邮件(包含废件箱)，或您没有写信权力";
//MAIL_ERROR            
$config['code']['0603'] = "发信失败";
//MAIL_REJECT           
$config['code']['0604'] = "对方拒收你的邮件";
//MAIL_FULL             
$config['code']['0605'] = "对方信箱满";
//MAIL_NOID             
$config['code']['0606'] = "错误的收件人ID";
//MAIL_SENDOK           
$config['code']['0607'] = "邮件发送成功";
//MAIL_DELETEERROR      
$config['code']['0608'] = "邮件删除失败";
//MAIL_DELETEOK         
$config['code']['0609'] = "邮件删除成功";
//MAIL_RENUMERROR         
$config['code']['0610'] = "错误的邮件回复号";
//MAIL_NOPERM         
$config['code']['0611'] = "您无权发信";
//MAIL_FORWARDOK
$config['code']['0612'] = "转寄邮件成功";
//SEC_NOSECION         
$config['code']['0700'] = "分区号错误";
//SEC_NOHOT
$config['code']['0701'] = "分区暂不存在热门话题";
//SEC_NOBOARD
$config['code']['0702'] = "分区不存在任何版面";
//FRIEND_NOPRIV
$config['code']['0800'] = "您没有权限设定好友或者好友个数超出限制";
//FRIEND_EXIST
$config['code']['0801'] = "你的好友名单中已存在这个好友";
//FRIEND_NOEXIST
$config['code']['0802'] = "你的好友名单中不存在这个好友";
//FRIEND_ADDOK
$config['code']['0803'] = "添加好友成功";
//FRIEND_DELETEOK
$config['code']['0804'] = "删除好友成功";
//ELITE_NODIR
$config['code']['0900'] = "精华区目录不存在";
//ELITE_DIRERROR
$config['code']['0901'] = "无法加载目录文件";
//RESET_QUICKNOID
$config['code']['1000'] = "此ID不是通过快速通道注册";
//RESET_OK
$config['code']['1001'] = "密码重置成功,您的密码为111111,请尽快改密码";
//RESET_NONUM
$config['code']['1002'] = "您的ID绑定的手机号错误";
//BIND_PWDERROR
$config['code']['1003'] = "您的密码错误";
//BIND_PHONEERROR
$config['code']['1004'] = "请输入正确的手机号";
//BIND_OK
$config['code']['1005'] = "绑定手机号成功";
//BIND_ERROR
$config['code']['1006'] = "绑定手机号失败";
//P2S_ERROR
$config['code']['1007'] = "绑定学号失败";
//P2S_OK
$config['code']['1008'] = "绑定学号成功";
//REG_FORMAT
$config['code']['1100'] = "用户ID，密码，昵称，其他信息不完全或不符合规范";
//REG_IDUSED
$config['code']['1101'] = "用户ID已使用或为系统关键字";
//REG_PWD
$config['code']['1102'] = "两次输入的密码不一样";
//REG_AUTH
$config['code']['1103'] = "验证码错误";
//REG_OK
$config['code']['1104'] = "注册ID成功，已自动提交注册单，请等待审核从而获得更多权限";
//REG_FORMOK
$config['code']['1105'] = "注册单提交成功";
//REG_REGED
$config['code']['1106'] = "您已经注册成功";
//REG_HAVEFORM
$config['code']['1107'] = "您已提交过注册单，请等待审核";
//ATT_NLIMIT
$config['code']['1201'] = "附件数量超过限制";
//ATT_SLIMIT
$config['code']['1202'] = "附件大小超过限制";
//ATT_NONE
$config['code']['1203'] = "附件不存在";
//ATT_NAMEERROR
$config['code']['1204'] = "附件文件名错误";
//ATT_SAMENAME
$config['code']['1205'] = "已经存在同名附件";
//ATT_NOPERM
$config['code']['1206'] = "无权添加附件";
//ATT_ADDOK
$config['code']['1207'] = "添加附件成功";
//ATT_DELOK
$config['code']['1208'] = "删除附件成功";
//REFER_NONE
$config['code']['1301'] = "未知的提醒";
//REFER_DELETEOK
$config['code']['1302'] = "提醒删除成功";
//REFER_DISABLED
$config['code']['1303'] = "提醒功能未启用";
//DENY_DENIED
$config['code']['1401'] = "用户已经被封禁";
//DENY_NOTDENIED
$config['code']['1402'] = "用户尚未被封禁,无法修改/删除";
//DENY_INVALIDDAY
$config['code']['1403'] = "封禁期限非法";
//DENY_NOREASON
$config['code']['1404'] = "请输入封禁理由";
//DENY_CANTPOST
$config['code']['1405'] = "不能封禁在版面无发表权限的用户";
//DENY_NOID
$config['code']['1406'] = "请输入欲封禁的ID";
//XW_JOKE
$config['code']['9999'] = "WHAT DO YOU WANT TO DO?!";
?>
