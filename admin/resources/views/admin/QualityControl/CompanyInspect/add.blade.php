

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
            <tr  @if (isset($hidden_option) && ($hidden_option & 1) == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must">*</span></th>
                <td>
                    <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                    <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                    <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择所属企业</button>
                </td>
            </tr>
            <tr>
                <th>年<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="year_no" value="{{ $info['year_no'] ?? '' }}" placeholder="请输入年"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>
            <tr>
                <th>存在问题<span class="must">*</span></th>
                <td>
<!--                    <input type="text" class="inp wnormal" style="width: 800px;"  name="exist_problem" value="{{ $info['exist_problem'] ?? '' }}" placeholder="请输入存在问题"/>
 -->
					<textarea name="exist_problem" placeholder="请输入存在问题" class="layui-textarea">{{ replace_enter_char($info['exist_problem'] ?? '',2) }}</textarea>

 </td>
            </tr>
            <tr>
                <th>违返条款<span class="must">*</span></th>
                <td>
                    <textarea name="breach_clause" placeholder="请输入违返条款" class="layui-textarea">{{ replace_enter_char($info['breach_clause'] ?? '',2) }}</textarea>

                </td>
            </tr>
            <tr>
                <th>原文链接<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="link_url" value="{{ $info['link_url'] ?? '' }}" placeholder="请输入原文链接"/>
                    <p>格式：http://www.baidu.com</p>
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
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlCompanyInspectedit";

    var SAVE_URL = "{{ url('api/admin/company_inspect/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/company_inspect')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业
</script>
<script src="{{ asset('/js/admin/QualityControl/CompanyInspect_edit.js') }}?8"  type="text/javascript"></script>
</body>
</html>
