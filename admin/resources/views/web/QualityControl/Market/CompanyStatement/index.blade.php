<!DOCTYPE html>
<html lang="en">
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



<div class="mm" style="margin:0">

	<div>
{{--		<div class="com-name">--}}
{{--					 {{ $info['company_name'] ?? '' }}--}}
{{--		</div>--}}
		<table class="layui-table table2 tableWidthFixed">
			<tbody>
				<tr>
					<th width="120">机构名称</th>
					<td>{{ $info['company_name'] ?? '' }}</td>
				</tr>
				<tr>
					<th>联系地址</th>
					<td> {{ $info['addr'] ?? '' }}</td>
				</tr>
				<tr>
					<th>CMA证书编号</th>
					<td>{{ $info['company_certificate_no'] ?? '' }}</td>
				</tr>
				<tr>
					<th>发证日期</th>
					<td>{{ $info['ratify_date'] ?? '' }}</td>
				</tr>
				<tr>
					<th>证书有效期</th>
					<td>{{ $info['valid_date'] ?? '' }}</td>
				</tr>
			</tbody>
		</table>
		<!-- <div class="content-info">
			<p>机构名称：<span>{{ $info['company_name'] ?? '' }}</span></p>
			<p>CMA证书编号：<span>{{ $info['company_certificate_no'] ?? '' }}</span></p>
			<p>发证日期：<span>{{ $info['ratify_date'] ?? '' }}</span></p>
			<p>证书有效期：<span> {{ $info['valid_date'] ?? '' }}</span></p>
			<p>联系地址：<span> {{ $info['laboratory_addr'] ?? '' }}</span></p>
		</div> -->
		<div class="c"></div>
	</div>


  <div class="mmhead" id="mywork">
    @include('common.pageParams')
    <form onsubmit="return false;" class="form-horizontal" style="display: bock;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />

          <input type="hidden" name="company_hidden"  value="{{ $company_hidden ?? 0 }}" />
          <span   @if (isset($company_hidden) && $company_hidden == 1 ) style="display: none;"  @endif>
                <input type="hidden" class="select_id" name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择企业</button>
          </span>
        <select style="width:80px; height:28px;" name="field">
            <option value="resource_name">资源名称</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>

  <h3>◆ 自我声明流程</h3>
  <div style="padding:15px 0; ">
		<p>下载相应的表格和打印 《陕西省检验检测机构资质认定自我声明公开承诺书》和《陕西省检验 检测机构资质认定自我声明确认书》,认真填写并提交市局行政审批处。</p>
		<p>其中《承诺书》需要机构盖章，签字；行政许可事项填写机构变更的具 体事项，比如法定代表人由 XX 变为 XX，并同时提交给市局证明材料， 下面附件处写提交的证明材料，  比如法人变更提交变更前后的营业执照 （标准变更事项及取消检验检测能力事项只附清单）
	</p>
		<a href="http://search.sxsrzrk.com/web/login" target="_blank" class="btn" style="margin-top:10px;">发布自我声明</a>

	</div>


   <h3>◆ 自我声明公告</h3>
  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
        <col>
        <col width="160">
        <col width="100">
    </colgroup>
    <thead>
    <tr>
		<th>文件名</th>
        <th>上传日期</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody id="data_list">
    </tbody>
  </table>

  <div class="mmfoot">
    <div class="mmfleft"></div>
    <div class="pagination">
    </div>
  </div>


    <h3>◆ 机构能力附表</h3>
	<table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="nlfb-table">
	  <colgroup>
	      <col width="200">
	      <col width="150">
	      <col>
	  </colgroup>
	  <thead>
	  <tr>
		<th>文件名</th>
	    <th>上传日期</th>
	    <th align="center">操作</th>
	  </tr>
	  </thead>
	  <tbody id="schedule_data_list">
	  </tbody>
	</table>

</div>

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/web/market/company_statement/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('web/market/company_statement/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('web/market/company_statement/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "机构自我声明" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('web/market/company_statement/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('web/market/company_statement/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/web/market/company_statement/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/web/market/company_statement/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('web/market/company_statement/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('web/market/company_statement/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/web/market/company_statement/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/rrr_dddd/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/rrr_dddd/ajax_sms_send')}}";// 短信模板发送短信

      var SELECT_COMPANY_URL = "{{url('web/market/company/select')}}";// 选择所属企业

      var DOWN_FILE_URL = "{{ url('web/market/down_file') }}";// 下载

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      // var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      // var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      var  SCHEDULE_LIST_DATA = @json($schedule_list ?? []);
      var SCHEDULE_TABLE_ID = 'schedule_data_list';
      var SCHEDULE_BAIDU_TELPLETE = 'baidu_template_data_list_schedule';

  </script>
  <script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/web/QualityControl/Market/CompanyStatement.js') }}?8944"  type="text/javascript"></script>
</body>
</html>
