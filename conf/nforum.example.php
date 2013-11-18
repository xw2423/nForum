<?php
/********* app config ******************/
$export['application']['encoding'] = 'GBK';
$export['application']['debug'] = true;

/********* site config ******************/
$export['site']['name'] = 'NFORUM测试论坛';
#类型: String
#描述: 站点名称

$export['site']['desc'] = 'NFORUM测试论坛';
#类型: String
#描述: 站点标题，网站默认标题为name-desc

$export['site']['keywords'] = 'nforum kbs bbs';
#类型: String
#描述: 站点关键字，存在于html的head中

$export['site']['description'] = 'NFORUM测试论坛';
#类型: String
#描述: 站点描述，存在于html的head中

$export['site']['static'] = '';
#类型: String
#描述: 资源文件域名，对于不需要cookie验证的资源文件，可以用此域名访问。如果值为空则与当前域名相同。

$export['site']['base'] = '';
#类型: String
#描述: 如果nForum在非web根目录下，请在此设置web虚拟目录，如'/bbs'

$export['site']['home'] = '/default';

$export['site']['preIndex'] = true;
#类型: Boolean
#描述: 是否启用进站页面，即guest用户访问根域名时，自动跳转至进站页面/index

$export['site']['notice']['text'] = '公告:&nbsp;啦啦啦，这是一个公告';
$export['site']['notice']['url'] = '';
#类型: String
#描述: nForum首页导航公告&链接

/********* module config ******************/
$export['modules']['install'] = array('index', 'vote', 'mobile', 'api');

$export['modules']['vote']['base'] = '/vote';

$export['modules']['mobile']['base'] = '/m';
$export['modules']['mobile']['domain'] = '';
#类型: String
#描述: Mobile模块的独立域名，不含http://，配置后使用该域名直接访问Mobile模块

$export['modules']['api']['base'] = '/api';
$export['modules']['api']['domain'] = '';
#类型: String
#描述: Api模块的独立域名，不含http://，配置后使用该域名直接访问Api模块

$export['modules']['api']['page_item_limit'] = 50;

/********* plugin config ******************/
$export['plugins']['install'] = array('uaacl', 'ipacl');

/********* view config ******************/
$export['view']['smarty']['compile_check'] = true;
#类型: Boolean
#描述: 是否检查模板修。如果为true，修改视图文件后自动重新编译模板

$export['view']['smarty']['force_compile'] = false;
#类型: Boolean
#描述: 是否强制编译模板

$export['view']['pack']['html'] = true;
#类型: Boolean
#描述: 是否压缩html
#
$export['view']['pack']['js'] = true;
#类型: Boolean
#描述: 是否压缩javascript

$export['view']['pack']['css'] = true;
#类型: Boolean
#描述: 是否压缩css

/********* adv config ******************/
$export['adv']['path'] = '/files/adv';

#id which can access advertisment managment page
$export['adv']['id'] = array('SYSOP');
#类型: Array
#描述: 可访问广告管理系统的id

/********* thumbnail config ******************/
$export['thumbnail']['small'] = array(120, null);
$export['thumbnail']['middle'] = array(240, null);
#类型: Array
#描述: 缩略图设置,key为缩略图类别,value为宽度和高度,不设置用null代替

/********* ubb config ******************/
$export['ubb']['parse'] = true;
#类型: Boolean
#描述: 是否解析ubb代码

$export['ubb']['syntax'] = '';
#类型: String
#描述: 语法高亮的SyntaxHighlighter3.x的目录名称，请把SyntaxHighlighter3.x放在www目录下，为空即不启用语法高亮

/********* search config ******************/
$export['search']['site'] = false;
#类型: Boolean
#描述: 是否开启全站搜索,如果开启,只有管理员能使用

$export['search']['max'] = 999;
#类型: Int
#描述: 版面默认搜索返回的最大文章数

$export['search']['day'] = 7;
#类型: Int
#描述: 版面默认搜索文章的最大间隔天数

/********* other config ******************/
$export['ajax']['check'] = true;
#类型: Boolean
#描述: 是否检测ajax请求的header

$export['redirect']['time'] = 3;
#类型: Int
#描述: ajax对话框消失并执行默认动作的时间间隔

$export['cache']['second'] = 300;
#类型: Int
#描述: HTTP EXPIRES

$export['proxy']['X_FORWARDED_FOR'] = false;
#类型: Boolean
#描述: web服务器前端存在代理时设为true

$export['elite']['root'] = '0Announce';

$export['refer']['enable'] = defined('BBS_ENABLE_REFER');

$export['rss']['num'] = 20;
#类型: Int
#描述: RSS服务输出的条目数

$export['exif'] = array('Photo');
#类型: Array
#描述: 解析图片exif信息的版面

/********* article config ******************/
$export['article']['ref_line'] = BBS_QUOTED_LINES;
$export['article']['quote_level'] = BBS_QUOTE_LEV;
$export['article']['att_num'] = BBS_MAXATTACHMENTCOUNT;
$export['article']['att_size'] = BBS_MAXATTACHMENTSIZE;
$export['article']['att_check'] = false;

/********* user config ******************/
$export['user']['face']['size'] = 1024 * 256;
#类型: Int
#描述: 用户自定义头像大小限制，单位字节

$export['user']['face']['dir'] = '/uploadFace';
#类型: String
#描述: 用户自定义头像上传目录，为整合wForum，此目录设置为wForum建议的目录

$export['user']['face']['ext'] = array('.jpg', '.jpeg', '.gif', '.png');
#类型: Array
#描述: 用户自定义头像允许格式

$export['user']['custom']['userdefine0'] = array(
        array(29,'显示详细用户信息', '是否允许他人看到您的性别、生日等资料', '允许', '不允许'));
//user_define2 is none after 1
$export['user']['custom']['userdefine1'] = array(
        array(0,'隐藏 IP', '是否发文和被查询的时候隐藏自己的 IP 信息','隐藏','不隐藏'),
        array(2,'启用 @ 提醒', '当文章中提到(@)了你时，是否发送提醒消息','发送','不发送'),
        array(3,'启用回复提醒', '当别人回复了你的文章时，是否发送提醒消息','发送','不发送'),
        //bit-31 for column num of web 2 or 3
        array(31,'首页列数', '首页显示的列数','3&nbsp;&nbsp;','2'));
//mailbox_prop is none after 2
$export['user']['custom']['mailbox_prop'] = array(
        array(0,'发信时保存信件到发件箱', '是否发信时自动选择保存到发件箱','保存','不保存'),
        array(1,'删除信件时不保存到垃圾箱', '是否删除信件时不保存到垃圾箱','不保存','保存'),
        array(3,'自动清除过期垃圾邮件', '是否自动清除过期垃圾邮件','是','否'),
);

/********* pagination config ******************/
$export['pagination']['threads'] = 30;
#类型: Int
#描述: 版面每页显示的主题数

$export['pagination']['article'] = 10;
#类型: Int
#描述: 主题每页显示的文章数

$export['pagination']['mail'] = 20;
#类型: Int
#描述: 邮箱每页显示的邮件数

$export['pagination']['friend'] = 20;
#类型: Int
#描述: 每页显示的好友数量

$export['pagination']['search'] = 80;
#类型: Int
#描述: 搜索结果每页显示的数量

/********* cookie config ******************/
$export['cookie']['prefix'] = 'nforum';
#类型: String
#描述: cookie前缀，不能为空

#if cookie domain is empty, it will be the same as domain
$export['cookie']['domain'] = '';
#类型: String
#描述: cookie域，默认为空就好

$export['cookie']['path'] = '/';
#类型: String
#描述: cookie路径

$export['cookie']['encryption'] = false;
#类型: Boolean
#描述: 是否对cookie用ip加密，如果为true可以有效的防止xss，但是如果ip发生变化cookie失效

/********* section list ******************/
for($i = 0;$i <= BBS_SECNUM - 1;$i++){
    $export['section'][constant("BBS_SECCODE{$i}")] = array(constant("BBS_SECNAME{$i}_0"),constant("BBS_SECNAME{$i}_1"));
}

/********* widget config ******************/
$export['widget']['persistent'] = true;
#类型: Boolean
#描述: 首页widget是否在服务器持久化，如果为true服务器自动保存widget的值，如果为false，widget将使用ajax方式获取内容。

$export['widget']['core'] = array('board', 'section', 'favor');

/**
 * the extension widget config is
 * $export['widget']['ext'][category-index] = array(category-name, array(widget-list)[, array(default-file)])
 * it will be include default-file first and check whether have widget
 * if not it will include widget-name.php
 */
$export['widget']['ext']['0'] = array('论坛相关', array('topten', 'recommend', 'bless'), array('classic'));
$export['widget']['ext']['1'] = array('其他', array('weather', 'vote'));

//游客以及登录用户的初始化模块
//example array('col', 'row', 'title'(array), color)
$export['widget']['default']['recommend'] = array('col'=>1, 'row'=>1);
$export['widget']['default']['section-0'] = array('col'=>1, 'row'=>2);
$export['widget']['default']['section-2'] = array('col'=>1, 'row'=>3);
$export['widget']['default']['section-4'] = array('col'=>1, 'row'=>4);
$export['widget']['default']['topten'] = array('col'=>2, 'row'=>1);
$export['widget']['default']['section-1'] = array('col'=>2, 'row'=>2);
$export['widget']['default']['section-3'] = array('col'=>2, 'row'=>3);
$export['widget']['default']['section-5'] = array('col'=>2, 'row'=>4);
$export['widget']['default']['weather'] = array('col'=>3, 'row'=>1);
$export['widget']['default']['vote'] = array('col'=>3, 'row'=>2);
$export['widget']['default']['bless'] = array('col'=>3, 'row'=>3);
$export['widget']['default']['section-6'] = array('col'=>3, 'row'=>4);
$export['widget']['color'] = array(
        0 => array('default', '默认'),
        1 => array('red', '红'),
        2 => array('orange', '橙'),
        3 => array('yellow', '黄'),
        4 => array('green', '绿'),
        5 => array('blue', '蓝'),
        6 => array('white', '白')
        );

/********* db config ******************/
$export['db']['dbms'] = 'mysql';
$export['db']['host'] = 'localhost';
$export['db']['port'] = '3306';

$export['db']['user'] = '';
$export['db']['pwd'] = '';
$export['db']['db'] = '';
#类型: String
#描述: 数据库配置

$export['db']['dsn'] = "{$export['db']['dbms']}:host={$export['db']['host']};port={$export['db']['port']};dbname={$export['db']['db']}";
$export['db']['charset'] = 'gbk';

/********* jsr config ******************/
$export['jsr']['mWidth'] = 1000;
#page in iframe
$export['jsr']['iframe'] = false;
#if jsr.iframe false,the allow page pattern(javascript)
$export['jsr']['allowFrame'] = '';
$export['jsr']['session']['timeout'] = 60;
$export['jsr']['redirect'] = $export['redirect']['time'];
$export['jsr']['keyboard'] = true;
