

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

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> 我的同事</div>--}}
<div class="mm">
  <div class="mmhead" id="mywork">

    @include('common.pageParams')
{{--    <div class="tabbox" >--}}
{{--      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加课程订单</a>--}}
{{--    </div>--}}
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
{{--          <span   @if (isset($hidden_option) && $hidden_option == 1 ) style="display: none;"  @endif>--}}
{{--                <input type="hidden" class="select_id" name="company_id"  value="{{ $info['company_id'] ?? '' }}" />--}}
{{--                <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>--}}
{{--                <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>--}}
{{--                <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择企业</button>--}}
{{--          </span>--}}
          <select class="wmini" name="vod_id" style=" width: 80px;@if (isset($hidden_option) && (($hidden_option & 2) == 2) ) display: none;  @endif">
              <option value="">所属课程</option>
              @foreach ($vod_kv as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultVod) && $defaultVod == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>

          <select class="wmini" name="pay_status" style="width: 80px;">
              <option value="">缴费状态</option>
              @foreach ($payStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultPayStatus) && $defaultPayStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="company_status" style="width: 80px;">
              <option value="">订单状态</option>
              @foreach ($companyStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultCompanyStatus) && $defaultCompanyStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="invoice_template_id" style="width: 80px;display: none;">
              <option value="">发票开票模板</option>
              @foreach ($invoice_template_kv as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultInvoiceTemplate) && $defaultInvoiceTemplate == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="invoice_project_template_id" style="width: 80px;display: none;">
              <option value="">发票项目模板</option>
              @foreach ($invoice_project_template_kv as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultInvoiceProjectTemplate) && $defaultInvoiceProjectTemplate == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
        <select style="width:80px; height:28px;" name="field">
            <option value="contacts">联络人员</option>
            <option value="tel">联络人员电话</option>
            <option value="order_no">订单号</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>
  <div class="table-header">
    {{--<button class="btn btn-danger  btn-xs batch_del"  onclick="action.batchDel(this)">批量删除</button>--}}
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.batchExportExcel(this)" >导出[按条件]</button>
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.exportExcel(this)" >导出[勾选]</button>
{{--    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcelTemplate(this)">导入模版[EXCEL]</button>--}}
{{--    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcel(this)">导入城市</button>--}}
{{--    <div style="display:none;" ><input type="file" class="import_file img_input"></div>{ {--导入file对象--} }--}}
{{--        <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsByIds(this, 0, 0, 1, 0, 0)" >发送短信[按条件]</button>--}}
{{--        <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsSelected(this, 0, 2, 0, 0)" >发送短信[勾选]</button>--}}
      <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.paySelected(this)" >缴费[勾选]</button>
  </div>
  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
        <col width="50">
        <col>
        <col>
        <col>
{{--        <col width="140">--}}
        <col width="100">
{{--        <col width="95">--}}
        <col width="150px;">
        {{--        <col width="165">--}}
        <col width="150px;">
        <col width="150px;">
        <col width="10%">
    </colgroup>
    <thead>
    <tr>
        <th>
            <label class="pos-rel">
                <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
            </label>
        </th>
        <th>单位<hr/>订单号</th>
        <th>联络人（电话）<hr/>发票项目模板</th>
        <th>课程<hr/>发票开票模板</th>
{{--        <th>报名人数<!-- 已作废人数 --></th>--}}
        <!-- <th>分班人数<hr/>结业人数</th> -->
        <th>单价<hr/>总价</th>
{{--        <th>分班状态</th>--}}
        <th>订单状态<hr/>报名时间</th>
        <th>缴费状态<hr/>缴费时间</th>
{{--        <th>开票状态<hr/>开票时间</th>--}}
        <th>有效期<hr/>到期时间</th>
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

</div>

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/user/vod_orders/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('user/vod_orders/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('user/vod_orders/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "课程订单" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('user/vod_orders/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('user/vod_orders/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/user/vod_orders/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/user/vod_orders/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('user/vod_orders/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('user/vod_orders/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/user/vod_orders/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/rrr_dddd/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/rrr_dddd/ajax_sms_send')}}";// 短信模板发送短信

{{--      var SELECT_COMPANY_URL = "{{url('admin/user/select')}}";// 选择所属企业--}}

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "userQualityControlRrrDdddedit";

      var PAY_URL = "{{ url('user/vod_orders/pay') }}";//操作(缴费)
      var COURSE_SHOW_URL = "{{url('web/vods/info/')}}/";// 前端课程查看地址
  </script>
  <script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/user/QualityControl/VodOrders.js') }}?13"  type="text/javascript"></script>
</body>
</html>
