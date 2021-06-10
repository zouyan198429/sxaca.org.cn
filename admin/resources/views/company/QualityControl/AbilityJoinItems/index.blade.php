

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>能力验证--已报名项目</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  @include('admin.layout_public.pagehead')
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>
<div class="layui-fluid">
  <div class="layui-row">
      <div class="layui-col-md12">

        <div class="layui-card">
            @include('common.pageParams')
            <div class="layui-card-header">
                  <h3 style="width:120px; float: left;">已报名项目</h3>
            </div>

            <form onsubmit="return false;" class="form-horizontal" style="display: none;" role="form" method="post" id="search_frm" action="#">
              <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />

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
          <div class="layui-card-body">
              <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
                <colgroup>
                    <col>
                    <col >
                    <col >
                    <col width="75" >
                    <col width="105">
                    <col width="150">
                    <col width="150">
                    <col width="75">
                    <col width="150">
                    <col width="80">
                    <col width="160">
                </colgroup>
                <thead>
                <tr>

{{--                    <th>ID</th>--}}
                    <th>检测项目</th>
                    <th>能力验证编码</th>
                    <th>方法标准(已选)</th>
                    <th>联系人</th>
                    <th>联系人手机</th>
                    <th>发布时间</th>
                    <th>报名时间</th>
                    <th>是否取样</th>
                    <th>上传数据<hr/>截止时间</th>
                    <th>验证结果</th>
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
        </div>
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
      var AJAX_URL = "{{ url('api/company/ability_join_item/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('company/ability_join_item/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('company/ability_join_item/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "项目" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('company/ability_join_item/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "详情" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('company/ability_join_item/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/company/ability_join_item/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/company/ability_join_item/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('company/ability_join_item/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('company/ability_join_item/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/company/ability_join_item/import') }}";//导入EXCEL地址
      var IMPORT_EXCEL_CLASS = "import_file";// 导入EXCEL的file的class
      var SMS_SEND_PAGE_URL = "{{url('admin/rrr_dddd/sms_send')}}";// 选择短信模板页面
      var SMS_SEND_URL = "{{url('api/admin/rrr_dddd/ajax_sms_send')}}";// 短信模板发送短信

      var IFRAME_SAMPLE_RESULT_URL = "{{url('company/ability_join_item/sample_result/')}}/";//上报数据页面地址前缀 + id
      var IFRAME_SAMPLE_RESULT_INFO_URL = "{{url('company/ability_join_item/sample_result_info/')}}/"; // 获得指定测试序号的 单次测试数据 + id + retry_no

      // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
      var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";


  </script>
  <script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/company/QualityControl/AbilityJoinItem.js') }}?31"  type="text/javascript"></script>
</body>
</html>
