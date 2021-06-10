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
                                <h3>报名单位</h3>
                                <p><cite>{{ $info['join_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs3">
                            <a  class="layadmin-backlog-body">
                                <h3>提交数据</h3>
                                <p><cite>{{ $info['first_submit_num'] + $info['repair_submit_num'] }}</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs3">
                            <a  class="layadmin-backlog-body">
                                <h3>补测单位</h3>
                                <p><cite>{{ $info['repair_num'] ?? '0' }}</cite></p>
                            </a>
                        </li>

                        <li class="layui-col-xs3">
                            <a class="layadmin-backlog-body">
                                <h3>合格数量</h3>
                                <p><cite>{{ $info['first_success_num'] + $info['repair_success_num'] }}</cite></p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>



            <div class="layui-form-item">
                <label class="layui-form-label">项目领域</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['ability_name'] ?? '' }}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">检测参数</label>
                <div class="layui-form-mid layui-word-aux">
                    {!! $info['submit_items_text'] ?? '' !!}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">项目标准</label>
                <div class="layui-form-mid layui-word-aux">
                    {!! $info['project_standards_text'] ?? '' !!}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">预估参加实验室数</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['estimate_add_num'] ?? '' }}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">报名时间</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['join_begin_date'] ?? '' }} ---  {{ $info['join_end_date'] ?? '' }}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">当前状态</label>
                <div class="layui-form-mid layui-word-aux">
                    {{ $info['status_text'] ?? '' }}
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
