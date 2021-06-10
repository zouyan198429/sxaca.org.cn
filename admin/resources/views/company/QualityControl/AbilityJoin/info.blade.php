<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>后台管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">

</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">
 
        <div class="layui-row layui-card-body">
                <?php
                $data_num = count($info['join_items']);
                ?>
                @foreach ($info['join_items'] as $k => $item_info)
                <fieldset class="layui-elem-field layui-field-title">
                    <legend>项目</legend>
                </fieldset>
                <div class="layui-form-item">
                    <label class="layui-form-label">检测项目</label>
                    <div class="layui-form-mid layui-word-aux">
                        {{ $item_info['ability_name'] ?? '' }}
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label ">方法标准</label>
                    <div class="layui-input-block " >
                        @foreach ($item_info['project_standards'] as $k_p => $p_info)
                          <label><input type="checkbox" disabled name="project_standard_id_{{ $info['id'] ?? '' }}[]" value="{{ $p_info['id'] ?? '' }}" @if(in_array($p_info['id'], $item_info['join_item_standard_ids'])) checked @endif  >{{ $p_info['tag_name'] ?? '' }}</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <br>
                        @endforeach
                         <label><input type="checkbox" disabled  name="project_standard_id_{{ $info['id'] ?? '' }}[]" value="0"  @if(in_array(0, $item_info['join_item_standard_ids'])) checked @endif>其他</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <br>
                        <textarea name="project_standard_name_{{ $info['id'] ?? '' }}" disabled id="" cols="50" rows="4"> @if(in_array(0, $item_info['join_item_standard_ids'])) {{ replace_enter_char($item_info['join_item_standards']['0']['project_standard_name'] ?? '',2) }}  @endif</textarea>
                    </div>
                </div>
                @if ($data_num > ($k + 1) )<hr>@endif
                @endforeach

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>联系人</legend>
                </fieldset>
                <div class="layui-form-item">
                    <label class="layui-form-label">姓名</label>
                    <div class="layui-input-block">
                        {{ $info['contacts'] ?? '' }}
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">手机</label>
                    <div class="layui-input-block">
                        {{ $info['mobile'] ?? '' }}
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">联系电话</label>
                    <div class="layui-input-block">
                        {{ $info['tel'] ?? '' }}
                    </div>
                </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{asset('laydate/laydate.js')}}"></script>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')
<script type="text/javascript">

</script>
<script src="{{ asset('/js/company/QualityControl/AbilityJoin_info.js?18') }}"  type="text/javascript"></script>
</body>
</html>

