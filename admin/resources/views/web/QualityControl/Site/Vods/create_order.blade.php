
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
    <div class="wrap peixun" style="width:980px;  min-height:600px; margin:40px auto 20px auto; border:1px solid #eee;  padding:20px 40px; background:#fff;  ">
        <div class="hd">
            <h3 class="tl">面授报名</h3>
        </div>
        <form class="am-form am-form-horizontal" method="post"  id="addForm">
            <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
            <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <div class="bd">
            <table class="table table-kc">
                <tr>
                    <th>培训课程：</th><td>{{ $info['vod_name'] ?? '' }}</td>
                </tr>
                <tr>
                    <th>当前用户：</th><td>{{ $show_name ?? '' }}</td>
                </tr>
                <tr>
                    <th>联系人：</th>
                    <td>
                        <input type="text" class="inp wnormal"  name="contacts" value="{{ $info['contacts'] ?? '' }}" placeholder="请输入联系人"/>
                    </td>
                </tr>
                <tr>
                    <th>联系电话：</th>
                    <td>
                        <input type="text" class="inp wnormal"  name="tel" value="{{ $info['tel'] ?? '' }}" placeholder="请输入联系电话"/>
                    </td>
                </tr>
            </table>
        </div>
        <div class="k20"></div>
        <div class="box-wrap tc">
            <button class="btn btn-l wnormal"  id="submitBtn" >购买</button>
        </div>
        </form>

    </div>
</div>



<div class="c"></div>
<!--主体内容 结束-->

@include('web.QualityControl.Site.layout_public.footer')

</body>
</html>

<script type="text/javascript">
    // var SEARCH_COMPANY_URL = "{ {url('web/certificate/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
    var SEARCH_COMPANY_URL = "{{url('web/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "webQualityControlSiterrrddddedit";

    var SAVE_URL = "{{ url('api/web/vods/ajax_join_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('web/vods/info/' . ($info['id'] ?? 0))}}";//"{ {url('web/vods')}}";//保存成功后跳转到的地址

    var PAY_URL = "{{ url('web/vod_orders/pay') }}";//操作(缴费)
</script>
{{--<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>--}}
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script src="{{ asset('/js/web/QualityControl/Site/Vods_create_order.js') }}?3"  type="text/javascript"></script>
{{--<script src="{{ asset('/js/web/QualityControl/CertificateSchedule/search.js') }}?2"  type="text/javascript"></script>--}}

