

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

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> 我的同事</div>--}}
<div class="mm">
  <div class="mmhead" id="mywork">

    @include('common.pageParams')
{{--    <div class="tabbox" >--}}
{{--      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加发票</a>--}}
{{--    </div>--}}
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />

        {{--<select class="wmini" name="province_id">--}}
          {{--<option value="">全部</option>--}}
          {{--@foreach ($province_kv as $k=>$txt)--}}
            {{--<option value="{{ $k }}"  @if(isset($province_id) && $province_id == $k) selected @endif >{{ $txt }}</option>--}}
          {{--@endforeach--}}
        {{--</select>--}}
          <span   @if (isset($hidden_option) && ($hidden_option & 1) == 1 ) style="display: none;"  @endif>
                <input type="hidden" class="select_id" name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择企业</button>
          </span>
          <input type="hidden" name="order_no"  value="{{ $order_no ?? '' }}" />

          <select class="wmini" name="invoice_service" style="width: 80px;">
              <option value="">开票服务商</option>
              @foreach ($invoiceService as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultInvoiceService) && $defaultInvoiceService == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="kplx" style="width: 80px;">
              <option value="">开票类型</option>
              @foreach ($kplx as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultKplx) && $defaultKplx == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="invoice_status" style="width: 80px;">
              <option value="">开票状态</option>
              @foreach ($invoiceStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultInvoiceStatus) && $defaultInvoiceStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="upload_status" style="width: 80px;">
              <option value="">开票数据状态</option>
              @foreach ($uploadStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultUploadStatus) && $defaultUploadStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="itype" style="width: 80px;">
              <option value="">发票类型</option>
              @foreach ($itype as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultItype) && $defaultItype == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="tspz" style="width: 80px;">
              <option value="">特殊票种标识</option>
              @foreach ($tspz as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultTspz) && $defaultTspz == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="zsfs" style="width: 80px;">
              <option value="">征税方式</option>
              @foreach ($zsfs as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultZsfs) && $defaultZsfs == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
        <select style="width:80px; height:28px;" name="field">
          <option value="order_num">业务单据号</option>
            <option value="kpzdbs">开票终端标识</option>
            <option value="jqbh">税控设备机器编号</option>
            <option value="yfp_hm">原发票号码</option>
            <option value="fp_hm">发票号码</option>
            <option value="yfp_dm">原发票代码</option>
            <option value="fp_dm">发票代码</option>
            <option value="jff_phone">手机号</option>
            <option value="jff_email">电子邮件</option>
            <option value="jym">校验码</option>
            <option value="pdf_item_key">发票清单PDF文件获取key</option>
            <option value="pdf_key">发票PDF文件获取key</option>
            <option value="ext_code">提取码</option>
            <option value="fpqqlsh">发票请求流水号</option>
            <option value="fp_mw">发票密文</option>
            <option value="kprq">开票日期</option>
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
{{--      <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsByIds(this, 0, 0, 1, 0, 0)" >发送短信[按条件]</button>--}}
{{--      <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsSelected(this, 0, 2, 0, 0)" >发送短信[勾选]</button>--}}
  </div>
  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
        <col width="50">
{{--        <col width="60">--}}
        <col>
        <col>
        <col width="8%">
        <col width="7%">
        <col width="8%">
        <col width="8%">
        <col width="8%">
        <col width="10%">
        <col width="95">
        <col width="95">
        <col width="80">
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
      <th>销售方名称<hr/>销售方纳税人识别号<hr/>发票请求流水号</th>
      <th>购买方名称<hr>购买方纳税人识别号<hr/>开票类型</th>
        <th>业务单据号<hr>开票服务商</th>
        <th>开票状态<hr>数据状态</th>
        <th>原发票号码<hr>发票号码</th>
        <th>原发票代码<hr>发票代码</th>
        <th>合计金额(不含税)<hr>合计税额【税总额】</th>
{{--        <th>价税合计(含税)</th>--}}
        <th>电子发票</th>
      <th>生成时间<hr/>提交数据时间</th>
        <th>开票时间<hr/>冲红时间</th>
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
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/admin/invoices/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/invoices/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/invoices/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "发票" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/invoices/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/invoices/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/invoices/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/invoices/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/invoices/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/invoices/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/invoices/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/rrr_dddd/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/rrr_dddd/ajax_sms_send')}}";// 短信模板发送短信

      var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载
      var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";


      var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业
  </script>

<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>

<script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/admin/QualityControl/Invoices.js') }}?23"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
