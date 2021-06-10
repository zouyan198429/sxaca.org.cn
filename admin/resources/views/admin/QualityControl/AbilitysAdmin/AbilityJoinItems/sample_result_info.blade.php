<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>数据上报---管理后台</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layuiadmin_quality/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layuiadmin_quality/style/admin.css')}}" media="all">
    <style>
    .layui-form-label {
    }
    .gray {
        color:#999;
        line-height: 160%;
    }
    </style>
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">

        <div class="layui-row layui-card-body">

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>测试结果</legend>
                </fieldset>
                @foreach ($info['join_item_reslut_info_updata']['items_samples_list'] as $k => $sample_info)
                <div class="layui-form-item">
                    <label class="layui-form-label">样品编号： </label>
                    <div class="layui-input-block sample_list" data-sample_one="{{ $sample_info['sample_one'] ?? '' }}">
                       编号 {{ $sample_info['sample_one'] ?? '' }}
                    {{-- 样品id _需要收集项目的数据项目的数据类型id --}}
                        <?php $sample_result_list = $sample_info['sample_result_list'] ?>
                        @foreach ($info['project_submit_items_list'] as $t_k => $submit_info)
                            <?php $key = ($sample_info['id'] ?? '') . '_' . ($submit_info['id'] ?? '') ?>
                            {{ $submit_info['name'] ?? '' }}：{{ $sample_result_list[$key]['sample_result'] ?? '' }}
                        @endforeach
                    </div>
                </div>
                @endforeach

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>检测所用仪器</legend>
                </fieldset>
                @foreach ($info['join_item_reslut_info_updata']['results_instrument_list'] as $k => $instrument_info)
                    <div class="instrument_list">
                        <div class="layui-form-item ">
                            <label class="layui-form-label">名称/型号</label>
                            <div class="layui-input-block">
                                {{ $instrument_info['instrument_model'] ?? '' }}
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">出厂编号</label>
                            <div class="layui-input-block">
                                {{ $instrument_info['factory_number'] ?? '' }}
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">检定日期</label>
                            <div class="layui-input-block">
                                {{ $instrument_info['check_date'] ?? '' }}
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">有效期</label>
                            <div class="layui-input-block">
                                {{ $instrument_info['valid_date'] ?? '' }}
                            </div>
                        </div>
                    </div>
                @endforeach

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>标准物质</legend>
                </fieldset>
                @foreach ($info['join_item_reslut_info_updata']['results_standard_list'] as $k => $standard_info)
                    <div class="standard_list">
                        <div class="layui-form-item">
                            <label class="layui-form-label">名称</label>
                            <div class="layui-input-block">
                                {{ $standard_info['name'] ?? '' }}
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">生产单位</label>
                            <div class="layui-input-block">
                                {{ $standard_info['produce_unit'] ?? '' }}
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">批号</label>
                            <div class="layui-input-block">
                                {{ $standard_info['batch_number'] ?? '' }}
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">有效期</label>
                            <div class="layui-input-block">
                                {{ $standard_info['valid_date'] ?? '' }}
                            </div>
                        </div>
                    </div>
                @endforeach

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>方法依据</legend>
                </fieldset>
                @foreach ($info['join_item_reslut_info_updata']['results_method_list'] as $k => $method_info)
                    <div class="method_list">
                        <div class="layui-form-item">
                            <label class="layui-form-label"></label>
                            <div class="layui-input-block">

                                {!! $method_info['content'] ?? '' !!}
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="layui-form-item">
                    <label class="layui-form-label">上传资料</label>
                    <div class="layui-input-block">
                        <?php
                        $resource_type = $info['join_item_reslut_info_updata']['resource_type'] ?? 1;
                        ?>
                        @if (isset($resource_type) && $resource_type == 1 )
                            <div class="row  baguetteBoxOne gallery ">
                                @foreach ($info['join_item_reslut_info_updata']['resource_list'] as $k => $resource_info)
                                    <a href="{{ $resource_info['resource_url'] ?? '' }}">
                                        <img src="{{ $resource_info['resource_url'] ?? '' }}" alt="{{ $resource_info['resource_name'] ?? '' }}"  style="width: 100px;" title="{{ $resource_info['resource_name'] ?? '' }}"/>
                                    </a>
                                @endforeach
                            </div>
                        @else


                            <div class="row">

                                @foreach ($info['join_item_reslut_info_updata']['resource_list'] as $k => $resource_info)
                                    {{ $resource_info['resource_name'] ?? '' }}
                                    <a href="{{ $resource_info['resource_url_format'] ?? '' }}" class="btn btn-mini btn-success"   target='_blank'>
                                        <i class="ace-icon fa fa-eye bigger-60"> 查看</i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-mini btn-success"  onclick="otheraction.down_file('{{ $resource_info["resource_url_old"] ?? "" }}')">
                                        <i class="ace-icon fa fa-cloud-download bigger-60"> 下载</i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
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
    // var SAVE_URL = "{{ url('api/admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items/ajax_save_result_sample') }}";// ajax保存提交数据地址
    var LIST_URL = "{{url('admin/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items')}}";//保存成功后跳转到的地址
    var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载网页打印机驱动

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
<!--{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}-->

<script src="{{ asset('/js/admin/QualityControl/AbilitysAdmin/AbilityJoinItems_sample_result_info.js') }}?13"  type="text/javascript"></script>

</body>
</html>
