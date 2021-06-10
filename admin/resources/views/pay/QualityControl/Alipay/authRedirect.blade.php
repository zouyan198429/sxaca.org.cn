

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
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}

<div class="page page-error text-center" style="text-align: center;">
    <header>
        <h1 style="font-size: 300%; color:#0c5460;">授权成功</h1>
        <p>{{ $info['pay_company_name'] ?? '' }}-支付宝相关授权成功</p>
    </header>
    <br/>
    <hr/>
    <a class="btn btn-primary btn-round" href="{{ url('/') }}" style="margin-right:10px;">回到首页</a>
{{--    <a class="btn btn-round" href="javascript:window.close();;">关闭页面</a>--}}
{{--    <footer class="page-copyright">--}}
{{--        <p>WEBSITE BY 莫非</p>--}}
{{--        <p>© 2018. All RIGHT RESERVED.</p>--}}
{{--    </footer>--}}
</div>
{{--<div class="mm">--}}
{{--        <table class="table1">--}}
{{--            <tr>--}}
{{--                <th></th>--}}
{{--                <td>--}}
{{--                    授权成功--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--        </table>--}}
{{--</div>--}}
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "payQualityControlalipayauthRedirect";

    var SAVE_URL = "{{ url('api/pay/alipay/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('pay/alipay')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/pay/QualityControl/Alipay_authRedirect.js') }}"  type="text/javascript"></script>
</body>
</html>
