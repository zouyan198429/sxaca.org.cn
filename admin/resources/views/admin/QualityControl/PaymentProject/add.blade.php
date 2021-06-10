

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
    {{--  本页单独使用 --}}
    <script src="{{asset('dist/lib/kindeditor/kindeditor.min.js')}}"></script>
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr  @if (isset($hidden_option) && ($hidden_option & 1) == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must">*</span></th>
                <td>
                    <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                    <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                    <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择所属企业</button>
                </td>
            </tr>
            <tr>
                <th>付款/收款分类<span class="must">*</span></th>
                <td  class="seledTypeNo">
                    @foreach ($type_no_kv as $k => $txt)
                        <label><input type="checkbox"  name="type_no[]"  value="{{ $k }}"  @if(isset($defaultTypeNo) && (($defaultTypeNo & $k) == $k)) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>收费名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="title" value="{{ $info['title'] ?? '' }}" placeholder="请输入收费名称"/>
                </td>
            </tr>
            <tr>
                <th>收款说明图片<span class="must">*</span></th>
                <td>
                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>一次最多上传1张图片。</p>
                    </div>
                    <div class="row  baguetteBoxOne gallery ">
                        <div class="col-xs-6">
                            @component('component.upfileone.piconecode')
                                @slot('fileList')
                                    large
                                @endslot
                                @slot('upload_id')
                                    myUploaderLarge
                                @endslot
                                @slot('upload_url')
                                    {{ url('api/admin/upload') }}
                                @endslot
                            @endcomponent
                            {{--
                            <input type="file" class="form-control" value="">
                            --}}
                        </div>
                    </div>
                    <p>【收款说明图片】或【收款文字说明】二者必填其一</p>

                </td>
            </tr>
            <tr>
                <th>收款文字说明<span class="must">*</span></th>
                <td>
                    <textarea class="kindeditor" name="pay_explain" rows="15" id="doc-ta-1" style=" width:770px;height:400px;">{!!  htmlspecialchars($info['pay_explain'] ?? '' )   !!}</textarea>

                </td>
            </tr>
            <tr>
                <th>是否指定金额<span class="must">*</span></th>
                <td class="sel_specified_amount_status">
                    @foreach ($specifiedAmountStatus as $k=>$txt)
                        <label><input type="radio"  name="specified_amount_status"  value="{{ $k }}"  @if(isset($defaultSpecifiedAmountStatus) && $defaultSpecifiedAmountStatus == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr  class="tr_pay_amount">
                <th>收费金额<span class="must">*</span></th>
                <td>
                    ¥<input type="text" class="inp wnormal"  name="pay_amount" value="{{ $info['pay_amount'] ?? '' }}" placeholder="请输入收费金额"  onkeyup="numxs(this) " onafterpaste="numxs(this)" />

                </td>
            </tr>
            <tr>
                <th>收费生效时间<span class="must">*</span></th>
                <td class="sel_pay_valid_status">
                    @foreach ($payValidStatus as $k=>$txt)
                        <label><input type="radio"  name="pay_valid_status"  value="{{ $k }}"  @if(isset($defaultPayValidStatus) && $defaultPayValidStatus == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                        <p>1、长期有效：启用后随时可以进行付款操作！</p>
                        <p>2、指定时间：仅在指定时间内，可以进行付款操作！</p>
                </td>
            </tr>
            <tr class="tr_pay_time">
                <th>收费起止时间<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wlong pay_begin_time" name="pay_begin_time" value="{{ $info['pay_begin_time'] ?? '' }}" placeholder="请选择收费开始时间" style="width: 150px;"  readonly="true"/>
                    -
                    <input type="text" class="inp wlong pay_end_time" name="pay_end_time" value="{{ $info['pay_end_time'] ?? '' }}" placeholder="请选择收费结束时间"  style="width: 150px;" readonly="true"/>

                </td>
            </tr>

            <tr>
                <th>付款时限<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="pay_limit_year" value="{{ $info['pay_limit_year'] ?? '' }}" placeholder="请输入付款有效时长【年】"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  style="width: 60px;"/>年
                    <input type="text" class="inp wnormal"  name="pay_limit_day" value="{{ $info['pay_limit_day'] ?? '' }}" placeholder="请输入付款有效时长【天】"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  style="width: 60px;"/>天
                    <input type="text" class="inp wnormal"  name="pay_limit_hour" value="{{ $info['pay_limit_hour'] ?? '' }}" placeholder="请输入付款有效时长【时】"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  style="width: 60px;"/>时
                    <input type="text" class="inp wnormal"  name="pay_limit_minute" value="{{ $info['pay_limit_minute'] ?? '' }}" placeholder="请输入付款有效时长【分】"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  style="width: 60px;"/>分
                    <input type="text" class="inp wnormal"  name="pay_limit_second" value="{{ $info['pay_limit_second'] ?? '' }}" placeholder="请输入付款有效时长【秒】"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  style="width: 60px;"/>秒
                    <p>用户生成付款记录后多长时间内容可以进行付款操作</p>
                </td>
            </tr>
            <tr>
                <th>有效时长【付款成功】<span class="must">*</span></th>
                <td class="sel_valid_limit">
                    @foreach ($validLimit as $k=>$txt)
                        <label><input type="radio"  name="valid_limit"  value="{{ $k }}"  @if(isset($defaultValidLimit) && $defaultValidLimit == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr class="tr_valid_limit">
                <th>有效时限【付款成功】<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="limit_year" value="{{ $info['limit_year'] ?? '' }}" placeholder="请输入有效时长【年】"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  style="width: 60px;"/>年
                    <input type="text" class="inp wnormal"  name="limit_day" value="{{ $info['limit_day'] ?? '' }}" placeholder="请输入有效时长【天】"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  style="width: 60px;"/>天
                    <input type="text" class="inp wnormal"  name="limit_hour" value="{{ $info['limit_hour'] ?? '' }}" placeholder="请输入有效时长【时】"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  style="width: 60px;"/>时
                    <input type="text" class="inp wnormal"  name="limit_minute" value="{{ $info['limit_minute'] ?? '' }}" placeholder="请输入有效时长【分】"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  style="width: 60px;"/>分
                    <input type="text" class="inp wnormal"  name="limit_second" value="{{ $info['limit_second'] ?? '' }}" placeholder="请输入有效时长【秒】"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  style="width: 60px;"/>秒
                    <p>付款成功后多长时间内容不可以进行相同的付款操作</p>
                </td>
            </tr>
            <tr>
                <th>唯一用户付款标准<span class="must">*</span></th>
                <td class="sel_valid_limit">
                    @foreach ($uniqueUserStandard as $k=>$txt)
                        <label><input type="radio"  name="unique_user_standard"  value="{{ $k }}"  @if(isset($defaultUniqueUserStandard) && $defaultUniqueUserStandard == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr class="answer_many">
                <th>收集字段<span class="must">*</span></th>
                <td >
                    <table class="table2">
                        <thead>
                        <tr>
                            <th style="width: 10%;">字段名</th>
                            <th >字段值类型</th>
                            <th >单选/复选的选项<hr/>【一行一个选项】</th>
                            <th >是否必填</th>
                            <th >填写终端</th>
                            <th >显示终端</th>
                            <th >操作</th>
                        </tr>
                        </thead>
                        <tbody id="data_list">
                        {{--
                        <tr>
                            <td>
                                <input type="hidden" name="answer_id[]" value="{{ $v['id'] or 0 }}"/>
                                <span class="colum"></span>、<input type="text" name="answer_content[]" class="inp wlong" value="{{ $v['answer_content'] or '' }}"/>
                            </td>
                            <td align="center">
                                <input type="radio" name="answer_val" value=""  @if(isset($v['is_right']) && $v['is_right'] == 1) checked="checked"  @endif />
                                <input type="checkbox" class="check_answer" name="check_answer_val[]" value="" @if(isset($v['is_right']) && $v['is_right'] == 1) checked="checked"  @endif/>
                            </td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.moveUp(this)">
                                    <i class="ace-icon fa fa-arrow-up bigger-60"> 上移</i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.moveDown(this)">
                                    <i class="ace-icon fa fa-arrow-down bigger-60"> 下移</i>
                                </a>

                                <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.del(this)">
                                    <i class="ace-icon fa fa-trash-o bigger-60"> 移除</i>
                                </a>
                            </td>
                        </tr>
                        --}}
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="7" style="text-align:right;">
                                <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.add()"  style="margin-right: 132px;">
                                    <i class="ace-icon fa fa-plus bigger-60"> 增加字段</i>
                                </a>
                            </td>
                        </tr>

                        </tfoot>
                    </table>
                </td>
            </tr>
            <tr>
                <th>开启状态<span class="must">*</span></th>
                <td class="sel_open_status">
                    @foreach ($openStatus as $k=>$txt)
                        <label><input type="radio"  name="open_status"  value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>收费状态<span class="must">*</span></th>--}}
{{--                <td class="sel_pay_status">--}}
{{--                    @foreach ($payStatus as $k=>$txt)--}}
{{--                        <label><input type="radio"  name="pay_status"  value="{{ $k }}"  @if(isset($defaultPayStatus) && $defaultPayStatus == $k) checked="checked"  @endif />{{ $txt }} </label>--}}
{{--                    @endforeach--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <th>记录处理方式<span class="must">*</span></th>
                <td class="sel_handle_method">
                    @foreach ($handleMethod as $k=>$txt)
                        <label><input type="radio"  name="handle_method"  value="{{ $k }}"  @if(isset($defaultHandleMethod) && $defaultHandleMethod == $k) checked="checked"  @endif />{{ $txt }} </label>
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
                <th><hr></th>
                <td>
                    <hr>
                </td>
            </tr>
            <tr>
                <th>收款帐号<span class="must">*</span></th>
                <td>
                    @foreach ($pay_config_kv as $k=>$txt)
                        <label><input type="radio"  name="pay_config_id"  value="{{ $k }}"  @if(isset($defaultPayConfig) && $defaultPayConfig == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                    <p style="color: gray;">
                        课程必须设置收款账号信息，班级也可以再次设置，班级设置优先！
                    </p>
                </td>
            </tr>
            <tr>
                <th>收款开通类型<span class="must">*</span></th>
                <td class="sel_pay_method">
                    @foreach ($payMethod as $k=>$txt)
                        <label><input type="checkbox"  name="pay_method[]"  value="{{ $k }}"  @if(isset($defaultPayMethod) && $defaultPayMethod > 0 && ($defaultPayMethod & $k) == $k) checked="checked"  @endif @if(isset($info['allow_pay_method']) && ($info['allow_pay_method'] & $k) <=0 ) disabled   @endif/>{{ $txt }} </label> <br />
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>发票开票模板<span class="must">*</span></th>
                <td>
                    @foreach ($invoice_template_kv as $k=>$txt)
                        <label><input type="radio"  name="invoice_template_id"  value="{{ $k }}"  @if(isset($defaultInvoiceTemplate) && $defaultInvoiceTemplate == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>发票商品项目模板<span class="must">*</span></th>
                <td>
                    @foreach ($invoice_project_template_kv as $k=>$txt)
                        <label><input type="radio"  name="invoice_project_template_id"  value="{{ $k }}"  @if(isset($defaultInvoiceProjectTemplate) && $defaultInvoiceProjectTemplate == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
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

    var SAVE_URL = "{{ url('api/admin/payment_project/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/payment_project')}}";//保存成功后跳转到的地址

    var BEGIN_TIME = "{{ $info['pay_begin_time'] ?? '' }}" ;//收费开始时间
    var END_TIME = "{{ $info['pay_end_time'] ?? '' }}" ;//收费结束时间

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业

    var PAY_CONFIG_INFO_URL = "{{ url('api/admin/order_pay_config/ajax_info') }}";// ajax获得支付方式详情记录地址

    // 文件上传相关的~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // var UPLOAD_COMMON_URL = "{ { url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    // var UPLOAD_WORD_URL = "{ { url('api/admin/vods/up_word') }}";//上传word地址
    var UPLOAD_LARGE_URL = "{{ url('api/admin/payment_project/up_file') }}";//上传excel地址
    // var UPLOAD_GRID_URL = "{ { url('api/admin/vods/up_pdf') }}";//上传pdf地址

    var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载网页打印机驱动
    var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

    // 初始化的资源信息
    // var RESOURCE_LIST_COMMON = @ json($info['resource_list'] ?? []) ;
    var RESOURCE_LIST_LARGE = @json($info['resource_list'] ?? []) ;
    // var RESOURCE_LIST_GRID = @ json($info['resource_list'] ?? []) ;
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    var REG_CONFIG = @json($regConfig ?? []);// 确定值的正则表达式，用于验证数据

    var SPECIFIED_AMOUNT_STATUS_FIXED = {{ \App\Models\QualityControl\PaymentProject::SPECIFIED_AMOUNT_STATUS_FIXED ?? 0 }};// 指定金额
    var PAY_VALID_STATUS_FIXED = {{ \App\Models\QualityControl\PaymentProject::PAY_VALID_STATUS_FIXED ?? 0 }};// 收费 指定时间
    var VALID_LIMIT_FIXED = {{ \App\Models\QualityControl\PaymentProject::VALID_LIMIT_FIXED ?? 0 }};// 指定有效时长

    var FIELD_VAL_TYPE_RADIO = {{ \App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_RADIO ?? 0 }};// 单选框
    var FIELD_VAL_TYPE_CHECKBOX = {{ \App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_CHECKBOX ?? 0 }};// 复选框

    var FIELD_CONFIG = @json($fieldsConfig ?? []);// 收集字段相关的选项
    var DYNAMIC_BAIDU_TEMPLATE = "baidu_template_data_list";//百度模板id
    var DYNAMIC_TABLE_BODY = "data_list";//数据列表id
    var PROJECT_FIELDS_LIST = @json($info['project_fields'] ?? []) ;
    var FIELDS_DATA_LIST = {
        'data_list':PROJECT_FIELDS_LIST
    };
    var DEFAULT_DATA_LIST = [{'id':0, 'val_type':0,'sel_items':'','required_status':0,'input_status':0,'show_status':0}];// 默认答案

    var FIELDS_NUMBER = 1;
</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/QualityControl/PaymentProject_edit.js') }}?54"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
