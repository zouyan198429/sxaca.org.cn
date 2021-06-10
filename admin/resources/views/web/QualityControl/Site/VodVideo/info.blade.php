
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{{ $info['video_name'] ?? '' }}_陕西质量认证在线视频课程{{ $info['video_name'] ?? '' }}_{{ $key_str ?? '' }}在线视频课程学习{{ $info['video_name'] ?? '' }}</title>
    <meta name="keywords" content="{{ $info['video_name'] ?? '' }},陕西质量认证在线视频课程{{ $info['video_name'] ?? '' }},{{ $key_str ?? '' }}在线视频课程学习{{ $info['video_name'] ?? '' }}" />
    <meta name="description" content="{{ $info['video_name'] ?? '' }},陕西质量认证在线视频课程{{ $info['video_name'] ?? '' }},{{ $key_str ?? '' }}在线视频课程学习{{ $info['video_name'] ?? '' }}" />
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('web.QualityControl.layout_public.pagehead')
    @include('web.QualityControl.Site.layout_public.pagehead')
    <script type="text/javascript" src="{{asset('dist/lib/mediaplay/ckplayer/ckplayer.js') }}" charset="utf-8" data-name="ckplayer"></script>{{--视频播放--}}
</head>
<body  class="body_article">
@include('web.QualityControl.Site.layout_public.header')

{{--@include('web.QualityControl.Site.layout_public.search')--}}
<!--主体内容 开始-->

<div class="show-header">
    <div class="wrap">
        <div class="tl"  style="color:#fff;">
            当前位置：
            <a href="{{url('/')}}" target="_self"  style="color:#fff;">网站首页</a>  >
            <a href="{{url('web/vods/0_0_20_1')}}" target="_self" style="color:#fff;">视频课程</a>  >
            <a href="{{url('web/vod_video/' . ($info['vod_id'] ?? 0) . '_0_20_1')}}" target="_self" style="color:#fff;">{{ $info['vod_name'] ?? '' }}</a>  >
            <a href="{{url('web/vod_video/info/' . $info['id'])}}" target="_self"  style="color:#fff;">{{ $info['video_name'] ?? '' }}</a>
        </div>
        <h1 style="color:#fff; padding:20px 0;">{{ $info['video_name'] ?? '' }}</h1>
        <div class="dv">
{{--            <img src="{{asset('staticweb/image-t/dv.jpg')}}" alt="">--}}
            <div class="video" style="width: 600px;height: 400px;">播放器容器</div>

        </div>
    </div>

</div>

<div id="main">
{{--    <div class="wrap show-info">--}}
{{--        <div class="sptinfo">--}}
{{--            <div class="tags-group">--}}
{{--                <li>--}}
{{--                    <i>点赞</i>--}}
{{--                    <span>32</span>--}}
{{--                </li>--}}
{{--                <li>--}}
{{--                    <i>浏览量</i>--}}
{{--                    <span>456</span>--}}
{{--                </li>--}}
{{--                <li>--}}
{{--                    <i></i>--}}
{{--                    <span>二维码</span>--}}
{{--                </li>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="c"></div>--}}
{{--    </div>--}}


    <div class="wrap">
{{--        <div class="details-main box-border pinglun">--}}
{{--            <div class="hd">--}}
{{--                <h3>评论(2)</h3>--}}
{{--            </div>--}}
{{--            <div class="bd tc slogin">--}}
{{--                <p>你还没有登录，请先<a href="">登录</a>或<a href="">注册</a>！</p>--}}
{{--            </div>--}}
{{--            <div class="k20"></div>--}}

{{--            <!-- <ul class="comment-list thread-pripost-list">--}}
{{--                <li id="post-29" data-user-id="27375" class="thread-post thread-post-29 media media-comment user-id-27375">--}}
{{--                <div class="media-left">--}}
{{--                    <a class="user-avatar js-user-card" href="/user/27375" data-card-url="/user/27375/card/show" data-user-id="27375">--}}
{{--                    <img class="avatar-sm" src="http://sce5a1b9c8d0t4-sb-qn.qiqiuyun.net/files/default/2020/03-03/092839776c10539691.jpg">--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--                <div class="media-body">--}}
{{--                    <div class="metas title">--}}
{{--                        <a href="/user/27375" class="nickname">王佳冰</a>--}}
{{--                        <span class="bullet">•</span>--}}
{{--                        <span class="color-gray">03-07 </span>--}}
{{--                    </div>--}}
{{--                    <div class="editor-text">--}}
{{--                        <p>--}}
{{--                            老师讲的好--}}
{{--                        </p>--}}
{{--                    </div>--}}
{{--                    <div class="comment-sns">--}}
{{--                        <div class="thread-post-interaction">--}}
{{--                            <a href="javascript:;" class="js-post-up interaction color-gray" data-url="/thread/0/post/29/up">--}}
{{--                            <span class="glyphicon glyphicon-thumbs-up"></span> (<span class="post-up-num">0</span>)--}}
{{--                            </a>--}}
{{--                            <a href="javascript:;" class="js-reply interaction color-gray"><span class="glyphicon glyphicon-comment hide"></span>--}}
{{--                回复--}}
{{--                            <span class="subposts-num-wrap hide">(<span class="subposts-num">0</span>)</span>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="thread-subpost-container subcomments clearfix hide">--}}
{{--                        <div class="thread-subpost-content">--}}
{{--                            <ul class="media-list thread-post-list thread-subpost-list">--}}
{{--                            </ul>--}}
{{--                            <div class="text-center">--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="thread-subpost-morebar clearfix hide">--}}
{{--                            <span class="thread-subpost-moretext hide"><span class="color-gray">还有-5条回复，</span><a href="javascript:;" class="js-post-more">点击查看</a></span>--}}
{{--                        </div>--}}
{{--                        <div class="empty">--}}
{{--                                          你还没有登录，请先--}}
{{--                            <a href="/login?goto=/open/course/14">登录</a>或<a href="/register?goto=/open/course/14">注册</a>！--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                </li>--}}

{{--            </ul> -->--}}
{{--            <div class="bd tc">--}}
{{--                <img src="{{asset('staticweb/image-t/pinglun.jpg')}}" alt="">--}}
{{--                <img src="{{asset('staticweb/image-t/pinglun.jpg')}}" alt="">--}}
{{--            </div>--}}

{{--        </div>--}}

        <div class="details-side">
            <div class="details-box box-border resource_list_courseware">
                <div class="hd">
                    课件资料
                </div>
                <div class="bd baguetteBoxOne gallery">
                    <span class="resource_list"  style="display: none;">@json($info['resource_list_courseware'] ?? [])</span>
                    <span  class="resource_show_courseware"></span>
{{--                    <div class="teacher">--}}
{{--                        <div class="tx">--}}
{{--                            <img src="{{asset('staticweb/image/tx.jpg')}}" alt="">--}}
{{--                        </div>--}}
{{--                        <h4>柳振青</h4>--}}
{{--                        <p>质量认证师</p>--}}
{{--                    </div>--}}

                </div>
            </div>
            <div class="k20"></div>

        </div>


        <div class="c"></div>
    </div>


</div>
<!--主体内容 结束-->

{{--<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>--}}
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>--}}
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('web.QualityControl.Site.layout_public.footer')

{{--<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>--}}
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')
</body>
</html>

<script type="text/javascript">
    // var SEARCH_COMPANY_URL = "{ {url('web/certificate/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
    var SEARCH_COMPANY_URL = "{{url('web/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址

    var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载
    var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

    $(function(){

    });

    function loadedHandler(name){
        //调用到该函数后说明播放器已加载成功，可以进行部分控制了。此时视频还没有加载完成，所以不能控制视频的播放，暂停，获取元数据等事件
        // player.videoMute();//设置静音
        // player.addListener('loadedmetadata', loadedMetaDataHandler); //监听元数据
        // player.addListener('duration', durationHandler); //监听元数据
        console.log('==loadedHandler===' + name);
    }
    // function loadedMetaDataHandler(name){
    //     player.videoPlay();//控制视频播放
    // }

    // function durationHandler(time,name){
    //     //监听到元数据信息
    //     // alert('视频总时间：'+time+'，播放器名称：'+name);
    // }
    window.onload = function() {
        // $('.search_frm').trigger("click");// 触发搜索事件
        // reset_list_self(false, false, true, 2);
        // 初始化列表文件显示功能--附件资料
        var uploadCoursewareAttrObj = {
            down_url:DOWN_FILE_URL,
            del_url: DEL_FILE_URL,
            del_fun_pre:'',
            files_type: 1,
            icon : 'file-o',
            operate_auth:(2)// (1 | 2)
        };
        var resourceCoursewareListObj = $('.resource_list_courseware');// $('#data_list').find('tr');
        initFileShow(uploadCoursewareAttrObj, resourceCoursewareListObj, 'resource_show_courseware', 'baidu_template_upload_file_show', 'baidu_template_upload_pic', 'resource_id_courseware[]');

         initPic();
        //定义一个变量：videoObject，用来做为视频初始化配置
        var videoObject = {
            container: '.video', //“#”代表容器的ID，“.”或“”代表容器的class
            variable: 'player', //播放函数名称，该属性必需设置，值等于下面的new ckplayer()的对象
            video: '{{ $video_url ?? '' }}',// '{{ $info["resource_list_video"][0]["resource_url"] ?? "" }}',// 'http://qualitycontrol.admin.cunwo.net/test/m3u8/222/index.m3u8',
            // video: 'http://qualitycontrol.admin.cunwo.net/test/mp4/111.mp4',
            // 注意: .m3u8的地址不能用 encodeURIComponent转码
            // video: encodeURIComponent('http://qualitycontrol.admin.cunwo.net/test/m3u8/222/index.m3u8'),// 'http://ckplayer-video.oss-cn-shanghai.aliyuncs.com/sample-mp4/05cacb4e02f9d9e.mp4'//视频地址
            // video: encodeURIComponent('http://qualitycontrol.admin.cunwo.net/test/mp4/111.mp4'),//
            // unescape: true,//默认flashplayer里需要解码
            drag: 'start',//拖动时支持的前置参数
            loaded: 'loadedHandler',//加载播放器后调用的函数 ; ---可以用到
            flashplayer: false,//设置成true则强制使用flashplayer ; ---可以用到
            html5m3u8: true,// false,//PC平台上是否使用h5播放器播放m3u8 ; ---可以用到
            autoplay: true,// false,//是否自动播放 ; ---可以用到
        };
        // 合并对象
        // objAppendProps(videoObject, {
        //     // volume: 0.8, //音量，范围：0-1 ; ---可以用到
        //     // poster: 'poster.png', //封面图片 ; ---可以用到
        //     // autoplay: false,//是否自动播放 ; ---可以用到
        //     // loop: false,//是否需要循环播放 ; ---可以用到
        //     // live: false,//是否是直播 ; ---可以用到
        //     // duration: 0,//指定总时间
        //     // forceduration:0,//强制使用该时间为总时间
        //     // seek: 0,//默认需要跳转的秒数 ; ---可以用到
        //     drag: 'start',//拖动时支持的前置参数
        //     loaded: 'loadedHandler',//加载播放器后调用的函数 ; ---可以用到
        //     flashplayer: false,//设置成true则强制使用flashplayer ; ---可以用到
        //     html5m3u8: true,// false,//PC平台上是否使用h5播放器播放m3u8 ; ---可以用到
        //     // track: null,//字幕轨道
        //     // cktrack: null,//ck字幕
        //     // cktrackdelay:0,//字幕显示延迟时间
        //     // preview: null,//预览图片对象
        //     // prompt: null,//提示点功能
        //     // type: '',//视频格式
        //     // crossorigin: '',//设置html5视频的crossOrigin属性
        //     // crossdomain: '',//安全策略文件地址
        //     // unescape: false,//默认flashplayer里需要解码
        //     // mobileCkControls: false,//移动端h5显示控制栏
        //     // mobileAutoFull: true,//移动端是否默认全屏播放
        //     // playbackrate: 1,//默认倍速
        //     // h5container: '',//h5环境中使用自定义容器
        //     // debug: false,//是否开启调试模式
        //     // overspread:true,//是否让视频铺满播放器
        //     // config: '',//调用配置函数名称
        //     // language:'',//语言文件路径
        //     // style:'',//风格文件路径
        //     // adfront: '',//前置贴片广告列表
        //     // adfronttime: '',//前置贴片广告强制时间列表
        //     // adfrontlink: '',//前置 贴片广告链接地址列表
        //     // adpause: '',//暂停广告列表，只是是图片
        //     // adpausetime: '',//暂停广告列表每个图片播放的时间
        //     // adpauselink: '',//暂停广告列表的链接地址列表
        //     // adinsert: '',//插入广告列表
        //     // adinserttime: '',//插入贴片广告时间列表
        //     // adinsertlink: '',//插入贴片广告链接列表
        //     // inserttime: '',//插入贴片广告显示的时间点列表
        //     // adend: '',//播放结速帖片广告列表
        //     // adendtime: '',//播放结速帖片时间列表
        //     // adendlink: '',//播放结速帖片链接列表
        //     // advertisements: ''//可以使用单独的json文件配置广告
        // }, true);
       var player = new ckplayer(videoObject);//初始化播放器
    };
    function initPic(){
        baguetteBox.run('.baguetteBoxOne');
        // baguetteBox.run('.baguetteBoxTwo');
    }
</script>

<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>

@component('component.upfileincludejsmany')
@endcomponent
{{--<script src="{{ asset('/js/web/QualityControl/CertificateSchedule/search.js') }}?2"  type="text/javascript"></script>--}}

