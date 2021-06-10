

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
    <div class="tabbox" style="display:none;">
      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加收款帐号</a>
    </div>
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
          <select class="wmini" name="pay_method">
              <option value="">请选择收款开通类型</option>
              @foreach ($payMethod as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultPayMethod) && $defaultPayMethod == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="open_status">
              <option value="">请选择开启状态</option>
              @foreach ($openStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="alipay_auth_status">
              <option value="">请选择支付宝授权状态</option>
              @foreach ($alipayAuthStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultAlipayAuthStatus) && $defaultAlipayAuthStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
        <select style="width:80px; height:28px;" name="field">
          <option value="pay_company_name">收款企业名称</option>
            <option value="pay_key">收款关键字</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>

    <div style="padding-bottom: 15px;">
        <p>支付宝授权说明：</p>
        <p>1、【支付宝授权】操作前，请完成企业支付宝账号注册和实名认证。具体操作请查看【<a href="https://opendocs.alipay.com/open/200/qyzfbsmrz" class="on" target="_blank">企业支付宝账号注册和实名认证指南</a>】。</p>
        <p>2、完成【企业支付宝账号注册和实名认证】后，请点击收款账号列表【支付宝授权】按钮，打开并登录对应企业的支付宝【注：确认登录支付宝的帐号是当前要授权的企业，如不是，请先退出支付宝账号并重新登录，再操作】，按照提示完成支付宝相关授权</p>
    </div>
  {{--
  <div class="table-header">
    { {--<button class="btn btn-danger  btn-xs batch_del"  onclick="action.batchDel(this)">批量删除</button>--} }
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.batchExportExcel(this)" >导出[按条件]</button>
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.exportExcel(this)" >导出[勾选]</button>
    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcelTemplate(this)">导入模版[EXCEL]</button>
    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcel(this)">导入城市</button>
    <div style="display:none;" ><input type="file" class="import_file img_input"></div>{ {--导入file对象--} }
        <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsByIds(this, 0, 0, 1, 0, 0)" >发送短信[按条件]</button>
        <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsSelected(this, 0, 2, 0, 0)" >发送短信[勾选]</button>
  </div>
--}}
  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
{{--        <col width="50">--}}
{{--        <col width="60">--}}
        <col>
        <col width="7%">
        <col>
        <col width="5%">
        <col width="7%">
        <col>
        <col>
        <col>
        <col width="5%">
        <col width="180">
    </colgroup>
    <thead>
    <tr>
{{--      <th>--}}
{{--        <label class="pos-rel">--}}
{{--          <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>--}}
{{--          <!-- <span class="lbl">全选</span> -->--}}
{{--        </label>--}}
{{--      </th>--}}
{{--      <th>ID</th>--}}
      <th>收款企业名称</th>
      <th>收款关键字</th>
        <th>收款开通类型</th>
        <th>开启状态</th>
        <th>支付宝授权状态</th>
        <th>备注</th>
      <th>创建时间</th>
      <th>更新时间</th>
      <th>排序[降序]</th>
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
{{--使用如下命令对数据进行base64的编码与解码：加密使用：$.base64.encode(aaa); url参数时-encodeURIComponent($.base64.encode(aaa))-处理+号，后端才能正常解密；解密使用：$.base64.decode(bbb);--}}
<script src="{{ asset('/static/js/jquery.base64.js') }}"></script>
  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/admin/order_pay_config/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/order_pay_config/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/order_pay_config/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "收款帐号" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/order_pay_config/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/order_pay_config/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/order_pay_config/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/order_pay_config/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/order_pay_config/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/order_pay_config/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/order_pay_config/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/rrr_dddd/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/rrr_dddd/ajax_sms_send')}}";// 短信模板发送短信

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";


      var INVOICE_CONFIG_HYDZFP_EDIT_URL = "{{ url('admin/invoice_config_hydzfp/add/0') }}"; // 电子发票配置沪友修改/添加url
      var INVOICE_SELLER_EDIT_URL = "{{ url('admin/invoice_seller/add/0') }}"; // 发票配置销售方修改/添加url
      var ALIPAY_AUTH_URL = "{!! $alipayAuthURL ?? '' !!}";
      var REFRESH_ALIPAY_TOKEN_URL = "{{ url('api/admin/order_pay_config/ajax_refreshAlipayToken') }}"; // 刷新授权令牌 access_token
  </script>
  <script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/admin/QualityControl/OrderPayConfig.js') }}?16"  type="text/javascript"></script>
</body>
</html>
