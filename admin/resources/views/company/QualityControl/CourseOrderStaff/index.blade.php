

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
<div class="mm">
  <div class="mmhead" id="mywork">

    @include('common.pageParams')
{{--    <div class="tabbox" >--}}
{{--      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加报名学员</a>--}}
{{--    </div>--}}
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
          <input type="hidden" name="company_hidden"  value="{{ $company_hidden ?? 0 }}" />
          <input type="hidden" name="class_id"  value="{{ $class_id ?? 0 }}" />
          <span   @if (isset($company_hidden) && $company_hidden == 1 ) style="display: none;"  @endif>
                <input type="hidden" class="select_id" name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择企业</button>
          </span>
          <select class="wmini" name="course_id" style="@if (isset($hidden_option) && (($hidden_option & 2) == 2) ) display: none;@endif">
              <option value="">所属课程</option>
              @foreach ($course_kv as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultCourseId) && $defaultCourseId == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="pay_status">
              <option value="">缴费状态</option>
              @foreach ($payStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultPayStatus) && $defaultPayStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="join_class_status" style="@if (isset($hidden_option) && (($hidden_option & 4) == 4) ) display: none;@endif">
              <option value="">分班状态</option>
              @foreach ($joinClassStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultJoinClassStatus) && $defaultJoinClassStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="staff_status">
              <option value="">人员状态</option>
              @foreach ($staffStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultStaffStatus) && $defaultStaffStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
{{--        <select style="width:80px; height:28px;" name="field">--}}
{{--          <option value="type_name">报名学员</option>--}}
{{--        </select>--}}
{{--        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>--}}
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>
    <div class="table-header">
        {{--<button class="btn btn-danger  btn-xs batch_del"  onclick="action.batchDel(this)">批量删除</button>--}}
        <button class="btn btn-success  btn-xs export_excel"  onclick="action.batchExportExcel(this)" >导出[按条件]</button>
        <button class="btn btn-success  btn-xs export_excel"  onclick="action.exportExcel(this)" >导出[勾选]</button> 
    </div>
  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
        <col width="50"> 
        <col>
        <col width="85">
        <col width="70"> 
        <col width="10%">
        <col width="105">
        <col width="120">
        <col width="95">
        <col width="95">
        <col width="95">
        <col width="5%">
    </colgroup>
    <thead>
    <tr>
      <th>
        <label class="pos-rel">
          <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
          <!-- <span class="lbl">全选</span> -->
        </label>
      </th>
{{--      <th>ID</th>--}}
        <th>课程<hr/>单位<hr/>证书所属单位</th>
{{--        <th></th>--}}
        <th>证件照</th>
        <th>姓名<hr/>班级</th>
        <th>手机号<hr/>身份证</th>
        <th>联络人<hr/>联络人电话</th>
        <th>单价<hr/>付款单号</th>
        <th>人员状态<hr/>报名时间</th>
        <th>缴费状态<hr/>缴费时间</th>
        <th>分班状态<hr/>分班时间</th>
      <th>操作</th>
    </tr>
    </thead>
    <tbody id="data_list"  class=" baguetteBoxOne gallery">
    </tbody>
  </table>
  <div class="mmfoot">
    <div class="mmfleft"></div>
    <div class="pagination">
    </div>
  </div>

</div>

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script> 
  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/company/course_order_staff/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('company/course_order_staff/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('company/course_order_staff/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "报名学员" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('company/course_order_staff/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('company/course_order_staff/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/company/course_order_staff/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/company/course_order_staff/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('company/course_order_staff/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('company/course_order_staff/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/company/course_order_staff/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/rrr_dddd/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/rrr_dddd/ajax_sms_send')}}";// 短信模板发送短信

      var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业

      var STAFF_STATUS_URL = "{{ url('api/company/course_order_staff/ajax_frozen') }}";//操作(作废/取消作废)
      var JOIN_CLASS_URL = "{{ url('company/course_order_staff/join_class') }}";//操作(分班)
      var CANCEL_CLASS_URL  = "{{ url('api/company/course_order_staff/ajax_cancel_class') }}";//操作(取消分班)
      var PAY_URL = "{{ url('company/course_order_staff/pay') }}";//操作(缴费)

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";


      var DOWN_FILE_URL = "{{ url('company/down_file') }}";// 下载
      var DEL_FILE_URL = "{{ url('api/company/upload/ajax_del') }}";// 删除文件的接口地址

  </script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>

<script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/company/QualityControl/CourseOrderStaff.js') }}?19"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
