<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>帮助中心</title>
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">

        <div class="layui-card-body">
            <div class="layui-carousel layadmin-carousel layadmin-backlog" lay-anim="" lay-arrow="none" style="width: 100%;">
                <div carousel-item="">
                    <ul class="layui-row layui-col-space10 layui-this">
                        <li class="layui-col-xs2">
                            <a  class="layadmin-backlog-body">
                                <h3>报名人数</h3>
                                <p><cite>{{ $info['join_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs2">
                            <a  class="layadmin-backlog-body">
                                <h3>报名池人数</h3>
                                <p><cite>{{ $info['wait_class_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs2">
                            <a  class="layadmin-backlog-body">
                                <h3>已分班人数</h3>
                                <p><cite>{{ $info['joined_class_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs2">
                            <a  class="layadmin-backlog-body">
                                <h3>已结业人数</h3>
                                <p><cite>{{ $info['finish_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs2">
                            <a class="layadmin-backlog-body">
                                <h3>已作废人数</h3>
                                <p><cite>{{ $info['cancel_num']  ?? '0' }}</cite></p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

		<style>
			.layui-form-label {
				width: 180px;
			}
		</style>

            <div class="layui-form-item">
                <label class="layui-form-label">课程名称</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['course_name'] ?? '' }}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">简要概述</label>
                <div class="layui-form-mid layui-word-aux">
                    {!! $info['explain_remarks'] ?? '' !!}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">收费标准(会员)</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['price_member'] ?? ''  }}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">收费标准(非会员)</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['price_general'] ?? '' }}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">收款帐号</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['pay_company_name'] ?? '' }}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">收款开通类型</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['pay_method_text'] ?? '' }}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">当前状态</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['status_online_text'] ?? '' }}
                </div>
            </div>
          <!--  <div class="layui-form-item baguetteBoxOne gallery"  id="resource_block">
                <label class="layui-form-label">图片资源</label>
                <div class="layui-form-mid layui-word-aux">
                    <span class="resource_list"  style="display: none;">@json($info['resource_list'] ?? [])</span>
                    <span  class="resource_show"></span>
                </div>
            </div> -->
            <div class="layui-form-item">
                <label class="layui-form-label">课程介绍</label>
                <div class="layui-form-mid layui-word-aux">
                    {!! $info['course_content'] ?? '' !!}
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

    var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载
    var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

    // 列表数据每隔指定时间就去执行一次刷新【如果表有更新时】--定时执行
    var IFRAME_TAG_KEY = "";// "QualityControl\\CTAPIStaff";// 获得模型表更新时间的关键标签，可为空：不获取
      var IFRAME_TAG_TIMEOUT = 60000;// 获得模型表更新时间运行间隔 1000:1秒 ；可以不要此变量：默认一分钟

      // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
      // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
      var PARENT_BUSINESS_FUN_NAME = "adminQualityControlRrrDdddedit";

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('js/admin/QualityControl/Course/Basic.js') }}?7"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
