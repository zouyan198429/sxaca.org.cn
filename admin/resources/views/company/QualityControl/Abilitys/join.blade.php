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

            <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="ids" value="{{ $ids ?? '' }}"/>

                @foreach ($data_list as $k => $info)
                <fieldset class="layui-elem-field layui-field-title">
                    <legend>项目</legend>
                </fieldset>
                <div class="layui-form-item">
                    <label class="layui-form-label">检测项目</label>
                    <div class="layui-form-mid layui-word-aux">
                        <span id="ability_name_{{ $info['id'] ?? '' }}">{{ $info['ability_name'] ?? '' }}</span>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label ">方法标准</label>
                    <div class="layui-input-block " id="project_standard_{{ $info['id'] ?? '' }}">
                        @foreach ($info['project_standards'] as $k_p => $p_info)
                          <label><input type="checkbox" name="project_standard_id_{{ $info['id'] ?? '' }}[]" value="{{ $p_info['id'] ?? '' }}">{{ $p_info['tag_name'] ?? '' }}</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <br>
                        @endforeach
                         <label><input type="checkbox" class="otherCheckbox" name="project_standard_id_{{ $info['id'] ?? '' }}[]" value="0">其他</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <br>
                        <textarea name="project_standard_name_{{ $info['id'] ?? '' }}" class="project_standard_name" cols="50" rows="4" readonly="readonly"></textarea>
                    </div>
                </div>
{{--                    @if ($data_num > ($k + 1) )<hr>@endif--}}
                @endforeach

                <fieldset class="layui-elem-field layui-field-title">
                    <legend>联系人</legend>
                </fieldset>
                @if(isset($joinInfo['contacts']) )
                <p class="gray">您已有报名【{{ $joinInfo['ability_code'] ?? '' }}】，此次报名会合并到已报名记录中，以下是已有的联系方法，您可以修改！</p>
                @endif
                <div class="layui-form-item">
                    <label class="layui-form-label">姓名</label>
                    <div class="layui-input-block">
                        <input type="text" name="contacts" value="{{ $joinInfo['contacts'] ?? '' }}"  lay-verify="title" autocomplete="off" placeholder="请输入姓名" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">手机</label>
                    <div class="layui-input-block">
                        <input type="text" name="mobile" value="{{ $joinInfo['mobile'] ?? '' }}"  lay-verify="title" autocomplete="off" placeholder="请输入手机" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">联系电话</label>
                    <div class="layui-input-block">
                        <input type="text" name="tel" value="{{ $joinInfo['tel'] ?? '' }}"  lay-verify="title" autocomplete="off" placeholder="请输入电话" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="btn btn-l wnormal"  id="submitBtn" >提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{asset('laydate/laydate.js')}}"></script>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')
<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/company/abilitys/ajax_new_join_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/abilitys')}}";//保存成功后跳转到的地址

</script>
<script src="{{ asset('/js/company/QualityControl/Abilitys_join.js?208') }}"  type="text/javascript"></script>
</body>
</html>

