

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
                <th>检测项目<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="ability_name" value="{{ $info['ability_name'] ?? '' }}" placeholder="请输入检测项目"/>
                </td>
            </tr>
            <tr>
                <th>预估参加实验数<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="estimate_add_num" value="{{ $info['estimate_add_num'] ?? '' }}" placeholder="请输入预估参加实验数"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>
            <tr>
                <th>报名起止时间<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wlong join_begin_date" name="join_begin_date" value="{{ $info['join_begin_date'] ?? '' }}" placeholder="请选择开始时间" style="width: 150px;"  readonly="true"/>
                    -
                    <input type="text" class="inp wlong join_end_date" name="join_end_date" value="{{ $info['join_end_date'] ?? '' }}" placeholder="请选择结束时间"  style="width: 150px;" readonly="true"/>
                </td>
            </tr>
            <tr>
                <th>数据提交时限<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="duration_minute" value="{{ $info['duration_minute'] ?? '' }}" placeholder="请输入数据提交时限"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                    <p class="gray">领样后，提交数据时限(<sapn style="color:red;font-weight: bold;">单位：天</sapn>；)</p>
                </td>
            </tr>
            <tr>
                <th>方法标准<span class="must">*</span></th>
                <td>
                    <div class="tags_block" id="project_standards">
                        <p class="tags_list">
                        </p>
                        <input type="text" name="tag_name" value="" class="tag_name">
                        <button class="btn btn-small tag_add" type="button">添加</button>
                    </div>
                </td>
            </tr>
            <tr>
                <th>验证数据项<span class="must">*</span></th>
                <td>
                    <div class="tags_block" id="submit_items">
                        <p class="tags_list">
                        </p>
                        <input type="text" name="tag_name" value="" class="tag_name">
                        <button class="btn btn-small tag_add" type="button">添加</button>
                    </div>
                </td>
            </tr>
            <tr>
                <th> </th>
                <td>
                    <button class="btn btn-l wnormal"  id="submitBtn" >提交</button>
                </td>
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

    var SAVE_URL = "{{ url('api/admin/abilitys/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/abilitys')}}";//保存成功后跳转到的地址

    var BEGIN_TIME = "{{ $info['join_begin_date'] ?? '' }}" ;//报名开始时间
    var END_TIME = "{{ $info['join_end_date'] ?? '' }}" ;//报名截止时间

    var PROJECT_STANDARDS_TAGS = @json($info['project_standards'] ?? []) ;
    var SUBMIT_ITEMS_TAGS = @json($info['submit_items'] ?? []) ;
</script>
<script src="{{ asset('/js/admin/QualityControl/Abilitys_edit.js?449') }}"  type="text/javascript"></script>
</body>
</html>
