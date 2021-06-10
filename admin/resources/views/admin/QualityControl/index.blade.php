<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>大后台 -{{ config('public.webName') }}</title>
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
              <dd><a lay-href="{{ url('admin/info') }}">基本资料</a></dd>
              <dd><a lay-href="{{ url('admin/password') }}">修改密码</a></dd>
              <hr>
              <dd  style="text-align: center;"><a href="{{ url('admin/logout') }}">退出</a></dd>
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
            <span>质量认证认可协会-大后台</span>
          </div>

          <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">


              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="信息审核" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>信息审核</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('admin/company') }}?open_status=1">新注册单位审核</a>
                      </dd>
                        <dd>
                            <a lay-href="{{ url('admin/user') }}?open_status=1">新注册个人审核</a>
                        </dd>
                      <dd>
                          <a lay-href="{{ url('admin/user') }}?role_num=8&sign_status=1">授权签字人审核</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/user') }}?role_status=1">人员角色审核</a>
                      </dd>
{{--                      <dd>--}}
{{--                          <a lay-href="javascript:void(01);">证书审核</a>--}}
{{--                      </dd>--}}

                  </dl>
              </li>
            <li data-name="user" class="layui-nav-item">
              <a href="javascript:;" lay-tips="会员管理" lay-direction="21">
                <i class="layui-icon layui-icon-component"></i>
                <cite>会员管理</cite>
              </a>
                <dl class="layui-nav-child">
                    <dd>
                        <a lay-href="{{ url('admin/company') }}">所有单位</a>
                    </dd>
                    <dd>
                        <a lay-href="{{ url('admin/company') }}?company_grade=1&open_status=2">非会员单位</a>
                    </dd>
                    <dd>
                        <a lay-href="{{ url('admin/company') }}?company_grade=2">会员单位</a>
                    </dd>
                    <dd>
                        <a lay-href="{{ url('admin/company') }}?company_grade=4">理事单位</a>
                    </dd>
                    <dd>
                        <a lay-href="{{ url('admin/company') }}?company_grade=8">常务理事单位</a>
                    </dd>
                    <dd>
                        <a lay-href="{{ url('admin/company') }}?company_grade=16">理事长单位</a>
                    </dd>
                    <dd>
                      <a lay-href="{{ url('admin/user') }}">个人会员</a>
                    </dd>
                </dl>
            </li>
			<li data-name="user" class="layui-nav-item">
			    <a href="javascript:;" lay-tips="会员分析" lay-direction="22">
			        <i class="layui-icon layui-icon-component"></i>
			        <cite>会员分析</cite>
			    </a>
			    <dl class="layui-nav-child">
			        <dd><a lay-href="{{ url('admin/company/grade_area') }}">会员地区分布</a></dd>
			        <dd><a lay-href="{{ url('admin/company/grade_industry') }}">会员行业分布</a></dd>
			        <dd><a lay-href="{{ url('admin/company') }}?record_type=2">快到期会员(所有)</a></dd>
					<dd><a lay-href="{{ url('admin/company') }}?record_type=2&company_grade_continue=1">快到期会员(无续期)</a></dd>
			        <dd><a lay-href="{{ url('admin/company') }}?record_type=2&company_grade_continue=2">快到期会员(有续期)</a></dd>
					<dd><a lay-href="{{ url('admin/company') }}?record_type=4">已过期会员</a></dd>
					<dd><a lay-href="{{ url('admin/company') }}?record_type=4&company_grade_continue=1">已过期会员(无续期)</a></dd>
					<dd><a lay-href="{{ url('admin/company') }}?record_type=4&company_grade_continue=2">已过期会员(有续期)</a></dd>
			    </dl>
			</li>




            <li data-name="user" class="layui-nav-item"  style="display: block;">
                  <a href="javascript:;" lay-tips="面授培训" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>面授培训</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('admin/course') }}">课程管理</a>
                      </dd>
{{--                      <dd>--}}
{{--                          <a lay-href="{{ url('admin/course_order') }}">报名企业</a>--}}
{{--                      </dd>--}}
{{--                      <dd>--}}
{{--                          <a lay-href="{{ url('admin/course_order_staff') }}">报名学员</a>--}}
{{--                      </dd>--}}
{{--                      <dd>--}}
{{--                          <a lay-href="{{ url('admin/course_class') }}">培训班管理</a>--}}
{{--                      </dd>--}}
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item" style="display: block;">
                  <a href="javascript:;" lay-tips="视频课程" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>视频课程</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('admin/vod_type') }}">课程分类</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/vods') }}">录播课程</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/vod_video') }}">课程课件</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/vod_orders') }}">购买管理</a>
                      </dd>
{{--                      <dd>--}}
{{--                          <a lay-href="javascript:void(32);">评论管理</a>--}}
{{--                      </dd>--}}
{{--                      <dd>--}}
{{--                          <a lay-href="javascript:void(33);">分析报告</a>--}}
{{--                      </dd>--}}
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="在线直播" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>在线直播</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('admin/live_notice') }}">直播公告</a>
                      </dd>
                      {{--                      <dd>--}}
                      {{--                          <a lay-href="javascript:void(20);">直播课</a>--}}
                      {{--                      </dd>--}}
                      {{--                      <dd>--}}
                      {{--                          <a lay-href="javascript:void(21);">评论管理</a>--}}
                      {{--                      </dd>--}}
                  </dl>
              </li>
              <!--
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="考试管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>考试管理</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="javascript:void(40);">试题</a>
                      </dd>
                      <dd>
                          <a lay-href="javascript:void(41);">试卷</a>
                      </dd>
                      <dd>
                          <a lay-href="javascript:void(42);">考试</a>
                      </dd>
                      <dd>
                          <a lay-href="javascript:void(43);">试题分类</a>
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
                          <a lay-href="{{ url('admin/abilitys') }}">项目管理</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/ability_join') }}">用户报名</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/company_ability') }}">结果导入</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/ability_code') }}">证书设置</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/company_new_schedule') }}">能力附表[id降序]</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/company_new_schedule/list') }}">能力附表[企业id降序]</a>
                      </dd>
{{--                      <dd>--}}
{{--                          <a lay-href="{{ url('admin/ability_type') }}?company_grade=4">领域管理</a>--}}
{{--                      </dd>--}}
                  </dl>
              </li>
			  <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="开票管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>获证机构管理</cite>
                  </a>
                  <dl class="layui-nav-child">
{{--                      <dd>--}}
{{--                          <a lay-href="{{ url('admin/certificate') }}">证书</a>--}}
{{--                      </dd>--}}
                      <dd>
                          <a lay-href="{{ url('admin/certificate_schedule') }}">能力范围</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/certificate_import_log') }}">导入批次</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/laboratory_addr') }}">实验室地址</a>
                      </dd>
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="检验机构信息管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>检验机构信息管理</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('admin/platform_notices') }}">通知公告</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/platform_down_files') }}">表格下载</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/company_statement') }}">机构自我声明</a>
                      </dd>

                      <dd>
                          <a lay-href="{{ url('admin/company_inspect') }}">监督检查</a>
                      </dd>
					  <dd>
					      <a lay-href="{{ url('admin/company_punish') }}">机构处罚</a>
					  </dd>
                      <dd>
                          <a lay-href="{{ url('admin/company_news') }}">其它</a>
                      </dd>
                  </dl>
              </li>

              <li data-name="user" class="layui-nav-item" style="display: block;">
                  <a href="javascript:;" lay-tips="财务及订单管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>财务及订单管理</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd class="layui-nav-itemed">
                          <a href="javascript:;">订单管理</a>
                          <dl class="layui-nav-child">
                              <dd>
                                  <a lay-href="{{ url('admin/orders') }}">订单管理</a>
                              </dd>
                              <dd>
                                  <a lay-href="{{ url('admin/order_pay') }}">在线支付明细【对帐】</a>
                              </dd>
                              <dd>
                                  <a lay-href="{{ url('admin/order_flow') }}">财务明细[流水帐]</a>
                              </dd>
                              <dd>
                                  <a lay-href="{{ url('admin/invoices') }}">电子发票</a>
                              </dd>
                          </dl>
                      </dd>
                      <dd class="layui-nav-itemed">
                          <a href="javascript:;">收款设置</a>
                          <dl class="layui-nav-child">
                              <dd><a lay-href="{{ url('admin/order_pay_method') }}">收款方式管理</a></dd>
                          </dl>
                          <dl class="layui-nav-child">
                              <dd><a lay-href="{{ url('admin/order_pay_config') }}">收款帐号列表</a></dd>
                          </dl>
                      </dd>
                      <dd class="layui-nav-itemed">
                          <a href="javascript:;">电子发票设置</a>
                          <dl class="layui-nav-child">
                              <dd><a lay-href="{{ url('admin/invoice_template') }}">开票模板管理</a></dd>
                          </dl>
                          <dl class="layui-nav-child">
                              <dd><a lay-href="{{ url('admin/invoice_project_template') }}">发票项目模板</a></dd>
                          </dl>
                      </dd>
                  </dl>
              </li>
              <!-- <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="开票管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>开票管理</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="javascript:void(60);">开票申请</a>
                      </dd>
                      <dd>
                          <a lay-href="javascript:void(61);">订单流水</a>
                      </dd>
                      <dd>
                          <a lay-href="javascript:void(62);">支付设置</a>
                      </dd>
                      <dd>
                          <a lay-href="javascript:void(63);">财务分析</a>
                      </dd>
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="内容管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>内容管理</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="javascript:void(60);">单页管理</a>
                      </dd>
                      <dd>
                          <a lay-href="javascript:void(61);">文章管理</a>
                      </dd>
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="消息公告" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>消息公告</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="javascript:void(70);">消息公告</a>
                      </dd>
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="广告及链接" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>广告及链接</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="javascript:void(80);">广告</a>
                      </dd>
                      <dd>
                          <a lay-href="javascript:void(81);">链接</a>
                      </dd>
                  </dl>
              </li> -->
              <li data-name="user" class="layui-nav-item" style="display: none;">
                  <a href="javascript:;" lay-tips="收款管理" lay-direction="2">
                      <i class="layui-icon layui-icon-component"></i>
                      <cite>收款管理</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('admin/payment_type') }}">付款/收款类型</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/payment_project') }}">付款/收款项目</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/payment_record') }}">付款/收款记录</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/payment_record_flow') }}">付款/收款记录流水</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/payment_record_log') }}">付款/收款记录操作日志</a>
                      </dd>
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="应用接口管理" lay-direction="2">
                      <i class="layui-icon layui-icon-user"></i>
                      <cite>应用接口管理</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd >
                          <a lay-href="{{ url('admin/third_service') }}">数据对接商管理</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/apply') }}">应用管理</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/api_log') }}">API日志</a>
                      </dd>
                  </dl>
              </li>
              <li data-name="user" class="layui-nav-item">
                  <a href="javascript:;" lay-tips="短信管理" lay-direction="2">
                      <i class="layui-icon layui-icon-user"></i>
                      <cite>短信管理</cite>
                  </a>
                  <dl class="layui-nav-child">
                      <dd>
                          <a lay-href="{{ url('admin/sms_module_params_common') }}">短信常用参数管理</a>
                      </dd>
{{--                      <dd>--}}
{{--                          <a lay-href="{{ url('admin/sms_limit') }}">短信限次配置</a>--}}
{{--                      </dd>--}}
                      <dd>
                          <a lay-href="{{ url('admin/sms_module') }}">短信模块管理</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/sms_template') }}">短信模板管理</a>
                      </dd>
                      <dd>
                          <a lay-href="{{ url('admin/sms_log') }}">短信日志管理</a>
                      </dd>
                  </dl>
              </li>
            <li data-name="user" class="layui-nav-item">
              <a href="javascript:;" lay-tips="基础设置" lay-direction="2">
                <i class="layui-icon layui-icon-user"></i>
                <cite>基础设置</cite>
              </a>
              <dl class="layui-nav-child">
                 <!--  <dd>
                      <a lay-href="javascript:void(90);">基本设置</a>
                  </dd> -->
                <dd>
                  <a lay-href="{{ url('admin/staff') }}">管理员管理</a>
                </dd>
                  <dd>
                      <a lay-href="{{ url('admin/expert') }}">数据查看人员管理</a>
                  </dd>
                  <!-- <dd>
                      <a lay-href="javascript:void(91);">角色权限</a>
                  </dd> -->
                  <!-- <dd>
                      <a lay-href="javascript:void(92);">会员级别</a>
                  </dd> -->
                  <!-- <dd>
                      <a lay-href="javascript:void(93);">短信模板</a>
                  </dd>  -->
                  <dd>
                      <a lay-href="{{ url('admin/industry') }}">企业行业管理</a>
                  </dd>
                  <dd>
                      <a lay-href="{{ url('admin/citys') }}">城市管理</a>
                  </dd>
                  <dd>
                      <a lay-href="{{ url('admin/company_certificate_type') }}">资质证书类型</a>
                  </dd>
                  <dd>
                      <a lay-href="{{ url('admin/sms_code') }}">短信验证码日志管理</a>
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


