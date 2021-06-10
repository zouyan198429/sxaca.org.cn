

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
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr>
                <th>课程</th>
                <td>
                    {{ $info['course_name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>班级</th>
                <td>
                    {{ $info['class_name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>企业名称</th>
                <td>
                    {{ $info['company_name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>报名批次</th>
                <td>
                    {{ $info['course_order_id'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>联络人</th>
                <td>
                    {{ $info['contacts'] ?? '' }}({{ $info['tel'] ?? '' }})
                </td>
            </tr>
            <tr>
                <th>缴费状态/班级状态</th>
                <td>
                    {{ $info['pay_status_text'] ?? '' }}/{{ $info['class_status_text'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>报名学员</th>
                <td>
                    <table  lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
                        <colgroup>
                            <col width="75">
                            <col>
                            <col>
                            <col width="80">
                            <col width="120">
                            <col width="85">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace check_all" value="" onclick="otheraction.seledAll(this)">
                                        <span>全选</span>
                                    </label>
                            </th>
                            <th >
                                    <span>姓名</span>
                            </th>
                            <th>
                                    <span>手机号<hr/>身份证</span>
                            </th>
                            <th>
                                    <span>单价<hr/>人员状态</span>
                            </th>
                            <th>
                                    <span> 缴费状态<hr/>支付单号</span>
                            </th>
                            <th>
                                    <span> 分班状态<hr/>班级</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody  id="data_list" >
                        @foreach ($info['course_order_staff'] as $k => $staff_info)
                            <tr>
                                <td >
                                    <label>
                                        <input onclick="otheraction.seledSingle(this)" type="checkbox" class="ace check_item"  name="staff_id[]"   value="{{ $staff_info['id'] ?? '' }}" @if(isset($staff_info['is_joined']) && ($staff_info['is_joined'] & 1) == 1)  disabled @endif>
                                        <span class="lbl"></span>
                                    </label>

                                </td>
                                <td>
                                        {{ $staff_info['real_name'] ?? '' }}({{ $staff_info['sex_text'] ?? '' }})
                                </td>
                                <td>
                                        {{ $staff_info['mobile'] ?? '' }}
                                    <hr/>
                                    {{ $staff_info['id_number'] ?? '' }}
                                </td>
                                <td>
                                        ￥{{ $staff_info['price'] ?? '' }}
                                    <hr/>
                                    {{ $staff_info['staff_status_text'] ?? '' }}
                                </td>
                                <td>
                                        {{ $staff_info['pay_status_text'] ?? '' }}
                                    <hr/>
                                    {{ $staff_info['order_no'] ?? '' }}
                                </td>
                                <td>
                                        {{ $staff_info['join_class_status_text'] ?? '' }}
                                    <hr/>
                                    {{ $staff_info['class_name'] ?? '' }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>


        </table>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/course_class_company/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/course_class_company')}}";//保存成功后跳转到的地址

    var DYNAMIC_TABLE = 'dynamic-table';//动态表格id
</script>
<script src="{{ asset('/js/admin/QualityControl/CourseClassCompany_info.js') }}?1"  type="text/javascript"></script>
</body>
</html>
