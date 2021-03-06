

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
        <a href="javascript:void(0);" class="on" onclick="otheraction.iframeModify(0)">添加章节</a>
      <a href="javascript:void(0);" class="on" onclick="action.iframeModify(0)">添加课件</a>
    </div>
    <form onsubmit="return false;" class="form-horizontal" style="display: block;" role="form" method="post" id="search_frm" action="#">
      <div class="msearch fr">
          <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
          <select class="wmini" name="vod_id" style=" @if (isset($hidden_option) && (($hidden_option & 2) == 2) ) display: none;  @endif">
              <option value="">所属课程</option>
              @foreach ($vod_kv as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultVod) && $defaultVod == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="video_type">
              <option value="">是否视频</option>
              @foreach ($videoType as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultVideoType) && $defaultVideoType == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
          <select class="wmini" name="status_online"  style=" @if (isset($hidden_option) && (($hidden_option & 4) == 4) ) display: none;  @endif">
              <option value="">上架状态</option>
              @foreach ($statusOnline as $k=>$txt)
                  <option value="{{ $k }}"  @if(isset($defaultStatusOnline) && $defaultStatusOnline == $k) selected @endif >{{ $txt }}</option>
              @endforeach
          </select>
        <select style="width:80px; height:28px;" name="field">
          <option value="video_name">目录/视频名称</option>
            <option value="video_url">视频网络文件地址</option>
            <option value="explain_remarks">简要概述</option>
        </select>
        <input type="text" value=""    name="keyword"  placeholder="请输入关键字"/>
        <button class="btn btn-normal search_frm">搜索</button>
      </div>
    </form>
  </div>
    <div style="padding-bottom: 15px;">
        <p>视频文件说明：</p>
        <p>1、优先选择mp4格式的视频。--跨平台支持，即支持pc端和移动端（ios和安卓）。</p>
        <p>2、如果不是mp4格式可用压制转码工具转为mp4格式。可选工具：<a href="javascript:void(0);" class="on" onclick="otheraction.down_file('/downfile/ShanaEncoder5.2.0.4.zip')">下载ShanaEncoder视频压制转码软件</a>、格式工厂。视频转码格式需要是h.264；音频转码格式需要是AAC</p>
        <p>3、建议点播优先使用mp4，其次使用m3u8。直播优先使用m3u8,这样可以兼容各平台。</p>
        <p>4、尽量不要使用flv来做点播，也不要使用rtmp协议来做直播,移动端不支持flv格式的点播放，也不支持rtmp协议的直播</p>
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
        <col width="10%">
        <col>
        <col width="8%">
        <col width="10%">
        <col  width="8%">
        <col width="10%">
        <col>
        <col  width="95">
{{--        <col>--}}
        <col width="5%">
        <col  width="8%">
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
        <th>所属课程</th>
      <th>章节/课件名称</th>
        <th>记录类型<hr/>状态</th>
        <th>封面图</th>
        <th>视频类型<hr/>视频地址</th>
        <th>视频</th>
        <th>附件资料</th>
{{--      <th></th>--}}
      <th>创建时间<hr/>更新时间</th>
      <th>排序[降序]</th>
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
      var AJAX_URL = "{{ url('api/admin/vod_video/ajax_alist') }}";//ajax请求的url
      var ADD_URL = "{{ url('admin/vod_video/add/0') }}"; //添加url

      var IFRAME_MODIFY_URL = "{{url('admin/vod_video/add/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_URL_TITLE = "课程目录" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题
      var IFRAME_MODIFY_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面

      var SHOW_URL = "{{url('admin/vod_video/info/')}}/";//显示页面地址前缀 + id
      var SHOW_URL_TITLE = "" ;// 详情弹窗显示提示
      var SHOW_CLOSE_OPERATE = 0 ;// 详情弹窗operate_num关闭时的操作0不做任何操作1刷新当前页面2刷新当前列表页面
      var EDIT_URL = "{{url('admin/vod_video/add/')}}/";//修改页面地址前缀 + id
      var DEL_URL = "{{ url('api/admin/vod_video/ajax_del') }}";//删除页面地址
      var BATCH_DEL_URL = "{{ url('api/admin/vod_video/ajax_del') }}";//批量删除页面地址
      var EXPORT_EXCEL_URL = "{{ url('admin/vod_video/export') }}";//导出EXCEL地址
      var IMPORT_EXCEL_TEMPLATE_URL = "{{ url('admin/vod_video/import_template') }}";//导入EXCEL模版地址
      var IMPORT_EXCEL_URL = "{{ url('api/admin/vod_video/import') }}";//导入EXCEL地址
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

      var IFRAME_MODIFY_DIR_URL = "{{url('admin/vod_video/addDir/')}}/";//添加/修改页面地址前缀 + id
      var IFRAME_MODIFY_DIR_URL_TITLE = "章节" ;// 详情弹窗显示提示  [添加/修改] +  栏目/主题

      var COURSE_SHOW_URL = "{{url('web/vod_video/info/')}}/";// 前端课程查看地址
  </script>

<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>

<script src="{{asset('js/common/list.js')}}?2"></script>
  <script src="{{ asset('js/admin/QualityControl/VodVideo.js') }}?14"  type="text/javascript"></script>

@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
