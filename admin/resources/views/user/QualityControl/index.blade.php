<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>前端 -{{ config('public.webName') }}</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css?8')}}" media="all">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">

  <script>
  /^http(s*):\/\//.test(location.href) || alert('请先部署到 localhost 下再访问');
  </script>
</head>
<body class="layui-layout-body">

  <div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
      <div class="layui-header">
        <!-- 头部区域 -->
        <ul class="layui-nav layui-layout-left">
          <li class="layui-nav-item layadmin-flexible" lay-unselect>
            <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
              <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
            </a>
          </li>
          {{--<li class="layui-nav-item layui-hide-xs" lay-unselect>--}}
            {{--<a href="http://www.layui.com/admin/" target="_blank" title="前台">--}}
              {{--<i class="layui-icon layui-icon-website"></i>--}}
            {{--</a>--}}
          {{--</li>--}}
          <li class="layui-nav-item" lay-unselect>
            <a href="javascript:;" layadmin-event="refresh" title="刷新">
              <i class="layui-icon layui-icon-refresh-3"></i>
            </a>
          </li>
          {{----}}
          {{--<li class="layui-nav-item layui-hide-xs" lay-unselect>--}}
            {{--<input type="text" placeholder="搜索..." autocomplete="off" class="layui-input layui-input-search" layadmin-event="serach" lay-action="{{ url('layui/template/search') }}?keywords=">--}}
          {{--</li>--}}
          {{----}}
        </ul>
        <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">

          {{--<li class="layui-nav-item" lay-unselect>--}}
            {{--<a lay-href="{{ url('layui/app/message/index') }}" layadmin-event="message" lay-text="消息中心">--}}
              {{--<i class="layui-icon layui-icon-notice"></i>  --}}
              {{----}}
              {{--<!-- 如果有新消息，则显示小圆点 -->--}}
              {{--<span class="layui-badge-dot"></span>--}}
            {{--</a>--}}
          {{--</li>--}}
          <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="theme">
              <i class="layui-icon layui-icon-theme"></i>
            </a>
          </li>

          {{--<li class="layui-nav-item layui-hide-xs" lay-unselect>--}}
            {{--<a href="javascript:;" layadmin-event="note">--}}
              {{--<i class="layui-icon layui-icon-note"></i>--}}
            {{--</a>--}}
          {{--</li>--}}

          <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="fullscreen">
              <i class="layui-icon layui-icon-screen-full"></i>
            </a>
          </li>
          <li class="layui-nav-item" lay-unselect>
            <a href="javascript:;">
              <cite>{{ $baseArr['real_name'] ?? '' }}</cite>
            </a>
            <dl class="layui-nav-child">
              <dd><a lay-href="{{ url('user/info') }}">基本资料</a></dd>
{{--              <dd><a lay-href="{{ url('user/password') }}">修改密码</a></dd>--}}
              <hr>
              <dd  style="text-align: center;"><a href="{{ url('user/logout') }}">退出</a></dd>
            </dl>
          </li>

          {{--<li class="layui-nav-item layui-hide-xs" lay-unselect>--}}
            {{--<a href="javascript:;" layadmin-event="about"><i class="layui-icon layui-icon-more-vertical"></i></a>--}}
          {{--</li>--}}
          <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
            <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
          </li>
        </ul>
      </div>

      <!-- 侧边菜单 -->
      <div class="layui-side layui-side-menu">
        <div class="layui-side-scroll">
          {{--<div class="layui-logo" lay-href="{{ url('layui/home/console') }}">--}}
          <div class="layui-logo" lay-href="{{ url('/help/index.html') }}">
            <span>质量认证认可协会-用户中心</span>
          </div>

          <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
            {{--<li data-name="home" class="layui-nav-item layui-nav-itemed">--}}
              {{--<a href="javascript:;" lay-tips="主页" lay-direction="2">--}}
                {{--<i class="layui-icon layui-icon-home"></i>--}}
                {{--<cite>主页</cite>--}}
              {{--</a>--}}
              {{--<dl class="layui-nav-child">--}}
                {{--<dd data-name="console" class="layui-this">--}}
                  {{--<a lay-href="{{ url('layui/home/console') }}">控制台</a>--}}
                  {{--<a lay-href="{{ url('/help/index.html') }}">控制台</a>--}}
                {{--</dd>--}}
                {{--<dd data-name="console">--}}
                  {{--<a lay-href="{{ url('layui/home/homepage1') }}">主页一</a>--}}
                {{--</dd>--}}
                {{--<dd data-name="console">--}}
                  {{--<a lay-href="{{ url('layui/home/homepage2') }}">主页二</a>--}}
                {{--</dd>--}}
              {{--</dl>--}}
            {{--</li>--}}

{{--            <li data-name="user" class="layui-nav-item">--}}
{{--              <a href="javascript:;" lay-tips="面授报名" lay-direction="2">--}}
{{--                <i class="layui-icon layui-icon-component"></i>--}}
{{--                <cite>面授报名</cite>--}}
{{--              </a>--}}
{{--                <dl class="layui-nav-child">--}}
{{--                    <dd>--}}
{{--                        <a lay-href="javascript:void(10);">面授报名</a>--}}
{{--                    </dd>--}}
{{--                </dl>--}}
{{--            </li>--}}
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="我的课程" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>我的课程</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('user/vods') }}">在线课程</a>
                      </dd>
                  </dl>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('user/vod_orders') }}">我的课程</a>
                      </dd>
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item" style="display: none;">
                  <a href="javascript:;" lay-tips="收款管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>收款管理</cite>
                  </a>
                  <dl class="layui-nav-child">
{{--                      <dd>--}}
{{--                          <a lay-href="{{ url('user/payment_type') }}">付款/收款类型</a>--}}
{{--                      </dd>--}}
                      <dd>
                          <a lay-href="{{ url('user/payment_project') }}?hidden_option=1">付款/收款项目</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('user/payment_record') }}?hidden_option=1">付款/收款记录</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('user/payment_record_flow') }}?hidden_option=1">付款/收款记录流水</a>
                      </dd>
{{--                      <dd>--}}
{{--                          <a lay-href="{{ url('user/payment_record_log') }}">付款/收款记录操作日志</a>--}}
{{--                      </dd>--}}
                  </dl>
              </li>
{{--              <li data-name="user" class="layui-nav-item">--}}
{{--                  <a href="javascript:;" lay-tips="我的考试" lay-direction="2">--}}
{{--                      <i class="layui-icon layui-icon-component"></i>--}}
{{--                      <cite>我的考试</cite>--}}
{{--                  </a>--}}
{{--                  <dl class="layui-nav-child">--}}
{{--                      <dd>--}}
{{--                          <a lay-href="javascript:void(30);">我的考试</a>--}}
{{--                      </dd>--}}
{{--                  </dl>--}}
{{--              </li>--}}
{{--              <li data-name="user" class="layui-nav-item">--}}
{{--                  <a href="javascript:;" lay-tips="消息中心" lay-direction="2">--}}
{{--                      <i class="layui-icon layui-icon-component"></i>--}}
{{--                      <cite>消息中心</cite>--}}
{{--                  </a>--}}
{{--                  <dl class="layui-nav-child">--}}
{{--                      <dd>--}}
{{--                          <a lay-href="javascript:void(40);">消息中心</a>--}}
{{--                      </dd>--}}
{{--                  </dl>--}}
{{--              </li>--}}
            <li data-name="user" class="layui-nav-item">
              <a href="javascript:;" lay-tips="个人资料" lay-direction="2">
                <i class="layui-icon layui-icon-user"></i>
                <cite>个人资料</cite>
              </a>
              <dl class="layui-nav-child">
                  <dd>
                      <a lay-href="{{ url('user/info') }}">个人资料</a>
                  </dd>
              </dl>
            </li>
          </ul>
        </div>
      </div>

      <!-- 页面标签 -->
      <div class="layadmin-pagetabs" id="LAY_app_tabs">
        <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
        <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
        <div class="layui-icon layadmin-tabs-control layui-icon-down">
          <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
            <li class="layui-nav-item" lay-unselect>
              <a href="javascript:;"></a>
              <dl class="layui-nav-child layui-anim-fadein">
                <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
              </dl>
            </li>
          </ul>
        </div>
        <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
          <ul class="layui-tab-title" id="LAY_app_tabsheader">
            {{--<li lay-id="{{ url('layui/home/console') }}" lay-attr="{{ url('layui/home/console') }}" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>--}}
            <li lay-id="{{ url('/help/index.html') }}" lay-attr="{{ url('/help/index.html') }}" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
          </ul>
        </div>
      </div>


      <!-- 主体内容 -->
      <div class="layui-body" id="LAY_app_body">
        <div class="layadmin-tabsbody-item layui-show">
          {{--<iframe src="{{ url('layui/home/console') }}" frameborder="0" class="layadmin-iframe"></iframe>--}}
          <iframe src="{{ url('/help/index.html') }}" frameborder="0" class="layadmin-iframe"></iframe>
        </div>
      </div>

      <!-- 辅助元素，一般用于移动设备下遮罩 -->
      <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
  </div>

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('static/js/custom/common.js')}}?12"></script>

  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>
  {{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>--}}

  <script>
      // 顶上切换的-每一个标签：latest_time 最新判断时间 ; tag_key 标签的key--
      // 左则的 ： tag_key 标签的key
      var RECORD_URL = '';// 当前标签的url
      var RECORD_TAG_KEY = '';// 当前标签的key
      var EXPIRE_TIME = 60;// 过期时长【单位秒】
      var SELED_CLASS = 'layui-this';// 切换时，选中状态的类名称
      // 请求模块表更新时间的接口;参数如：module_name=QualityControl\CTAPIStaff；如果为空：则不请求接口
      var GET_TABLE_UPDATE_TIME_URL = "{{ url('api/admin/ajax_getTableUpdateTime') }}";
  </script>
  <script src="{{asset('static/js/custom/layuiTagAutoRefesh.js')}}"></script>
  <script>
  layui.config({
    base: '/layui-admin-v1.2.1/src/layuiadmin/' //静态资源所在路径
  }).extend({
    index: 'lib/index' //主入口模块
  }).use('index');
  </script>
  {{--
  <!-- 百度统计 -->
  <script>
  var _hmt = _hmt || [];
  (function() {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?d214947968792b839fd669a4decaaffc";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
  })();
  </script>
  --}}
</body>
</html>


