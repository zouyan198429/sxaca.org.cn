

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
	
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
		<div>
			<h3> {{ $info['course_name'] ?? '' }}</h3>
		</div>
        <!--  <table class="table1">
            <tr>
                <th>课程</th>
                <td>
                    { { $info['course_name'] ?? '' }}
                </td>
            </tr>
          <tr>
                <th>报名状态</th>
                <td>
                    { { $info['company_status_text'] ?? '' }}
                </td>
            </tr>
				<tr>
					<th>企业名称</th>
					<td>
						{ { $info['company_name'] ?? '' }}
					</td>
				</tr>
               <tr>
                <th>联络人</th>
                <td>
                    { { $info['contacts'] ?? '' }}({ { $info['tel'] ?? '' }})
                </td>
            </tr>
          <tr>
                <th>单价/总价</th>
                <td>
                    ￥{ { $info['price'] ?? '' }}/￥{ { $info['price_total'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>缴费状态</th>
                <td>
                    { { $info['pay_status_text'] ?? '' }}
					<! -- 分班状态{ { $info['join_class_status_text'] ?? '' }}
                </td>
            </tr>

			</table> -->
			<hr>
			<h3>报名学员</h3>
			<style>
				.resource_show .file-wrapper>.content { 
						display: none;
				}
				.resource_show .file-list .file-wrapper>.actions{
					display: none;
				}
				.resource_show .file-wrapper>.actions {
					display: none;
				}
				.resource_show .file-wrapper>.content>.file-name {
					display: none;
				}
				.resource_show .file-wrapper .card {
					margin:0;
				}
				.file-list-grid .file .file-icon {
					width: auto;
				}
				.resourceBlock .file-list-grid .file {
					border: 0;
					margin:0;
					background:none;
				}
				.resourceBlock .file-list .file-icon-image, .uploader-files .file-icon-image {					
					background:none;
				}
			</style>
            <table  lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                          <!--  <th>
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace check_all" value="" onclick="otheraction.seledAll(this)">
                                        <span>全选</span>
                                    </label>
                            </th> -->
                            <th >姓名</th>
                            <th >证书所属单位</th>
                            <th >证件照</th>
                            <th>手机号</th>
							<th>身份证</th>
                            <th>状态</th>
                            <!-- <th>
                                    <span> 缴费状态<hr/>支付单号</span>
                            </th> -->
                            <!-- <th>
                                    <span> 分班状态<hr/>班级</span>
                            </th> -->
                        </tr>
                        </thead>
                        <tbody  id="data_list"   class=" baguetteBoxOne gallery">
                        @foreach ($info['course_order_staff'] as $k => $staff_info)
                            <tr>
                               <!-- <td >
                                    <label>
                                        <input onclick="otheraction.seledSingle(this)" type="checkbox" class="ace check_item"  name="staff_id[]"   value="{{ $staff_info['id'] ?? '' }}" @if(isset($staff_info['is_joined']) && ($staff_info['is_joined'] & 1) == 1)  disabled @endif>
                                        <span class="lbl"></span>
                                    </label>

                                </td> -->
                                <td>
                                        {{ $staff_info['real_name'] ?? '' }}({{ $staff_info['sex_text'] ?? '' }})
                                </td>
                                <td>
                                        {{ $staff_info['certificate_company'] ?? '' }}
                                </td>
                                <td>
                                    <span class="resource_list"  style="display: none;">@json($staff_info['resource_list'] ?? [])</span>
                                    <span  class="resource_show"></span>
                                </td>
                                <td>
                                        {{ $staff_info['mobile'] ?? '' }}
                                </td>
                                <td>
                                    {{ $staff_info['id_number'] ?? '' }}
                                </td>
                                <td>
                                       <!-- ￥{{ $staff_info['price'] ?? '' }} -->

                                    {{ $staff_info['staff_status_text'] ?? '' }}
                                </td>
                               <!-- <td>
                                        {{ $staff_info['pay_status_text'] ?? '' }}
                                    <hr/>
                                    {{ $staff_info['order_no'] ?? '' }}
                                </td> -->
                               <!-- <td>
                                        {{ $staff_info['join_class_status_text'] ?? '' }}
                                    <hr/>
                                    {{ $staff_info['class_name'] ?? '' }}
                                </td> -->
                            </tr>
                        @endforeach
                        </tbody>
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

    var SAVE_URL = "{{ url('api/company/course_order/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/course_order')}}";//保存成功后跳转到的地址

    var DYNAMIC_TABLE = 'dynamic-table';//动态表格id

    var DOWN_FILE_URL = "{{ url('company/down_file') }}";// 下载
    var DEL_FILE_URL = "{{ url('api/company/upload/ajax_del') }}";// 删除文件的接口地址

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/company/QualityControl/CourseOrder_info.js') }}?34"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
