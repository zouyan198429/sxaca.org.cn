

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
    <div class="tabbox" >
      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加用户</a>
    </div>
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />


          <span>
                <input type="hidden" class="select_id" name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择企业</button>
          </span>
          <select class="wmini" name="city_id" style="width: 80px;">
              <option value="">城市</option>
              @foreach ($citys_kv as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultCity) && $defaultCity == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
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
          <select class="wmini" name="role_num" style="width: 80px;">
              <option value="">角色</option>
              @foreach ($roleNum as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultRoleNum) && $defaultRoleNum == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="role_status" style="width: 80px;display: none;">
              <option value="">角色审核状态</option>
              @foreach ($roleStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultRoleStatus) && $defaultRoleStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="sign_status" style="width: 80px;display: none;">
              <option value="">授权人审核状态</option>
              @foreach ($signStatus as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultSignStatus) && $defaultSignStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="sign_is_food" style="width: 80px;display: none;">
              <option value="">是否食品</option>
              @foreach ($signIsFood as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultSignIsFood) && $defaultSignIsFood == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
        <select style="width:90px; height:28px;" name="field">
            <option value="admin_username">用户名</option>
            <option value="real_name">真实姓名</option>
            <option value="mobile">手机</option>
            <option value="qq_number">QQ/微信</option>
            <option value="position_name">职位</option>
            <option value="email">邮箱</option>
            <option value="id_number">身份证号</option>
            <option value="addr">通讯地址</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字" style="width: 100px;"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>
  <div class="table-header">
    <button class="btn btn-danger  btn-xs batch_del"  onclick="action.batchDel(this)">批量删除</button>
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.batchExportExcel(this)" >导出[按条件]</button>
    <button class="btn btn-success  btn-xs export_excel"  onclick="action.exportExcel(this)" >导出[勾选]</button>
    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcelTemplate(this)">导入模版[EXCEL]</button>
      <button class="btn btn-success  btn-xs import_excel"  onclick="otheraction.iframeImport(0)">导入</button>
{{--    <button class="btn btn-success  btn-xs import_excel"  onclick="action.importExcel(this)">导入</button>--}}
{{--    <div style="display:none;" ><input type="file" class="import_file img_input"></div>{ {--导入file对象--}}
      <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsByIds(this, 0, 0, 1, 0, 0)" >发送短信[按条件]</button>
      <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsSelected(this, 0, 2, 0, 0)" >发送短信[勾选]</button>
      <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.openSelected(this, 2)" >审核通过[勾选]</button>
      <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.openSelected(this, 4)" >审核不通过[勾选]</button>
      <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.roleSelected(this, 2)" >人员角色审核通过[勾选]</button>
      <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.roleSelected(this, 4)" >人员角色审核不通过[勾选]</button>
      <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.signSelected(this, 2)" >授权人审核通过[勾选]</button>
      <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.signSelected(this, 4)" >授权人审核不通过[勾选]</button>
    <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.frozenSelected(this, 2)" >冻结[勾选]</button>
    <button class="btn btn-success  btn-xs export_excel"  onclick="otheraction.frozenSelected(this, 1)" >解冻[勾选]</button>
  </div>
  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
        <col width="50">
{{--        <col width="50">--}}

{{--        <col width="60">--}}
        <col width="75">
        <col width="105">

        <col >
        <col  width="7%">
        <!-- <col >
        <col width="75">
        <col width="75"> -->
        <col  width="7%">
        <col>
        <col  width="75">
        <col width="75" >

{{--        <col width="75">--}}
        <col width="90">
{{--        <col width="160">--}}
        <col width="12%">
    </colgroup>
    <thead>
    <tr>
        <th>
          <label class="pos-rel">
            <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
          </label>
        </th>
{{--        <th>ID</th>--}}

        <th>姓名</th>
{{--        <th>性别</th>--}}
        <th>手机号<hr/>所属企业</th>
       <!--  <th>城市</th>
        <th>邮箱</th>
        <th>微信号</th> -->

        <th>证件照</th>
        <th>职位</th>
        <th>角色</th>
        <th>签字范围<hr/>签字审核状态</th>
        <th>完善资料<hr/>冻结状态</th>
        <th>信息审核<hr/>角色状态</th>
{{--        <th>冻结状态</th>--}}
{{--        <th>上次登录</th>--}}
      <th>上次登录<hr/>创建时间</th>
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
      var AJAX_URL = "{{ url('api/admin/user/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/user/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/user/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "用户" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/user/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/user/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/user/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/user/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/user/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/user/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/user/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/user/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/user/ajax_sms_send')}}";// 短信模板发送短信

      var ROLE_OPERATE_URL = "{{ url('api/admin/user/ajax_role') }}";//角色审核操作(通过/不通过)
      var SIGN_OPERATE_URL = "{{ url('api/admin/user/ajax_sign') }}";//授权人审核操作(通过/不通过)
      var OPEN_OPERATE_URL = "{{ url('api/admin/user/ajax_open') }}";//审核操作(通过/不通过)
      var ACCOUNT_STATUS_URL = "{{ url('api/admin/user/ajax_frozen') }}";//操作(冻结/解冻)


      var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业
      var IFRAME_IMPORT_URL = "{{url('admin/user/import_bath')}}/";// 导入

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";


      var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载
      var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

  </script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>

<script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/admin/QualityControl/User.js?67') }}"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
