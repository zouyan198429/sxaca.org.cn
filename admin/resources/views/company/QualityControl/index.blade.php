<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ config('public.webName') }}</title>
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

          <li class="layui-nav-item" lay-unselect>
            <a href="javascript:;" layadmin-event="refresh" title="刷新">
              <i class="layui-icon layui-icon-refresh-3"></i>
            </a>
          </li>
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
{{--              <dd><a lay-href="{{ url('company/info') }}">基本资料</a></dd>--}}
              <dd><a lay-href="{{ url('company/password') }}">修改密码</a></dd>
              <hr>
              <dd  style="text-align: center;"><a href="{{ url('company/logout') }}">退出</a></dd>
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
            <span>企业管理后台</span>
          </div>

          <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">


          <li data-name="user" class="layui-nav-item">
              <a href="javascript:;" lay-tips="面授培训" lay-direction="2">
                <i class="layui-icon layui-icon-component"></i>
                <cite>面授培训</cite>
              </a>
                <dl class="layui-nav-child">
                    <dd>
                        <a lay-href="{{ url('company/course') }}">面授培训</a>
                    </dd>
                </dl>
                <dl class="layui-nav-child">
                    <dd>
                        <a lay-href="{{ url('company/course_order') }}">我的报名</a>
                    </dd>
                </dl>
            </li>
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="在线课程" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>在线课程</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('company/vods') }}">在线课程</a>
                      </dd>
                  </dl>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('company/vod_orders') }}">我的课程</a>
                      </dd>
                  </dl>
              </li>
              <!--
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="在线考试" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>在线考试</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="javascript:void(30);">在线考试</a>
                      </dd>
                  </dl>
              </li> -->
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="能力验证" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>能力验证</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('company/abilitys') }}">能力验证</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('company/ability_join_item') }}">已报名项目</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('/download/nengli.html') }}">资料下载</a>
                      </dd>
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="员工管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>员工管理</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('company/user') }}">员工管理</a>
                      </dd>
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item" style="display: block;">
                  <a href="javascript:;" lay-tips="订单管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>订单管理</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('company/invoice_buyer') }}">电子发票抬头</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('company/orders') }}">订单管理</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('company/order_pay') }}">在线支付明细【对帐】</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('company/order_flow') }}">财务明细[流水帐]</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('company/invoices') }}">电子发票</a>
                      </dd>
                  </dl>
              </li>
              <!-- <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="发票管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>发票管理</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="javascript:void(60);">发票管理</a>
                      </dd>
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="发票管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>消息中心</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="javascript:void(70);">消息中心</a>
                      </dd>
                  </dl>
              </li> -->
            <li data-name="user" class="layui-nav-item">
              <a href="javascript:;" lay-tips="企业帐号" lay-direction="2">
                <i class="layui-icon layui-icon-user"></i>
                <cite>企业帐号</cite>
              </a>
              <dl class="layui-nav-child">
                <dd>
					  <a lay-href="{{ url('company/basic') }}">基本信息</a>
                </dd>
{{--                  <dd>--}}
{{--                      <a lay-href="javascript:void(81);">企业简介</a>--}}
{{--                  </dd>--}}
                  <dd>
                      <a lay-href="{{ url('company/company_new_schedule') }}">能力附表</a>
                  </dd>
                  <dd>
                      <a lay-href="{{ url('company/company_content/basic/0') }}">企业简介</a>
                  </dd>
<!--                   <dd>
                      <a lay-href="javascript:void(83);">开票信息</a>
                  </dd>
                  <dd>
                      <a lay-href="javascript:void(84);">帐号安全</a>
                  </dd>
                  <dd>
                      <a lay-href="javascript:void(85);">资质证书</a>
                  </dd>
                  <dd>
                      <a lay-href="javascript:void(86);">我的会籍</a>
                  </dd> -->
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
            <li lay-id="{{ url('/help/index.html') }}" lay-attr="{{ url('/help/index.html') }}" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
          </ul>
        </div>
      </div>



      <!-- 主体内容 -->
      <div class="layui-body" id="LAY_app_body">
        <div class="layadmin-tabsbody-item layui-show">
          <iframe src="/help/index.html" frameborder="0" class="layadmin-iframe"></iframe>
        </div>
      </div>

      <!-- 辅助元素，一般用于移动设备下遮罩 -->
      <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
  </div>

  <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
  <script src="{{asset('static/js/custom/common.js')}}?12"></script>

  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>

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

</body>
</html>


