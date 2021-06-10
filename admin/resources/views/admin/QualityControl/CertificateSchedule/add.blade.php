

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
            <tr  @if (isset($company_hidden) && $company_hidden == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must">*</span></th>
                <td>
                    <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                    <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                    <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择所属企业</button>
                </td>
            </tr>
            <tr>
                <th>CMA证书号<span class="must">*</span></th>
                <td>
                    {{ $info['certificate_no'] ?? '' }}
{{--                    <input type="text" class="inp wnormal"  name="certificate_no" value="{{ $info['certificate_no'] ?? '' }}" placeholder="请输入CMA证书号"/>--}}
                </td>
            </tr>
            <tr>
                <th>有效起止时间<span class="must">*</span></th>
                <td>
{{--                    {{ $info['ratify_date'] ?? '' }} - {{ $info['valid_date'] ?? '' }}--}}
                    <input type="text" class="inp wlong ratify_date" name="ratify_date" value="{{ $info['ratify_date'] ?? '' }}" placeholder="请选择批准日期" style="width: 150px;"/> <!--  readonly="true"-->
                    -
                    <input type="text" class="inp wlong valid_date" name="valid_date" value="{{ $info['valid_date'] ?? '' }}" placeholder="请选择有效期至"  style="width: 150px;"/><!--  readonly="true"-->
                </td>
            </tr>
            <tr>
                <th>实验室地址<span class="must">*</span></th>
                <td>
                    {{ $info['addr'] ?? '' }}
{{--                    <input type="text" class="inp wnormal"  name="addr" value="{{ $info['addr'] ?? '' }}" placeholder="请输入实验室地址"/>--}}
                </td>
            </tr>
            <tr>
                <th>一级名称<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="category_name" value="{{ $info['category_name'] ?? '' }}" placeholder="请输入一级名称"/>
                </td>
            </tr>
            <tr>
                <th>二级名称<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="project_name" value="{{ $info['project_name'] ?? '' }}" placeholder="请输入二级名称"/>
                </td>
            </tr>
            <tr>
                <th>三级名称<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="three_name" value="{{ $info['three_name'] ?? '' }}" placeholder="请输入三级名称"/>
                </td>
            </tr>
            <tr>
                <th>四级名称<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="four_name" value="{{ $info['four_name'] ?? '' }}" placeholder="请输入四级名称"/>
                </td>
            </tr>
            <tr>
                <th>项目名称<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="param_name" value="{{ $info['param_name'] ?? '' }}" placeholder="请输入项目名称"/>

                </td>
            </tr>
            <tr>
                <th>标准（方法）名称<span class="must">*</span></th>
                <td>
                    <textarea name="method_name" placeholder="请输入标准（方法）名称" class="layui-textarea">{{ replace_enter_char($info['method_name'] ?? '',2) }}</textarea>

                </td>
            </tr>
            <tr>
                <th>限制范围<span class="must"></span></th>
                <td>
                    <textarea name="limit_range" placeholder="请输入限制范围" class="layui-textarea">{{ replace_enter_char($info['limit_range'] ?? '',2) }}</textarea>

                </td>
            </tr>
            <tr>
                <th>说明<span class="must"></span></th>
                <td>
                    <textarea name="explain_text" placeholder="请输入说明" class="layui-textarea">{{ replace_enter_char($info['explain_text'] ?? '',2) }}</textarea>

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

    var SAVE_URL = "{{ url('api/admin/certificate_schedule/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/certificate_schedule')}}";//保存成功后跳转到的地址

    var BEGIN_TIME = "{{ $info['ratify_date'] ?? '' }}" ;//批准日期
    var END_TIME = "{{ $info['valid_date'] ?? '' }}" ;//有效期至

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业
</script>
<script src="{{ asset('/js/admin/QualityControl/CertificateSchedule_edit.js') }}?6"  type="text/javascript"></script>
</body>
</html>
