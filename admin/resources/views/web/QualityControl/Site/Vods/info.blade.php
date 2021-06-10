
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{{ $info['vod_name'] ?? '' }}_陕西质量认证在线视频课程{{ $info['vod_name'] ?? '' }}_{{ $key_str ?? '' }}在线视频课程学习{{ $info['vod_name'] ?? '' }}</title>
    <meta name="keywords" content="{{ $info['vod_name'] ?? '' }},陕西质量认证在线视频课程{{ $info['vod_name'] ?? '' }},{{ $key_str ?? '' }}在线视频课程学习{{ $info['vod_name'] ?? '' }}" />
    <meta name="description" content="{{ $info['vod_name'] ?? '' }},陕西质量认证在线视频课程{{ $info['vod_name'] ?? '' }},{{ $key_str ?? '' }}在线视频课程学习{{ $info['vod_name'] ?? '' }}" />

    @include('web.QualityControl.layout_public.pagehead')
    @include('web.QualityControl.Site.layout_public.pagehead')

</head>
<body  class="body_article">
@include('web.QualityControl.Site.layout_public.header')

{{--@include('web.QualityControl.Site.layout_public.search')--}}
<!--主体内容 开始-->

<div class="localhost">
    <div class="wrap">
        当前位置：
        <a href="{{url('/')}}" target="_self">网站首页</a>&nbsp;&gt;
        <a href="{{url('web/vods/0_0_20_1')}}" target="_self">视频课程</a>  >
        <a href="{{url('web/vods/info/' . $info['id'])}}" target="_self">{{ $info['vod_name'] ?? '' }}</a>
    </div>
</div>
<div id="main">
    <div class="wrap box-border shipin-header">
        <div class="pic">
            @foreach ($info['resource_list'] as $k => $resource_info)
                <img src="{{ $resource_info['resource_url'] ?? '' }}" alt="{{ $resource_info['resource_name'] ?? '' }}"  title="{{ $resource_info['resource_name'] ?? '' }}"/>

            @endforeach
{{--            <img src="{{asset('staticweb/image/pic01.jpg')}}" alt="">--}}
        </div>
        <div class="spinfo">
            <div class="title">
                {{ $info['vod_name'] ?? '' }}
            </div>
            <p>{!!  $info['explain_remarks'] ?? '' !!}</p>
            <!-- <div class="tags">会员免费</div>
            <div class="price">费用：<span class="red">320</span>元</div> -->
            <div class="btn-wrap">

                @if(isset($vod_power) && $vod_power > 0 )
                <a href="{{url('web/vods/create_order/' . $info['id'])}}" class="btn">申请开通</a>
                @endif
                <a href="#videolist" class="btn">开始学习</a>
            </div>

        </div>
        <div class="c"></div>
    </div>

    <div class="k20"></div>

    <div class="wrap">
        <div class="details-main">
            <div class="tab box-border">
                <a href="#vodcontent" class="on">课程简介</a>
                <a href="#videolist">目录</a>
            </div>
            <div class="tab-item box-border"  id="vodContent">
{{--                <p>能力验证译自“proficiencytesting”。该译名最早见于国家标准GB/T15483-1999[2-3]。这一术语在我国还曾有多种称谓，诸如“能力比对检验”W、“水平测试”[5]。在我国台湾则称其为“能力试验活动”，简称“能力试验”[6]</p><p>能力验证在不同的专业领域有时还采用其他名称，如在医学临床检验领域中常称为“外部质量评价”（externalqualityassessment，EQA)。在我国相关标准中将EQA称为“室间质量评价”[7]。在国外的一些文献中，能力验证尚有其他各种名称，如“laboratoryperformancestudy”，“performancetesting”等[8]。但目前都趋向采用“proficiencytesting”这一标准化的术语[9]。</p>--}}
{{--                <p>由上述能力验证的定义可见，能力验证的运作是基于实验室间的比对。所谓实验室间比对，就是一个组织机构按照预先设定的条件，将相同或类似的样品分发给两个或多个实验室进行检测或者测量，然后将各个实验室的结果汇总，并按规定的要求进行处理、评价和说明。ISO/ffiC17043将实验室间比对的各种目的归纳如下：</p>--}}
{{--                <p>(1)评价实验室对特定检测或测量的能力，并持续监管其运行状态。</p>--}}
{{--                <p>(2)识别实验室存在的问题，并采取措施加以改进。存在的问题可能与不合适的检测或测量程序、设备校准、人员培训和管理等因素有关。</p>--}}
{{--                <p>(3)建立检测或测量方法的有效性和可比性。</p>--}}
{{--                <p>(4)增强实验室客户的信心。</p>--}}
{{--                <p>(5)识别实验室之间的差异。</p>--}}
{{--                <p>(6)根据比对的结果，帮助参加实验室提尚能力。</p>--}}
{{--                <p>(7)确认实验室声称的测量不确定度。</p>--}}
                {!! $info['vod_content'] ?? ''  !!}
            </div>
            <div class="k20"></div>
            <div class="tab-item box-border "   id="videolist">
                <div class="hd">
                    <h3>课程目录</h3>
                </div>
                <div class="bd kc-mulu" >

                    <dl>
                        @foreach ($info['vod_video'] as $k => $v)
                        <dt>
                            {!! $v['video_name_level'] ?? '' !!}
                            @if($v['is_video'] == 2)
                            <a href="{{url('web/vod_video/info/' . $v['id'])}}" target="_blank"  class="btn">学 习</a>
                            @endif
{{--                            <a href="shipin-test.html">单元测试</a>--}}
                        </dt>
                        @endforeach

                    </dl>
{{--                    <dl>--}}
{{--                        <dt>--}}
{{--                            <a href="shipin-show.html">第1章: 食品复检机构的意义和范围</a>--}}
{{--                        </dt>--}}
{{--                        <dd>--}}
{{--                            <a href="shipin-show.html">第1节: 食品复检的意义、范围</a>--}}
{{--                        </dd>--}}
{{--                        <dd>--}}
{{--                            <a href="shipin-show.html">第2节: 食品复检的要求</a>--}}
{{--                        </dd>--}}
{{--                        <dd>--}}
{{--                            <a href="shipin-show.html">第3节: 复检申请，复检机构确认</a>--}}
{{--                        </dd>--}}
{{--                    </dl>--}}
                </div>
            </div>

        </div>
        <div class="details-side">
            <!-- <div class="details-box box-border ">
                <div class="hd">
                    授课老师
                </div>
                <div class="bd">
                    <div class="teacher">
                        <div class="tx">
                            <img src="{{asset('staticweb/image/tx.jpg')}}" alt="">
                        </div>
                        <h4>柳振青</h4>
                        <p>质量认证师</p>
                    </div>
                    <div class="teacher">
                        <div class="tx">
                            <img src="{{asset('staticweb/image/tx.jpg')}}" alt="">
                        </div>
                        <h4>柳振青</h4>
                        <p>质量认证师</p>
                    </div>
                </div>
            </div>
            <div class="k20"></div> -->
{{--            <div class="details-box box-border ">--}}
{{--                <div class="hd">--}}
{{--                    最新学员--}}
{{--                </div>--}}
{{--                <div class="bd">--}}
{{--                    <div class="tx-w">--}}
{{--                        <img src="{{asset('staticweb/image/tx01.jpg')}}" alt="">--}}
{{--                    </div>--}}
{{--                    <div class="tx-w">--}}
{{--                        <img src="{{asset('staticweb/image/tx01.jpg')}}" alt="">--}}
{{--                    </div>--}}
{{--                    <div class="tx-w">--}}
{{--                        <img src="{{asset('staticweb/image/tx01.jpg')}}" alt="">--}}
{{--                    </div>--}}
{{--                    <div class="tx-w">--}}
{{--                        <img src="{{asset('staticweb/image/tx01.jpg')}}" alt="">--}}
{{--                    </div>--}}
{{--                    <div class="tx-w">--}}
{{--                        <img src="{{asset('staticweb/image/tx01.jpg')}}" alt="">--}}
{{--                    </div>--}}
{{--                    <div class="tx-w">--}}
{{--                        <img src="{{asset('staticweb/image/tx01.jpg')}}" alt="">--}}
{{--                    </div>--}}
{{--                    <div class="tx-w">--}}
{{--                        <img src="{{asset('staticweb/image/tx01.jpg')}}" alt="">--}}
{{--                    </div>--}}
{{--                    <div class="c"></div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="k20"></div>
            <div class="details-box box-border ">
                <div class="hd">
                    推荐课程
                </div>
                <div class="bd">
                    <ul class="thumblist1">

                        @foreach ($recommend_list as $k => $v)
                        <li>
                            <a href="{{url('web/vods/info/' . $v['id'])}}">
                                <div class="pic">
                                    @foreach ($v['resource_list'] as $k => $resource_info)
                                        <img src="{{ $resource_info['resource_url'] ?? '' }}" alt="{{ $resource_info['resource_name'] ?? '' }}"  title="{{ $resource_info['resource_name'] ?? '' }}"/>

                                    @endforeach
{{--                                    <img src="{{asset('staticweb/image-t/pic02.jpg')}}" alt="">--}}
                                </div>
                                <div class="title">
                                    {{ $v['vod_name'] ?? '' }}
                                </div>
                            </a>
                        </li>
                        @endforeach
{{--                        <li>--}}
{{--                            <a href="shipin-details.html">--}}
{{--                                <div class="pic">--}}
{{--                                    <img src="{{asset('staticweb/image-t/pic02.jpg')}}" alt="">--}}
{{--                                </div>--}}
{{--                                <div class="title">--}}
{{--                                    生态环境监测人员培训--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </li>--}}
                    </ul>
                </div>
            </div>




        </div>


        <div class="c"></div>
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

