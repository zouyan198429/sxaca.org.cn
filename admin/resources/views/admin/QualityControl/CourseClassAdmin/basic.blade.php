<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>帮助中心</title>
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
                        <li class="layui-col-xs3">
                            <a  class="layadmin-backlog-body">
                                <h3>培训班名称</h3>
                                <p><cite>{{ $info['class_name'] ?? '' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs3">
                            <a  class="layadmin-backlog-body">
                                <h3>所属课程</h3>
                                <p><cite>{{ $info['course_name'] ?? '' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs3">
                            <a  class="layadmin-backlog-body">
                                <h3>开班城市</h3>
                                <p><cite>{{ $info['city_name'] ?? '' }}</cite></p>
                            </a>
                        </li>

                        <li class="layui-col-xs3">
                            <a class="layadmin-backlog-body">
                                <h3>班级人数</h3>
                                <p><cite>{{ $info['join_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>



            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-form-mid layui-word-aux">
                    {!! $info['remarks'] ?? '' !!}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">收款帐号</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['pay_company_name'] ?? '' }}
                    <p>注：为空，则以课程配置的收款帐号为收款帐号。</p>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">收款方式</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['pay_method_text'] ?? '' }}
                    <p>注：为空，则以课程配置的收款方式为收款方式。</p>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">班级状态</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['class_status_text'] ?? '' }}
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
</script>

</body>
</html>
