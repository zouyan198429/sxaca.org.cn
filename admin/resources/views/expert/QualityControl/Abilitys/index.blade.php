

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
{{--      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加项目</a>--}}
{{--    </div>--}}
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />

          <select class="wmini" name="status" style="width: 80px;">
              <option value="">状态</option>
              @foreach ($status as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultStatus) && $defaultStatus == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>

          <select class="wmini" name="is_publish" style="width: 80px;">
              <option value="">是否公布</option>
              @foreach ($isPublish as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultIsPublish) && $defaultIsPublish == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
        <select style="width:80px; height:28px;" name="field">
          <option value="ability_name">检测项目</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>

  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
    <colgroup>
        <col width="50">
        <col>
       <!--  <col width="60"> -->
        <col width="80">
        <col>
        <col>
        <col width="">
        <col width="90">
        <col width="60">
        <col width="60">
        <col width="60">
        <col width="90">
        <col width="90">
        <col width="150">
    </colgroup>
    <thead>
    <tr>
     <!--  <th>
        <label class="pos-rel">
          <input type="checkbox"  class="ace check_all"  value="" onclick="action.seledAll(this)"/>
        </label>
      </th> -->
      <th>ID</th>
      <th>检测项目</th>
        <!-- <th>预估参加数</th> -->
        <th>报名企业</th>
        <th>报名起止时间</th>
        <!-- <th>方法标准</th> -->
        <th>验证数据项</th>
        <th>提交时限</th>
        <th>状态</th>
        <th>初测提交数据<hr/>补测提交数据</th>
        <th>初测满意数<hr/>补测满意数</th>
        <th>初测非满意数<hr/>补测非满意数</th>
        <th>是否公布<hr/>公布时间</th>
      <th>创建时间</th>
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
      var AJAX_URL = "{{ url('api/expert/abilitys/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('expert/abilitys/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('expert/abilitys/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "项目" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('expert/abilitys/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "详情" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('expert/abilitys/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/expert/abilitys/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/expert/abilitys/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('expert/abilitys/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('expert/abilitys/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/expert/abilitys/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/rrr_dddd/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/rrr_dddd/ajax_sms_send')}}";// 短信模板发送短信


      var ABILITYS_ADMIN_URL = "{{url('expert/abilitys_admin/')}}/";//项目管理 页面地址前缀 + id
      var EXPORT_JOIN_EXCEL_URL = "{{ url('expert/abilitys/export_join/') }}/";//导出报名企业EXCEL地址+ id
      var IFRAME_PUBLISH_URL = "{{url('expert/abilitys/publish/')}}/";//公布结果页面地址前缀 + id

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";


  </script>
  <script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/expert/QualityControl/Abilitys.js') }}?825"  type="text/javascript"></script>
</body>
</html>
