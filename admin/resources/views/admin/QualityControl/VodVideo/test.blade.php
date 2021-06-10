

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
    <script type="text/javascript" src="{{asset('dist/lib/mediaplay/ckplayer/ckplayer.js') }}" charset="utf-8" data-name="ckplayer"></script>{{--视频播放--}}
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
{{--    <form class="am-form am-form-horizontal" method="post"  id="addForm"  onsubmit="return false;">--}}

{{--    </form>--}}
    <div class="video" style="width: 600px;height: 400px;">播放器容器</div>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlVodVideoInfo";

    var SAVE_URL = "{{ url('api/admin/vod_video/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/vod_video')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业

    //定义一个变量：videoObject，用来做为视频初始化配置
    var videoObject = {
        container: '.video', //“#”代表容器的ID，“.”或“”代表容器的class
        variable: 'player', //播放函数名称，该属性必需设置，值等于下面的new ckplayer()的对象
        video: 'http://qualitycontrol.admin.cunwo.net/test/m3u8/222/index.m3u8',
        // video: 'http://qualitycontrol.admin.cunwo.net/test/mp4/111.mp4',
        // 注意: .m3u8的地址不能用 encodeURIComponent转码
        // video: encodeURIComponent('http://qualitycontrol.admin.cunwo.net/test/m3u8/222/index.m3u8'),// 'http://ckplayer-video.oss-cn-shanghai.aliyuncs.com/sample-mp4/05cacb4e02f9d9e.mp4'//视频地址
        // video: encodeURIComponent('http://qualitycontrol.admin.cunwo.net/test/mp4/111.mp4'),//
        unescape: true,//默认flashplayer里需要解码
    };
    // 合并对象
    objAppendProps(videoObject, {
        // volume: 0.8, //音量，范围：0-1 ; ---可以用到
        // poster: 'poster.png', //封面图片 ; ---可以用到
        autoplay: true,// false,//是否自动播放 ; ---可以用到
        // loop: false,//是否需要循环播放 ; ---可以用到
        // live: false,//是否是直播 ; ---可以用到
        // duration: 0,//指定总时间
        // forceduration:0,//强制使用该时间为总时间
        // seek: 0,//默认需要跳转的秒数 ; ---可以用到
          drag: 'start',//拖动时支持的前置参数
          loaded: 'loadedHandler',//加载播放器后调用的函数 ; ---可以用到
          flashplayer: false,//设置成true则强制使用flashplayer ; ---可以用到
          html5m3u8: true,// false,//PC平台上是否使用h5播放器播放m3u8 ; ---可以用到
        // track: null,//字幕轨道
        // cktrack: null,//ck字幕
        // cktrackdelay:0,//字幕显示延迟时间
        // preview: null,//预览图片对象
        // prompt: null,//提示点功能
        // type: '',//视频格式
        // crossorigin: '',//设置html5视频的crossOrigin属性
        // crossdomain: '',//安全策略文件地址
        // unescape: false,//默认flashplayer里需要解码
        // mobileCkControls: false,//移动端h5显示控制栏
        // mobileAutoFull: true,//移动端是否默认全屏播放
        // playbackrate: 1,//默认倍速
        // h5container: '',//h5环境中使用自定义容器
        // debug: false,//是否开启调试模式
        // overspread:true,//是否让视频铺满播放器
        // config: '',//调用配置函数名称
        // language:'',//语言文件路径
        // style:'',//风格文件路径
        // adfront: '',//前置贴片广告列表
        // adfronttime: '',//前置贴片广告强制时间列表
        // adfrontlink: '',//前置 贴片广告链接地址列表
        // adpause: '',//暂停广告列表，只是是图片
        // adpausetime: '',//暂停广告列表每个图片播放的时间
        // adpauselink: '',//暂停广告列表的链接地址列表
        // adinsert: '',//插入广告列表
        // adinserttime: '',//插入贴片广告时间列表
        // adinsertlink: '',//插入贴片广告链接列表
        // inserttime: '',//插入贴片广告显示的时间点列表
        // adend: '',//播放结速帖片广告列表
        // adendtime: '',//播放结速帖片时间列表
        // adendlink: '',//播放结速帖片链接列表
        // advertisements: ''//可以使用单独的json文件配置广告
    }, true);
    var player = new ckplayer(videoObject);//初始化播放器
    function loadedHandler(name){
        //调用到该函数后说明播放器已加载成功，可以进行部分控制了。此时视频还没有加载完成，所以不能控制视频的播放，暂停，获取元数据等事件
        // player.videoMute();//设置静音
        // player.addListener('loadedmetadata', loadedMetaDataHandler); //监听元数据
        // player.addListener('duration', durationHandler); //监听元数据
    }
    // function loadedMetaDataHandler(name){
    //     player.videoPlay();//控制视频播放
    // }

    // function durationHandler(time,name){
    //     //监听到元数据信息
    //     // alert('视频总时间：'+time+'，播放器名称：'+name);
    // }
</script>
<script src="{{ asset('/js/admin/QualityControl/VodVideo_info.js') }}?2"  type="text/javascript"></script>
</body>
</html>
