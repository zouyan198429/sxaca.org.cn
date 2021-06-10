

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
<div class="mm">
  <div class="mmhead" id="mywork">

    @include('common.pageParams')
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
          <span>
                <input type="hidden" class="select_id" name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择企业</button>
          </span>
          <select class="wmini" name="retry_no" style="width: 80px;">
              <option value="">测试次序</option>
              @foreach ($retryNo as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultRetryNo) && $defaultRetryNo == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="is_sample" style="width: 80px;">
              <option value="">是否取样</option>
              @foreach ($isSample as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultIsSample) && $defaultIsSample == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="status" style="width: 80px;">
              <option value="">状态</option>
              @foreach ($status as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultStatus) && $defaultStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="is_print" style="width: 80px;">
              <option value="">证书打印</option>
              @foreach ($isPrint as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultIsPrint) && $defaultIsPrint == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="is_grant" style="width: 80px;">
              <option value="">证书领取</option>
              @foreach ($isGrant as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultIsGrant) && $defaultIsGrant == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>

        <select style="width:120px; height:28px;" name="field">
          <option value="ability_code">能力验证代码</option>
            <option value="contacts">联系人姓名</option>
            <option value="mobile">联系人手机</option>
            <option value="tel">联系人电话</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>
    <div style="padding-bottom: 15px;">
        <p>证书打印说明：</p>
        <p>1、<a href="javascript:void(0);" class="on" onclick="otheraction.downDrive(this)">下载网页打印机驱动</a>并安装。</p>
        <p>2、确保C-Lodop云打印服务器开启【默认会开机自动开启】</p>
        <p>3、确保C-Lodop云打印服务器开启端口：8000【可能通过(设置)变更】</p>
        <p>4、项目报名后，打印证书前，请在《证书设置》设置对应年份的证书落款署名、证书落款日期。</p>
    </div>

    <div class="table-header">

        <button class="btn btn-success  btn-xs export_excel ace-icon fa fa-cloud-download"  onclick="otheraction.downDrive(this)" >下载网页打印机驱动</button>
        <button class="btn btn-success  btn-xs export_excel ace-icon fa fa-print"  onclick="otheraction.printSearch(this)" >打印证书[按条件]</button>
        <button class="btn btn-success  btn-xs export_excel ace-icon fa fa-print"  onclick="otheraction.printSelected(this)" >打印证书[勾选]</button>

        <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsByIds(this, 0, 0, 1, 0, 0)" >发送短信[按条件]</button>
        <button class="btn btn-success  btn-xs export_excel"  onclick="action.smsSelected(this, 0, 2, 0, 0)" >发送短信[勾选]</button>
    </div>
  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
        <col width="50">
        <col width="115">
        <col>
        <col width="75" >
        <col width="105">
        <col width="95">
{{--        <col width="75">--}}
        <col width="75">
{{--        <col width="120">--}}
        <col width="100">
        <col width="110">
        <col width="75">
        <col width="135">
    </colgroup>
    <thead>
    <tr>
     <th>
        <label class="pos-rel">
          <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
        </label>
      </th>
{{--        <th>ID</th>--}}
        <th>能力验证代码</th>
        <th>单位</th>
        <th>联系人</th>
        <th>联系人手机<hr/>联系人电话</th>
        <th>报名时间</th>
{{--        <th></th>--}}
        <th>报名项目<hr/>满意项目</th>
{{--        <th></th>--}}
        <th>状态<hr/>取样状态</th>
        <th>初测提交数据<hr/>补测提交数据</th>
        <th>打印证书<hr/>领取证书</th>
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
<script src="{{asset('/static/js/LodopFuncs.js')}}"></script>
  @include('public.dynamic_list_foot')

  <script type="text/javascript">
      var OPERATE_TYPE = <?php echo isset($operate_type)?$operate_type:0; ?>;
      var AUTO_READ_FIRST = false;//自动读取第一页 true:自动读取 false:指定地方读取
      var LIST_FUNCTION_NAME = "reset_list_self";// 列表刷新函数名称, 需要列表刷新同步时，使用自定义方法reset_list_self；异步时没有必要自定义
      var AJAX_URL = "{{ url('api/admin/ability_join/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/ability_join/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/ability_join/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "项目" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/ability_join/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "详情" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/ability_join/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/ability_join/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/ability_join/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/ability_join/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/ability_join/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/ability_join/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/ability_join/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/ability_join/ajax_sms_send')}}";// 短信模板发送短信

      var IFRAME_SAMPLE_URL = "{{url('admin/ability_join/get_sample/')}}/";//添加/修改页面地址前缀 + id
      var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业
      var IFRAME_PRINT_URL = "{{url('admin/ability_join/print/')}}/";//打印证书页面地址前缀 + id

      var IS_PRINT_URL = "{{ url('api/admin/ability_join/ajax_print') }}";//操作(标记打印操作)
      var IS_GRANT_URL = "{{ url('api/admin/ability_join/ajax_grant') }}";//操作(标记证书领取操作)

      var SEARCH_PRINT_URL = "{{ url('api/admin/ability_join/ajax_search_print') }}";//按查询条件结果操作(标记打印操作)
      var DOWN_DRIVE_URL = "{{ url('admin/down_drive') }}";// 下载网页打印机驱动
      // 打印配置
      var PRINT_INT_ORIENT = 3;// intOrient,intPageWidth,intPageHeight,strPageName
      var PRINT_INT_PAGE_WIDTH = 970;// 580;
      var PRINT_INT_PAGE_HEIGHT = 45;
      var PRINT_STR_PAGE_NAME = '';

      // https://www.it610.com/article/2094844.htm  打印函数LODOP.SET_PRINT_PAGESIZE
      // SET_PRINT_PAGESIZE(intOrient,intPageWidth,intPageHeight,strPageName);
      //
      // 参数含义：
      // intOrient：打印方向及纸张类型
      // 值为1---纵向打印，固定纸张；
      // 值为2---横向打印，固定纸张；
      // 值为3---纵向打印，宽度固定，高度按打印内容的高度自适应；
      // 0(或其它)----打印方向由操作者自行选择或按打印机缺省设置。
      // intPageWidth：
      // 纸张宽，单位为0.1mm 譬如该参数值为45，则表示4.5mm,计量精度是0.1mm。
      //
      // intPageHeight：
      // 固定纸张时该参数是纸张高；高度自适应时该参数是纸张底边的空白高，计量单位与纸张宽一样。
      //
      // strPageName：
      // 纸张名，必须intPageWidth等于零时本参数才有效，有如下选择：
      // Letter, LetterSmall, Tabloid, Ledger, Legal,Statement, Executive,
      //     A3, A4, A4Small, A5, B4, B5, Folio, Quarto, qr10X14, qr11X17, Note,
      //     Env9, Env10, Env11, Env12,Env14, Sheet, DSheet, ESheet

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";


  </script>
  <script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/admin/QualityControl/AbilityJoin.js') }}?16"  type="text/javascript"></script>
</body>
</html>
