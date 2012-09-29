    </section>
    <!--body end-->

</section>
<!--main end-->
<div class="clearfix" style="width:100%"></div>
<!--footer start-->
<footer id="bot_foot">
    <figure id="bot_logo">
        <a href="<{$base}><{$home}>">
            <img src="<{$static}><{$base}>/img/logo_footer.gif" />
        </a>
    </figure>
    <aside id='bot_info'>
        当前论坛上总共有<span class="c-total"><{$webTotal}></span>人在线，其中注册用户<span class="c-user"><{$webUser}></span>人，访客<span class="c-guest"><{$webGuest}></span>人。<br />
        powered by BYR-Team<span class="copyright">&copy;</span>2009-<{$smarty.now|date_format:"%Y"}>.<br />
        all rights reserved
    </aside>
</footer>
<!--footer end-->
<{include file="script.tpl"}>
</body>
</html>
