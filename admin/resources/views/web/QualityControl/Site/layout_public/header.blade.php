<!--Logo 开始-->
<div id="logo_main">
    <div class="inner">
        <div id="logo">
            <!--网站Logo 开始-->
            <div class="WebLogo">
                <a href="http://www.sxsrzrk.com/"
                ><img
                        src="{{asset('quality/staticsite/image/1594474530.jpg')}}"
                        title="陕西省质量认证认可协会-陕西质量认证咨询中心"
                        alt="陕西省质量认证认可协会-陕西质量认证咨询中心"
                    /></a>
            </div>
            <!--网站Logo 结束-->
        </div>
        <div id="toptell">
            <span>服务热线：</span><i>029-87290790 87294737 87291424 87290659 </i>
        </div>
        <div id="toplogin">
            @if(isset($baseArr['real_name']) && !empty($baseArr['real_name']))
                您好：{{ $baseArr['real_name'] ?? '' }}
            @else
            <a href="{{ url('web/reg') }}">注册</a
            ><a href="{{ url('web/login') }}">登录</a>
            @endif
        </div>
        <div class="c"></div>
    </div>
</div>
<!--Logo 结束-->

<div class="navimain">
    <div class="menu">
        <ul>
            <li><a href="http://www.sxsrzrk.com/index.php" target="_self" class="hide">网站首页</a></li>
            <li>
                <a href="http://www.sxsrzrk.com/index.php/xhjjz.html" target="_self">协会简介</a>
                <ul>
                    <li><a href="http://www.sxsrzrk.com/index.php/xhjj.html" target="_self">协会简介</a></li>
                    <li><a href="http://www.sxsrzrk.com/index.php/xhzc.html" target="_self">协会章程</a></li>
                    <li><a href="http://www.sxsrzrk.com/index.php/lxwm.html" target="_self">联系我们</a></li>
                </ul>
            </li>
            <li>
                <a href="http://www.sxsrzrk.com/index.php/xwzx.html" target="_self">新闻中心</a>
                <ul>
                    <li><a href="http://www.sxsrzrk.com/index.php/xhdt.html" target="_self">协会动态</a></li>
                    <li><a href="http://www.sxsrzrk.com/index.php/hyxx.html" target="_self">行业信息</a></li>
                </ul>
            </li>
            <li>
                <a href="http://www.sxsrzrk.com/index.php/zcfg.html" target="_self">政策法规</a>
                <ul>
                    <li><a href="http://www.sxsrzrk.com/index.php/flfg.html" target="_self">法律法规</a></li>
                    <li><a href="http://www.sxsrzrk.com/index.php/hybz.html" target="_self">行业标准</a></li>
                </ul>
            </li>
            <li>
                <a href="http://www.sxsrzrk.com/index.php/nlyz.html" target="_self">能力验证</a>
                <ul>
                    <li>
                        <a href="http://www.sxsrzrk.com/index.php/nlyzzs.html" target="_self">能力验证知识</a>
                    </li>
                    <li>
                        <a href="http://www.sxsrzrk.com/index.php/nlyzdt.html" target="_self">能力验证动态</a>
                    </li>
                    <li>
                        <a href="http://www.sxsrzrk.com/index.php/nlyzfw.html" target="_self">能力验证查询</a>
                    </li>
                    <li>
                        <a href="http://www.sxsrzrk.com/index.php/nlyzzx.html" target="_self">能力验证咨询</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="http://www.sxsrzrk.com/index.php/xxcx.html" target="_self">技术培训</a>
                <ul>
                    <li><a href="http://www.sxsrzrk.com/index.php/pxbm.html" target="_self">培训报名</a></li>
                    <li><a href="http://www.sxsrzrk.com/index.php/xxpx.html" target="_self">培训信息</a></li>
                </ul>
            </li>
            <li>
                <a href="http://www.sxsrzrk.com/index.php/zlzr.html" target="_self">认证咨询</a>
                <ul>
                    <li><a href="http://www.sxsrzrk.com/index.php/jj.html" target="_self">认证机构简介</a></li>
                    <li><a href="http://www.sxsrzrk.com/index.php/sys.html" target="_self">CMA咨询</a></li>
                    <li><a href="http://www.sxsrzrk.com/index.php/cnaszx.html" target="_self">CNAS咨询</a></li>
                    <li>
                        <a href="http://www.sxsrzrk.com/index.php/sprzzx.html" target="_self">食品认证咨询</a>
                    </li>
                    <li>
                        <a href="http://www.sxsrzrk.com/index.php/gltxrzzxfw.html" target="_self"
                        >管理体系认证咨询服务</a
                        >
                    </li>
                    <li>
                        <a href="http://www.sxsrzrk.com/index.php/sycprzzx.html" target="_self"
                        >工业产品认证咨询</a
                        >
                    </li>
                    <li>
                        <a href="http://www.sxsrzrk.com/index.php/rzrkzs.html" target="_self">认证认可知识</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{url('web/company/0_0_20_1')}}?qkey=1&company_grade=2" target="_self" class="current"
                >会员服务</a
                >
                <ul>
                    <li><a href="{{url('web/company/0_0_20_1')}}?qkey=1&company_grade=2" target="_self">会员单位</a></li>
                    <li><a href="{{url('web/company/0_0_20_1')}}?qkey=1&company_grade=4" target="_self">理事单位</a></li>

                    <li>
                        <a href="{{url('web/company/0_0_20_1')}}?qkey=1&company_grade=8" target="_self">常务理事单位</a>
                    </li>
                    <li><a href="{{url('web/company/0_0_20_1')}}?qkey=1&company_grade=16" target="_self">理事长单位</a></li>
                </ul>
            </li>
            <li>
                <a href="http://www.sxsrzrk.com/index.php/zlxz.html" target="_self" class="hide">资料下载</a>
            </li>
        </ul>
    </div>
</div>
