

<script type='text/javascript' src='{{asset('quality/staticsite/js/jquery/common.js')}}'></script>

<script>
    scrolltotop.controlattrs={offsetx:20, offsety:150};
    scrolltotop.controlHTML = '<img src="{{asset('quality/staticsite/image/gototop.gif')}}" />';
    scrolltotop.anchorkeyword = '#gotop';
    scrolltotop.title = '回顶部';
    scrolltotop.init();
</script>
<!--gotop end-->

<script type="text/javascript">
    window.onload = window.onresize = window.onscroll = function ()
    {
        var oBox = document.getElementById("qqbox_zzjs");
        var oLine = document.getElementById("online_wwwzzjsnet");
        var oMenu = document.getElementById("menu_zzjs_net");
        var iScrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        setTimeout(function ()
        {
            clearInterval(oBox.timer);
            var iTop = parseInt((document.documentElement.clientHeight - oBox.offsetHeight)/2) + iScrollTop;
            oBox.timer = setInterval(function ()
            {
                var iSpeed = (iTop - oBox.offsetTop) / 8;
                iSpeed = iSpeed > 0 ? Math.ceil(iSpeed) : Math.floor(iSpeed);
                oBox.offsetTop == iTop ? clearInterval(oBox.timer) : (oBox.style.top = oBox.offsetTop + iSpeed + "px");
            }, 30)
        }, 100)
        oBox.onmouseover = function ()
        {
            this.style.width = 138 + "px";
            oLine.style.display = "block";
            oMenu.style.display = "none";
        };
        oBox.onmouseout = function ()
        {
            this.style.width = '';
            oLine.style.display = "none";
            oMenu.style.display = "block";
        };
    };
</script>
{{--<script type="text/javascript" src="http://sxsrzrk.com/Public/jquery/tc.js"></script><!--在线客服 begin -->--}}
