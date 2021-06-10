

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
    <div class="tabbox" >
      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加管理员</a>
    </div>
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />

        {{--<select class="wmini" name="province_id">--}}
          {{--<option value="">全部</option>--}}
          {{--@foreach ($province_kv as $k=>$txt)--}}
            {{--<option value="{{ $k }}"  @if(isset($province_id) && $province_id == $k) selected @endif >{{ $txt }}</option>--}}
          {{--@endforeach--}}
        {{--</select>--}}
          <select class="wmini" name="admin_type" style="width: 80px;display: none;">
              <option value="">帐户类型</option>
              @foreach ($adminType as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultAdminType) && $defaultAdminType == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="is_perfect" style="width: 80px;display: none;">
              <option value="">完善资料</option>
              @foreach ($isPerfect as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultIsPerfect) && $defaultIsPerfect == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="issuper" style="width: 80px;display: none;">
              <option value="">超级帐户</option>
              @foreach ($issuper as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultIssuper) && $defaultIssuper == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="open_status" style="width: 80px;">
              <option value="">审核状态</option>
              @foreach ($openStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="account_status" style="width: 80px;">
              <option value="">冻结状态</option>
              @foreach ($accountStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultAccountStatus) && $defaultAccountStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="sex" style="width: 80px;display: none;">
              <option value="">性别</option>
              @foreach ($sex as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultSex) && $defaultSex == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="company_is_legal_persion" style="width: 80px;display: none;">
              <option value="">是否独立法人</option>
              @foreach ($companyIsLegalPersion as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultCompanyIsLegalPersion) && $defaultCompanyIsLegalPersion == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="company_type" style="width: 80px;display: none;">
              <option value="">企业类型</option>
              @foreach ($companyType as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultCompanyType) && $defaultCompanyType == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="company_prop" style="width: 80px;display: none;">
              <option value="">企业性质</option>
              @foreach ($companyProp as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultCompanyProp) && $defaultCompanyProp == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="company_peoples_num" style="width: 80px;display: none;">
              <option value="">单位人数</option>
              @foreach ($companyPeoples as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultCompanyPeoples) && $defaultCompanyPeoples == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="company_grade" style="width: 80px;display: none;">
              <option value="">会员等级</option>
              @foreach ($companyGrade as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultCompanyGrade) && $defaultCompanyGrade == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
        <select style="width:90px; height:28px;" name="field">
            <option value="admin_username">用户名</option>
            <option value="real_name">真实姓名</option>
            <option value="tel">电话</option>
            <option value="mobile">手机</option>
            <option value="qq_number">QQ/微信</option>
{{--            <option value="company_name">单位名称</option>--}}
{{--            <option value="company_credit_code">统一社会信用代码</option>--}}
{{--            <option value="company_legal_credit_code">主体机构统一社会信用代码</option>--}}
{{--            <option value="company_legal_name">主体机构</option>--}}
{{--            <option value="company_legal">法人代表</option>--}}
{{--            <option value="company_certificate_no">证书编号</option>--}}
{{--            <option value="company_contact_name">联系人</option>--}}
{{--            <option value="company_contact_mobile">联系人手机</option>--}}
{{--            <option value="company_contact_tel">联系电话</option>--}}
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>
  <div class="table-header">
    <button class="btn btn-danger  btn-xs batch_del"  onclick="action.batchDel(this)">批量删除</button>
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.batchExportExcel(this)" >导出[按条件]</button>
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.exportExcel(this)" >导出[勾选]</button>
    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcelTemplate(this)">导入模版[EXCEL]</button>
    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcel(this)">导入</button>
    <div style="display:none;" ><input type="file" class="import_file img_input"></div>{{--导入file对象--}}
      <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsByIds(this, 0, 0, 1, 0, 0)" >发送短信[按条件]</button>
      <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsSelected(this, 0, 2, 0, 0)" >发送短信[勾选]</button>
{{--      <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.openSelected(this, 2)" >审核通过[勾选]</button>--}}
{{--      <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.openSelected(this, 4)" >审核不通过[勾选]</button>--}}
    <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.frozenSelected(this, 2)" >冻结[勾选]</button>
    <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.frozenSelected(this, 1)" >解冻[勾选]</button>
  </div>
  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
        <col width="40">
{{--        <col width="60">--}}
        <col width="150">
        <col width="150">
        <col width="150">
        <col>
        <col width="150">
        <col width="100">
        <col>
        <col>
        <col width="90">
        <col width="125">
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
{{--      <th>所属客户端</th>--}}
        <th>用户名</th>
        <th>姓名</th>
        <th>手机号</th>
        <th>电话</th>
        <th>QQ\email\微信</th>
        <th>角色</th>
        <th>审核状态</th>
        <th>冻结状态</th>
      <th>创建时间<hr>上次登录</th>
{{--      <th>更新时间</th>--}}
      {{--<th>排序[降序]</th>--}}
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
      var AJAX_URL = "{{ url('api/admin/staff/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/staff/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/staff/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "帐号管理" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/staff/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/staff/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/staff/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/staff/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/staff/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/staff/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/staff/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/staff/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/staff/ajax_sms_send')}}";// 短信模板发送短信

      var OPEN_OPERATE_URL = "{{ url('api/admin/staff/ajax_open') }}";//审核操作(通过/不通过)
      var ACCOUNT_STATUS_URL = "{{ url('api/admin/staff/ajax_frozen') }}";//操作(冻结/解冻)

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";


  </script>
  <script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/admin/QualityControl/Staff.js') }}?3"  type="text/javascript"></script>
</body>
</html>
