

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
<div class="mm">
        <table class="table1">
            <tr>
                <th>导入批次<span class="must">*</span></th>
                <td>
                    {{ $info['import_no'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>导入时间<span class="must">*</span></th>
                <td>
                    {{ $info['import_time'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>内容<span class="must">*</span></th>
                <td>
                    <textarea name="import_content" readonly placeholder="请输入内容" class="layui-textarea">{{ replace_enter_char($info['import_content'] ?? '',2) }}</textarea>
{{--                    {!! $info['import_content'] ?? '' !!}--}}
                </td>
            </tr>
            <tr>
                <th>成功导入数<span class="must">*</span></th>
                <td>
                    {{ $info['success_num'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>失败导入数<span class="must">*</span></th>
                <td>
                    {{ $info['fail_num'] ?? '' }}
                </td>
            </tr>

        </table>
</div>
<script type="text/javascript" src="{{asset('laydate/laydate.js')}}"></script>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

</body>
</html>
