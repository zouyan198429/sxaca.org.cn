

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
            <tr  @if (isset($hidden_option) && (($hidden_option & 2) == 2) ) style="display: none;"  @endif>
                <th>所属课程<span class="must"></span></th>
                <td>
                    @foreach ($course_id_kv as $k=>$txt)
                        <label><input type="radio"  name="course_id"  value="{{ $k }}"  @if(isset($defaultCourseId) && $defaultCourseId == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>班级名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="class_name" value="{{ $info['class_name'] ?? '' }}" placeholder="请输入班级名称"/>
                </td>
            </tr>
            <tr>
                <th>开班城市<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="city_id" style="width: 100px;">
                        <option value="">请选择城市</option>
                        @foreach ($citys_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCity) && $defaultCity == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>备注<span class="must"></span></th>
                <td>
                    <textarea name="remarks" placeholder="请输入备注" class="layui-textarea">{{ replace_enter_char($info['remarks'] ?? '',2) }}</textarea>

                </td>
            </tr>
            <tr>
                <th>收款帐号<span class="must"></span></th>
                <td>
                    @foreach ($pay_config_kv as $k=>$txt)
                        <label><input type="radio"  name="pay_config_id"  value="{{ $k }}"  @if(isset($defaultPayConfig) && $defaultPayConfig == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                    <p>
                        不设置，则使用课程设置收款账号信息！
                    </p>
                </td>
            </tr>
            <tr>
                <th>收款开通类型<span class="must"></span></th>
                <td class="sel_pay_method">
                    @foreach ($payMethod as $k=>$txt)
                        <label><input type="checkbox"  name="pay_method[]"  value="{{ $k }}"  @if(isset($defaultPayMethod) && $defaultPayMethod > 0 && ($defaultPayMethod & $k) == $k) checked="checked"  @endif @if(isset($info['allow_pay_method']) && ($info['allow_pay_method'] & $k) <=0 ) disabled   @endif/>{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>状态<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    @foreach ($classStatus as $k=>$txt)--}}
{{--                        <label><input type="radio"  name="class_status"  value="{{ $k }}"  @if(isset($defaultClassStatus) && $defaultClassStatus == $k) checked="checked"  @endif />{{ $txt }} </label>--}}
{{--                    @endforeach--}}
{{--                </td>--}}
{{--            </tr>--}}
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
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/course_class/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/course_class')}}";//保存成功后跳转到的地址

    var PAY_CONFIG_INFO_URL = "{{ url('api/admin/order_pay_config/ajax_info') }}";// ajax获得支付方式详情记录地址
</script>
<script src="{{ asset('/js/admin/QualityControl/CourseClass_edit.js') }}?4"  type="text/javascript"></script>
</body>
</html>
