
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
{{--    <title>副理事长单位|会员中心-陕西省质量认证认可协会-陕西省质量认证认可协会-陕西质量认证咨询中心</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />--}}
{{--    <meta name="author" content="陕西省质量认证认可协会-陕西质量认证咨询中心">--}}
{{--    <meta name="keywords" content="会员中心-陕西省质量认证认可协会"><meta name="description" content="陕西质量认证认可协会---官网----陕西省质量认证咨询中心-陕西省质量认证认可协会">--}}

    <title>陕西质量认证咨询中心_{{ $info['notice_name'] ?? '' }}资质认定获证机构_陕西{{ $info['notice_name'] ?? '' }}资质认定获证机构_检验检测能力</title>
    <meta name="keywords" content="陕西质量认证咨询中心,{{ $info['notice_name'] ?? '' }}资质认定获证机构,陕西{{ $info['notice_name'] ?? '' }}资质认定获证机构,检验检测能力" />
    <meta name="description" content="陕西质量认证咨询中心,{{ $info['notice_name'] ?? '' }}资质认定获证机构,陕西{{ $info['notice_name'] ?? '' }}资质认定获证机构,检验检测能力" />
    @include('web.QualityControl.Site.layout_public.pagehead')
</head>
<body  class="body_article">
@include('web.QualityControl.Site.layout_public.header')
@include('web.QualityControl.Site.layout_public.companyHeader')
{{--@include('web.QualityControl.Site.layout_public.search')--}}
<!--主体内容 开始-->

<div class="article">
    <!--左侧区域 开始-->
    <div id="left">
        <div class="sidebox subnav">
            <div class="subnavtitle hd"><h2>最新直播公告</h2></div>
            <div class="left_body">
                <ul class="subchannellist">
                    @foreach ($newest_list as $k => $tInfo)
                        <li class="depth1"  ><a href="{{url('web/live_notice/info/' . $tInfo['id'] )}}">{{ $tInfo['notice_name'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="c"></div>
        </div>
    </div>
    <div id="right">
        <div id="location_1">
            <b>当前位置：</b>
            <a href="{{url('/')}}" target="_self">网站首页</a>&nbsp;&gt;&gt;&nbsp;
            <a href="{{url('web/live_notice/20_1')}}" target="_self" >直播公告</a>&nbsp;&gt;&gt;&nbsp;
            <a href="{{url('web/live_notice/info/' . $info['id'])}}" target="_self">{{ $info['notice_name'] ?? '' }}</a>
        </div>
        <div class="right_title"><h2>直播公告</h2></div>
        <div class="right_body">
            <div class="InfoTitle"><h1>{{ $info['notice_name'] ?? '' }}</h1></div>
            <div class="info_from_wrap">
{{--                <b>来源：</b><a href="http://www.sxsrzrk.com/" target="_blank">陕西省质量认证认可协会-陕西质量认证咨询中心</a>&nbsp;--}}
                <b>日期：</b>{{ $info['created_at'] ?? '' }}&nbsp;
            </div>
            <!-- 频道/文章内容  开始-->
            <div class="InfoSContent"></div>
            <div class="InfoContent">
                {!! $info['notice_content'] ?? '' !!}
            </div>

        </div>
        <div class="c"></div>
    </div>

    <div class="c"></div>
<!--主体内容 结束-->
</div>

<!--主体内容 结束-->

@include('web.QualityControl.Site.layout_public.footer')

</body>
</html>

<script type="text/javascript">
    // var SEARCH_COMPANY_URL = "{ {url('web/certificate/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
    var SEARCH_COMPANY_URL = "{{url('web/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址

</script>
{{--<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>--}}
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

{{--<script src="{{ asset('/js/web/QualityControl/CertificateSchedule/search.js') }}?2"  type="text/javascript"></script>--}}

