

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
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr  @if (isset($company_hidden) && $company_hidden == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must">*</span></th>
                <td>
                    <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                    <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                    <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择所属企业</button>
                </td>
            </tr>
            <tr>
                <th>操作类型<span class="must">*</span></th>
                <td class="sel_pay_method">
                    @foreach ($openStatus as $k=>$txt)
                        <label><input type="radio"  name="open_status"  value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                        <br/>首次：证书起止日期必填[会更新到证书记录]；<br/>扩项：起止日期非必填[不会更新证书记录];CMA证书号选填【企业已维护证书信息时】
                </td>
            </tr>
            <tr>
                <th>CMA证书号<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="certificate_no" value="{{ $info['certificate_no'] ?? '' }}" placeholder="请输入CMA证书号"/>
                </td>
            </tr>

            <tr>
                <th>实验室地址<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="addr" value="{{ $info['addr'] ?? '' }}" placeholder="请输入实验室地址"/>
                </td>
            </tr>
			<tr>
			    <th>有效起止时间<span class="must">*</span></th>
			    <td>
			        <input type="text" class="inp wlong ratify_date" name="ratify_date" value="{{ $info['ratify_date'] ?? '' }}" placeholder="请选择批准日期" style="width: 150px;"  readonly="true"/>
			        -
			        <input type="text" class="inp wlong valid_date" name="valid_date" value="{{ $info['valid_date'] ?? '' }}" placeholder="请选择有效期至"  style="width: 150px;" readonly="true"/>

                    <p>注：</p>
                    <p>1、首次操作时，excel文件中的【批准日期】列请为空</p>
                    <p>2、扩项操作时，excel文件中的【批准日期】可以指定具体日期，如果为空，则用此页面填写的值。--excel文件中的优先</p>
                    <p>3、excel文件中的【批准日期】列必须在<span style="color: red;">excel中格式化为日期格式</span>，否则日期列值导入不成功。--<span style="color: red;">特别注意</span></p>
			    </td>
			</tr>
            <tr>
                <th>文件<span class="must">*</span></th>
                <td>
                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>请上传excel格式的文件；一次最多上传1个文件。</p>
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

                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>文件<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    <input type="hidden" name="resource_id" value=""/>--}}

{{--                    <div class="alert alert-warning alert-dismissable">--}}
{{--                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>--}}
{{--                        <p>请上传excel格式的文件</p>--}}
{{--                    </div>--}}
{{--                    <div class="row  baguetteBoxOne gallery ">--}}
{{--                        <div class="col-xs-6">--}}
{{--                            @component('component.upfileone.piconecode')--}}
{{--                                @slot('fileList')--}}
{{--                                    large--}}
{{--                                @endslot--}}
{{--                                @slot('upload_url')--}}
{{--                                    {{ url('api/admin/upload') }}--}}
{{--                                @endslot--}}
{{--                            @endcomponent--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <span>请上传excel格式的文档</span>--}}
{{--                </td>--}}
{{--            </tr>--}}
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

    var SAVE_URL = "{{ url('api/admin/certificate_schedule/ajax_excel_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/certificate_schedule')}}";//保存成功后跳转到的地址

    // var UPLOAD_EXCEL_URL = "{{ url('api/admin/certificate_schedule/up_excel') }}";//上传excel地址

    var BEGIN_TIME = "{{ $info['ratify_date'] ?? '' }}" ;//批准日期
    var END_TIME = "{{ $info['valid_date'] ?? '' }}" ;//有效期至

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业

    // 文件上传相关的~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // var UPLOAD_COMMON_URL = "{ { url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    // var UPLOAD_WORD_URL = "{ { url('api/admin/course/up_word') }}";//上传word地址
    var UPLOAD_LARGE_URL = "{{ url('api/admin/certificate_schedule/up_excel') }}";//上传excel地址
    // var UPLOAD_GRID_URL = "{ { url('api/admin/course/up_pdf') }}";//上传pdf地址

    var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载网页打印机驱动
    var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

    // 初始化的资源信息
    // var RESOURCE_LIST_COMMON = @ json($info['resource_list'] ?? []) ;
    var RESOURCE_LIST_LARGE = @json($info['resource_list'] ?? []) ;
    // var RESOURCE_LIST_GRID = @ json($info['resource_list'] ?? []) ;
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    {{--var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf--}}
    {{--var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。--}}

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/QualityControl/CertificateSchedule_excel.js') }}?6"  type="text/javascript"></script>

{{--<link href="{{asset('dist/lib/uploader/zui.uploader.min.css') }}" rel="stylesheet">--}}
{{--<script src="{{asset('dist/lib/uploader/zui.uploader.min.js') }}"></script>--}}{{--此文件引用一次就可以了--}}
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
