

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr>
                <th>过期配置名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="industry_name" value="{{ $info['industry_name'] ?? '' }}" placeholder="请输入过期配置名称"/>
                </td>
            </tr>
            <tr>
                <th>年<span class="must">*</span></th>
                <td>
                    @foreach ($yearNum as $k=>$txt)
                        <label><input type="radio"  name="year_num"  value="{{ $k }}"  @if(isset($defaultYearNum) && $defaultYearNum == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>月<span class="must">*</span></th>
                <td>
                    @foreach ($monthNum as $k=>$txt)
                        <label><input type="radio"  name="month_num"  value="{{ $k }}"  @if(isset($defaultMonthNum) && $defaultMonthNum == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>日<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="day_num" style="width: 100px;">
                        <option value="">请选择秒</option>
                        @foreach ($dayNum as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultDayNum) && $defaultDayNum == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>时<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="hour_num" style="width: 100px;">
                        <option value="">请选择秒</option>
                        @foreach ($hourNum as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultHourNum) && $defaultHourNum == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>分钟<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="min_num" style="width: 100px;">
                        <option value="">请选择分钟</option>
                        @foreach ($minNum as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultMinNum) && $defaultMinNum == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>秒<span class="must">*</span></th>
                <td>
                    <select class="wnormal" name="sec_num" style="width: 100px;">
                        <option value="">请选择秒</option>
                        @foreach ($secNum as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultSecNum) && $defaultSecNum == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/company_expire/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/company_expire')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/admin/QualityControl/CompanyExpire_edit.js') }}?1"  type="text/javascript"></script>
</body>
</html>
