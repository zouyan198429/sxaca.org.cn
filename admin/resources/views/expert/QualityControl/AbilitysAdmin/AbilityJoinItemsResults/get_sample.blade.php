<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>管理后台</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
{{--  <link rel="stylesheet" href="../../layuiadmin/layui/css/layui.css" media="all">--}}
{{--  <link rel="stylesheet" href="../../layuiadmin/style/admin.css" media="all">--}}
</head>
<body>
<div class="layui-fluid">
  <div class="layui-card">


        <div class="layui-card-header">
            报名单位管理
        </div>
        <div class="layui-card">
            <div class="layui-card-body">


              <table class="layui-table">
                <tbody>
                  <tr>
                    <th>报名企业</th>
                    <td>{{ $info['company_info_data']['company_name'] ?? '' }}</td>
                    <th>会员等级</th>
                    <td>{{ $info['company_info_data']['company_grade_text'] ?? '' }}</td>
                  </tr>
				  <tr>
				    <th>CMA证书号</th>
				    <td>{{ $info['company_info_data']['company_certificate_no'] ?? '' }}</td>
				    <th></th>
				    <td></td>
				  </tr>
                  <tr>
                    <th>联系人</th>
                    <td>{{ $info['contacts'] ?? '' }}</td>
                    <th>联系电话</th>
                    <td>{{ $info['mobile'] ?? '' }}({{ $info['tel'] ?? '' }})</td>
                  </tr>
                  <tr>
                    <th>报名时间</th>
                    <td>{{ $info['join_time'] ?? '' }}</td>
                    <th>取样时间</th>
                    <td>{{ $info['sample_time'] ?? '' }}</td>
                  </tr>


                </tbody>
              </table>
<!--
              <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                <legend>能力验证代码</legend>
              </fieldset>


              <table class="layui-table">
                <tbody>
                 <tr>
                    <th style="width:200px; background:#f1f1f1;">能力验证代码</th>
                    <td colspan="3"><input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="" class="layui-input"   > </td>
                  </tr>
                </tbody>
              </table> -->

              <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                <legend>取样编号</legend>
              </fieldset>
                <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
              <table class="layui-table">

                <thead>
                  <tr>
                    <th>项目<hr/>状态<hr/>取样状态</th>
                    <th>{{ $info['retry_no_text'] ?? '' }}取样编号1</th>
                    <th>{{ $info['retry_no_text'] ?? '' }}取样编号2</th>
                    <th>{{ $info['retry_no_text'] ?? '' }}取样编号3</th>
                  </tr>
                </thead>
                <tbody id="samples_list">
                  <tr >
                    <td>
                        {{ $info['ability_name'] ?? '' }}
                        <hr/>
                        {{ $info['status_text'] ?? '' }} <hr/>{{ $info['is_sample_text'] ?? '' }}
                    </td>

                    <td><input type="text" name="items_samples[]" value="{{ $info['join_items_samples_list']['0']['sample_one'] ?? '' }}" lay-verify="title" autocomplete="off" class="layui-input" onkeyup="isnum(this) " onafterpaste="isnum(this)"  ></td>
                    <td><input type="text" name="items_samples[]" value="{{ $info['join_items_samples_list']['1']['sample_one'] ?? '' }}" lay-verify="title" autocomplete="off" class="layui-input" onkeyup="isnum(this) " onafterpaste="isnum(this)"  ></td>
                    <td><input type="text" name="items_samples[]" value="{{ $info['join_items_samples_list']['2']['sample_one'] ?? '' }}" lay-verify="title" autocomplete="off" class="layui-input" onkeyup="isnum(this) " onafterpaste="isnum(this)"  ></td>
                </tr>
                </tbody>
              </table>
              <div class="layui-form-item">
                  <div class="layui-input-block">
                    <button type="button"  class="layui-btn" lay-submit="" lay-filter="demo1" id="submitBtn">立即提交</button>
{{--                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>--}}
                  </div>
                </div>
                </form>
            </div>
        </div>

  </div>
</div>

<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/expert/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results/ajax_save_sample') }}";// ajax保存取样地址
    var LIST_URL = "{{url('expert/abilitys_admin/' . ($ability_id ?? 0)  . '/ability_join_items_results')}}";//保存成功后跳转到的地址

</script>
<script src="{{ asset('/js/expert/QualityControl/AbilitysAdmin/AbilityJoinItemsResults_get_sample.js') }}?2"  type="text/javascript"></script>
</body>
</html>
