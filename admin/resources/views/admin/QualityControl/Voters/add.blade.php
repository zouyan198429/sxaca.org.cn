

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
                <th>组序号<span class="must">*</span></th>
                <td>
                    @foreach ($groupType as $k=>$txt)
                        <label><input type="radio"  name="group_type"  value="{{ $k }}"  @if(isset($defaultGroupType) && $defaultGroupType == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>组号<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="group_no" value="{{ $info['group_no'] ?? '' }}" placeholder="请输入组号"/>
                </td>
            </tr>
            <tr>
                <th>姓名<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="real_name" value="{{ $info['real_name'] ?? '' }}" placeholder="请输入姓名"/>
                </td>
            </tr>
            <tr>
                <th>性别<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="sex_name" value="{{ $info['sex_name'] ?? '' }}" placeholder="请输入性别"/>
                </td>
            </tr>
            <tr>
                <th>身份证号<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="id_number" value="{{ $info['id_number'] ?? '' }}" placeholder="请输入身份证号"/>
                </td>
            </tr>
            <tr>
                <th>出生日期<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="birth_date" value="{{ $info['birth_date'] ?? '' }}" placeholder="请输入出生日期"/>
                </td>
            </tr>
            <tr>
                <th>与户主关系<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="relation_name" value="{{ $info['relation_name'] ?? '' }}" placeholder="请输入与户主关系"/>
                </td>
            </tr>
            <tr>
                <th>政治面貌<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="politics_name" value="{{ $info['politics_name'] ?? '' }}" placeholder="请输入政治面貌"/>
                </td>
            </tr>
            <tr>
                <th>家庭住址<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="addr" value="{{ $info['addr'] ?? '' }}" placeholder="请输入家庭住址"/>
                </td>
            </tr>
            <tr>
                <th>联系方式<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="contact" value="{{ $info['contact'] ?? '' }}" placeholder="请输入联系方式"/>
                </td>
            </tr>
            <tr>
                <th>备注<span class="must"></span></th>
                <td>
                    <textarea name="remarks" placeholder="请输入备注" class="layui-textarea">{{ replace_enter_char($info['remarks'] ?? '',2) }}</textarea>

                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlVotersedit";

    var SAVE_URL = "{{ url('api/admin/voters/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/voters')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/admin/QualityControl/Voters_edit.js') }}"  type="text/javascript"></script>
</body>
</html>
