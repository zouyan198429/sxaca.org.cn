
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>陕西质量认证在线视频课程_{{ $key_str ?? '' }}在线视频课程学习_陕西{{ $key_str ?? '' }}在线视频课程学习第{{ $page ?? '' }}页_认证视频课程</title>
    <meta name="keywords" content="陕西质量认证在线视频课程,{{ $key_str ?? '' }}在线视频课程学习,陕西{{ $key_str ?? '' }}在线视频课程学习第{{ $page ?? '' }}页,认证视频课程" />
    <meta name="description" content="陕西质量认证在线视频课程,{{ $key_str ?? '' }}在线视频课程学习,陕西{{ $key_str ?? '' }}在线视频课程学习第{{ $page ?? '' }}页,认证视频课程" />
    @include('web.QualityControl.layout_public.pagehead')
    @include('web.QualityControl.Site.layout_public.pagehead')
</head>
<body  class="body_article">
@include('web.QualityControl.Site.layout_public.header')

{{--@include('web.QualityControl.Site.layout_public.search')--}}
<!--主体内容 开始-->

{{--<div class="subbanner">--}}
{{--    <div class="wrap">--}}
{{--        <h2>视频课程</h2>--}}
{{--        <div class="dvsearch">--}}
{{--            <input name="Keywords" placeholder="请输入关键词" class="btn-keywords" type="text" value="" maxlength="50">--}}
{{--            <input class="btn-search" name="submit" type="submit" value="搜索">--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<div class="localhost">
    <div class="wrap">
        当前位置：
        <a href="{{url('/')}}" target="_self">网站首页</a>&nbsp;&gt;
        <a href="{{url('web/vods/0_0_' . $pagesize . '_1')}}" target="_self">视频课程</a>&nbsp;&gt;
        <a href="{{url('web/vod_video/0_0_' . $pagesize . '_1')}}" target="_self">视频课件</a>
        @if(isset($vod_id) &&  $vod_id > 0)
            &nbsp;&gt;<a href="{{url('web/vods/' . $vod_id . '_0_20_1')}}" target="_self">{{ $vod_name ?? '' }}</a>
        @endif
    </div>
</div>
{{--<div class="subnav-v">--}}
{{--    <div class="wrap">--}}
{{--        <ul class="subnav-inline">--}}
{{--            <li>--}}
{{--                <a href="{{url('web/vods/0_0_' . $pagesize . '_1')}}" @if(isset($defaultVodType) && $defaultVodType == 0) class="on" @endif>全部</a>--}}
{{--            </li>--}}
{{--            @foreach ($vod_type_kv as $k=>$txt)--}}
{{--            <li>--}}
{{--                <a href="{{url('web/vods/' . $k . '_0_' . $pagesize . '_1')}}" @if(isset($defaultVodType) && $defaultVodType == $k) class="on" @endif>{{ $txt }}</a>--}}
{{--            </li>--}}
{{--            @endforeach--}}
{{--            <div class="c"></div>--}}
{{--        </ul>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<hr>--}}
<div class="k20"></div>
<div id="main">
    <div class="wrap">
{{--        <div class="taggroup">--}}
{{--            <a href="{{url('web/vods/' . $defaultVodType . '_0_' . $pagesize . '_1')}}" @if(isset($defaultRecommendStatus) && $defaultRecommendStatus == 0) class="on" @endif>全部</a>--}}
{{--            @foreach ($recommendStatus as $k=>$txt)--}}
{{--            <a href="{{url('web/vods/' . $defaultVodType . '_' . $k . '_' . $pagesize . '_1')}}"  @if(isset($defaultRecommendStatus) && $defaultRecommendStatus == $k) class="on" @endif>{{ $txt }}</a>--}}
{{--            @endforeach--}}
{{--        </div>--}}
        <div class="bd">

            <ul class="gridlist">
                @foreach ($data_list as $k => $v)
                <li>
                    <a href="{{url('web/vod_video/info/' . $v['id'])}}" target="_blank">

                        @foreach ($v['resource_list'] as $k => $resource_info)
                                <img src="{{ $resource_info['resource_url'] ?? '' }}" alt="{{ $resource_info['resource_name'] ?? '' }}"  title="{{ $resource_info['resource_name'] ?? '' }}"/>

                        @endforeach
{{--                        <img src="{{asset('staticweb/image/pic01.jpg')}}" alt="">--}}
                        <div class="title">
                            {{ $v['video_name'] ?? '' }}
                        </div>
                    </a>
                </li>
                @endforeach
{{--                <li>--}}
{{--                    <a href="shipin-details.html">--}}
{{--                        <img src="{{asset('staticweb/image/pic01.jpg')}}" alt="">--}}
{{--                        <div class="title">--}}
{{--                            检验检测机构年度监督检查专题技术讲座--}}
{{--                        </div>--}}
{{--                    </a>--}}
{{--                </li>--}}
                <div class="c"></div>
            </ul>
        </div>
        <div class="mmfoot"><!--
                        <div class="mmfleft"></div> -->
            <div class="pagination">
                {!! $pageInfoLink ?? ''  !!}
            </div>
        </div>
{{--        <div class="pages">--}}
{{--            <a href="#1"><</a>--}}
{{--            <a href="#1" class="on">1</a>--}}
{{--            <a href="#1">2</a>--}}
{{--            <a href="#1">3</a>--}}
{{--            <a href="#1">4</a>--}}
{{--            <a href="#1">5</a>--}}
{{--            <a href="#1">6</a>--}}
{{--            <a href="#0">7</a>--}}
{{--            <a href="#1">></a>--}}
{{--        </div>--}}

    </div>
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

