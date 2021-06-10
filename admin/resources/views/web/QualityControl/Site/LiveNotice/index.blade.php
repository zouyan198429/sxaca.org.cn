
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
{{--    <title>副理事长单位|会员中心-陕西省质量认证认可协会-陕西省质量认证认可协会-陕西质量认证咨询中心视频直播公告</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />--}}
{{--    <meta name="author" content="陕西省质量认证认可协会-陕西质量认证咨询中心视频直播公告">--}}
{{--    <meta name="keywords" content="会员中心-陕西省质量认证认可协会"><meta name="description" content="陕西质量认证认可协会---官网----陕西省质量认证咨询中心-陕西省质量认证认可协会">--}}

    <title>陕西质量认证咨询中心视频直播公告_{{ $key_str ?? '' }}资质认定获证机构视频直播公告_陕西{{ $key_str ?? '' }}资质认定获证机构视频直播公告第{{ $page ?? '' }}页_检验检测能力</title>
    <meta name="keywords" content="陕西质量认证咨询中心视频直播公告,{{ $key_str ?? '' }}资质认定获证机构视频直播公告,陕西{{ $key_str ?? '' }}资质认定获证机构视频直播公告第{{ $page ?? '' }}页,检验检测能力" />
    <meta name="description" content="陕西质量认证咨询中心视频直播公告,{{ $key_str ?? '' }}资质认定获证机构视频直播公告,陕西{{ $key_str ?? '' }}资质认定获证机构视频直播公告第{{ $page ?? '' }}页,检验检测能力" />
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
    <!--左侧区域 结束--><!--右侧区域 开始-->
    <div id="right">
        <div class="right_title"><h2>直播公告</h2></div>
        <div class="right_body">
            <ul class="textlist">
                <!--循环开始-->
                @foreach ($data_list as $k => $v)
                <li>
                    <a href="{{url('web/live_notice/info/' . $v['id'])}}" target="_blank" title="" >{{ $v['notice_name'] ?? '' }}</a>
                </li>
                @endforeach
                <!--循环结束-->
            </ul>
            <div class="mmfoot"><!--
                        <div class="mmfleft"></div> -->
                <div class="pagination">
                    {!! $pageInfoLink ?? ''  !!}
                </div>
            </div>
{{--            <div class="page">--}}
{{--                { !! $pageInfoLink ?? ''  !!}--}}
{{--            </div>--}}
        </div>
        <div class="right_bottom"></div>
    </div>
    <!--右侧区域 结束-->
    <div class="clear"></div>
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

