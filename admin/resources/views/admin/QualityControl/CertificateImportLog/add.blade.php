

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
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr>
                <th>导入批次<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="import_no" value="{{ $info['import_no'] ?? '' }}" placeholder="请输入导入批次"/>
                </td>
            </tr>
            <tr>
                <th>导入时间<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wlong import_time" name="import_time" value="{{ $info['import_time'] ?? '' }}" placeholder="请选择导入时间" style="width: 150px;"  readonly="true"/>

                </td>
            </tr>
            <tr>
                <th>内容<span class="must">*</span></th>
                <td>
                    <textarea name="import_content" placeholder="请输入内容" class="layui-textarea">{{ replace_enter_char($info['import_content'] ?? '',2) }}</textarea>
                </td>
            </tr>
            <tr>
                <th>成功导入数<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="success_num" value="{{ $info['success_num'] ?? '' }}" placeholder="请输入成功导入数"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>
            <tr>
                <th>失败导入数<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="fail_num" value="{{ $info['fail_num'] ?? '' }}" placeholder="请输入失败导入数"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
</div>
<script type="text/javascript" src="{{asset('laydate/laydate.js')}}"></script>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/certificate_import_log/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/certificate_import_log')}}";//保存成功后跳转到的地址

    var BEGIN_TIME = "{{ $info['import_time'] ?? '' }}" ;//批准日期

</script>
<script src="{{ asset('/js/admin/QualityControl/CertificateImportLog_edit.js') }}"  type="text/javascript"></script>
</body>
</html>
