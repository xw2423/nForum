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

<!--header start-->
<header id="top_head">
    <!--top-menu start-->
    <aside id="top_menu">
        <ul>
            <li><a href="<{$base}>#">合作交流</a></li>
            <li><a href="<{$base}>#">论坛帮助</a></li>
            <li><a href="<{$base}>/flink">友情链接</a></li>
            <li><a href="<{$base}>#">意见建议</a></li>
        </ul>
    </aside>
    <!--top-menu end-->

    <!--logo start-->
    <figure id="top_logo">
        <a href="<{$base}><{$home}>">
            <img src="<{$static}><{$base}>/img/logo.gif" />
        </a>
    </figure>
    <!--logo end-->

    <!--ban_ner start-->
    <article id="ban_ner">
        <div id="ban_ner_wrapper"></div>
    </article>
    <!--ban_ner end-->

</header>
<!--header end-->

<{include file="left.tpl"}>

<!--main start-->
<section id="main" class="corner">

    <!--notice start-->
    <nav id="notice" class="corner">
        <div id="notice_nav"><{if $notice[0].url != ""}><a href="<{$base}><{$notice[0].url}>"><{$notice[0].text}></a><{else}><{$notice[0].text}><{/if}><{section loop=$notice name=key start=1}>&ensp;>>&ensp;<{if $notice[key].url != ""}><a href="<{$base}><{$notice[key].url}>"><{$notice[key].text}></a><{else}><{$notice[key].text}><{/if}><{/section}></div>
    </nav>
    <!--notice end-->

    <!--body start-->
    <section id="body" class="corner">
