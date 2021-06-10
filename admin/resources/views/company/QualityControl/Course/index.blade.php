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


<div class="layui-fluid">
	<div class="layui-row">
		<div class="layui-col-md12">
			<div class="layui-card">
					<div class="layui-card-header">
						培训课程
					</div>
					<div class="layui-card-body">
						
							<div class="mmhead" id="mywork">
								@include('common.pageParams')
								<form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
									<div class="msearch fr">
										<input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
										<select style="width:80px; height:28px;" name="field">
											<option value="course_name">课程名称</option>
											<option value="explain_remarks">简要概述</option>
										</select>
										<input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
										<button class="btn btn-normal search_frm">搜索</button>
									</div>
								</form>
							</div>
							 
							<table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">								 
								<thead>
									<tr>
										<th>课程名称</th>
										<th>状态</th>
										<th>我的参与</th>
										<th>操作</th>
									</tr>
								</thead>
								<tbody id="data_list"  class=" baguetteBoxOne gallery" >
									
								</tbody>
							</table>
							
							<div class="mmfoot">
								<div class="mmfleft"></div>
								<div class="pagination">
								</div>
							</div>
			</div>
		</div> 
	</div>
</div>
<a href="javascript:void(0);" class="btn btn-success  btn-xs  on" id="testBTN" style="display: none;">测试</a>

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/company/course/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('company/course/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('company/course/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "课程" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('company/course/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('company/course/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/company/course/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/company/course/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('company/course/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('company/course/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/company/course/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/rrr_dddd/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/rrr_dddd/ajax_sms_send')}}";// 短信模板发送短信

      var DOWN_FILE_URL = "{{ url('company/down_file') }}";// 下载
      var DEL_FILE_URL = "{{ url('api/company/upload/ajax_del') }}";// 删除文件的接口地址

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";


      var JOIN_URL = "{{url('company/course/join/')}}/";//报名页面地址前缀 + id

      var PAY_URL = "{{ url('company/course_order/pay') }}";//操作(缴费)

  </script>

<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>

<script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/company/QualityControl/Course.js') }}?268"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
