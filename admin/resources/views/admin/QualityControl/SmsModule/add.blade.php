

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
    <form class="am-form am-form-horizontal" method="post"  id="addForm"  onsubmit="return false;">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr>
                <th>模块名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="module_name" value="{{ $info['module_name'] ?? '' }}" placeholder="请输入模块名称"/>
                </td>
            </tr>
            <tr>
                <th>备注说明<span class="must">*</span></th>
                <td>
                    <textarea name="remarks" placeholder="请输入备注说明" class="layui-textarea">{{ replace_enter_char($info['remarks'] ?? '',2) }}</textarea>

                </td>
            </tr>
            <tr>
                <th>可用参数<span class="must">*</span></th>
                <td   class="staff_td">
                    <div class="table-header">
                        <button class="btn btn-danger  btn-xs ace-icon fa fa-trash-o bigger-60"  onclick="otheraction.batchDel(this, '.staff_td', 'tr')">批量删除</button>
                    </div>
                    <table class="table2">
                        <thead>
                        <tr>
                            <th style="width: 95px;">
                                <label class="pos-rel">
                                    <input type="checkbox" class="ace check_all" value="" onclick="otheraction.seledAll(this,'.table2')">
                                    <span class="lbl">全选</span>
                                </label>
{{--                                <input type="hidden" name="subject_ids[]" value="1502"/>--}}
{{--                                <input type="hidden" name="subject_history_ids[]" value="17"/>--}}
                            </th>
                            <th>参数名称</th>
                            <th>参数代码</th>
                            <th>参数类型</th>
                            <th>日期时间格式化</th>
                            <th>固定值</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="data_list   baguetteBoxOne gallery" >
                        <?php $module_params = $info['module_params'] ?? [];?>
                        @foreach ($module_params as $param_key => $param_info)
                            <tr>
                                <td >
                                    <label class="pos-rel">
                                        <input onclick="otheraction.seledSingle(this , '.table2')" type="checkbox" class="ace check_item" value="{{ $param_info['id'] ?? 0 }}">
                                        <span class="lbl"></span>
                                    </label>
                                    <input type="hidden" name="params_ids[]" value="{{ $param_info['id'] ?? 0 }}">
                                </td>
                                <td><input type="text" name="param_name[]" value="{{ $param_info['param_name'] ?? '' }}" placeholder="请输入参数名称" style="width:90px; "></td>
                                <td><input type="text" name="param_code[]" value="{{ $param_info['param_code'] ?? '' }}" placeholder="请输入参数代码" style="width:90px; "></td>
                                <td>
                                    <select class="wmini" name="param_type[]" style="width: 95px;">
                                        <option value="">参数类型</option>
                                        @foreach ($paramType as $k=>$txt)
                                            <option value="{{ $k }}"  @if(isset($param_info['param_type']) && $param_info['param_type'] == $k) selected @endif >{{ $txt }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="date_time_format[]" value="{{ $param_info['date_time_format'] ?? '' }}" placeholder="请输入日期时间格式化" style="width:90px; "></td>
                                <td><input type="text" name="fixed_val[]" value="{{ $param_info['fixed_val'] ?? '' }}" placeholder="请输入固定值" style="width:90px; "></td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.moveUp(this, 'tr')">
                                        <i class="ace-icon fa fa-arrow-up bigger-60"> 上移</i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.moveDown(this, 'tr')">
                                        <i class="ace-icon fa fa-arrow-down bigger-60"> 下移</i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.del(this, 'tr')">
                                        <i class="ace-icon fa fa-trash-o bigger-60"> 移除</i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>


                    </table>
                    <br/>
                    <button class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.addParams(this)">增加一行参数</button>
                    <p>
                       参数类型说明：<br/>
                        1、日期时间：按指定的格式，格式化发送短信时的日期时间 如：Y-m-d H:i:s 、 Y-m-d<br/>
                        2、固定值：固定值，会在发送短信时替换短信模板对应的内容<br/>
                        3、手动输入：在发送短信时，手动输入值，替换短信模板对应的内容<br/>
                        4、字段匹配：在发送短信时，系统已经预先约定好的内容，替换短信模板对应的内容。一般有开发人员设定<br/>
                    </p>
                </td>
            </tr>
            <tr>
                <th>开启状态<span class="must">*</span></th>
                <td class="sel_pay_method">
                    @foreach ($openStatus as $k=>$txt)
                        <label><input type="radio"  name="open_status"  value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>排序[降序]<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="sort_num" value="{{ $info['sort_num'] ?? '' }}" placeholder="请输入排序"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
</div>

<div style="display: none;" id="param_tr">
    <table>
        <tbody>
        <tr>
            <td >
                <label class="pos-rel">
                    <input onclick="otheraction.seledSingle(this , '.table2')" type="checkbox" class="ace check_item" value="0">
                    <span class="lbl"></span>
                </label>
                <input type="hidden" name="params_ids[]" value="0">
            </td>
            <td><input type="text" name="param_name[]" value="" placeholder="请输入参数名称" style="width:90px; "></td>
            <td><input type="text" name="param_code[]" value="" placeholder="请输入参数代码" style="width:90px; "></td>
            <td>
                <select class="wmini" name="param_type[]" style="width: 95px;">
                    <option value="">参数类型</option>
                    @foreach ($paramType as $k=>$txt)
                        <option value="{{ $k }}"  >{{ $txt }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="text" name="date_time_format[]" value="" placeholder="请输入日期时间格式化" style="width:90px; "></td>
            <td><input type="text" name="fixed_val[]" value="" placeholder="请输入固定值" style="width:90px; "></td>
            <td>
                <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.moveUp(this, 'tr')">
                    <i class="ace-icon fa fa-arrow-up bigger-60"> 上移</i>
                </a>
                <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.moveDown(this, 'tr')">
                    <i class="ace-icon fa fa-arrow-down bigger-60"> 下移</i>
                </a>
                <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.del(this, 'tr')">
                    <i class="ace-icon fa fa-trash-o bigger-60"> 移除</i>
                </a>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/sms_module/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/sms_module')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/admin/QualityControl/SmsModule_edit.js') }}?14"  type="text/javascript"></script>
</body>
</html>
