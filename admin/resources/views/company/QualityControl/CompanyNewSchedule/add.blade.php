

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css?88')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css?8')}}" media="all">
    <style>
    .gray {
    color: #999;
    margin-top:10px;
     }
     .btn {
        height: 32px;
     }
     .tip1 {
        text-indent: 2em;

     }
     .k20 {
        height: 20px;
        clear: both;
        width: 100%;
     }
    </style>
</head>
<body>

<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
    <p class="red tip1" ><i class="ace-icon fa fa-info-circle  bigger-60"></i> 请上传资质认定的全部附表，每次上传一个文档的PDF版本。</p>
    <table class="table1">

        <tr>
            <th>文档类型<span class="must">*</span></th>
            <td>
                <select class="wnormal" name="type_id" style="width: 280px;">
                    <option value="">请选择文档类型</option>
                    @foreach ($type_ids as $k=>$txt)
                        <option value="{{ $k }}"  @if(isset($defaultTypeId) && $defaultTypeId == $k) selected @endif >{{ $txt }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <th>PDF文件上传<span class="must">*</span></th>
            <td>
                <input type="hidden" name="resource_id_pdf" value=""/>

                <div class="alert alert-warning alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p>请上传一个pdf格式的文件</p>
                </div>
                <div class="row  baguetteBoxOne gallery ">
                    <div class="col-xs-6">
                        @component('component.upfileone.piconecode')
                            @slot('fileList')
                                large
                            @endslot
                            @slot('upload_url')
                                {{ url('api/admin/upload') }}
                            @endslot
                        @endcomponent
                    </div>
                </div>
{{--                <span>请上传pdf格式的文档</span>--}}
            </td>
        </tr>
{{--        <tr>--}}
{{--            <th>PDF文件上传<span class="must">*</span></th>--}}
{{--            <td>--}}
{{--                <span class="file_name"></span>--}}
{{--                <input type="hidden" name="resource_id_pdf" value="">--}}
{{--                <button type="button" class="btn btn-success  btn-xs import_excel"  onclick="otheraction.importExcel(this)">上传文件</button>--}}
{{--                <div style="display:none;" ><input type="file" data-file_type="pdf" class="import_file img_input"></div>--}}{{--导入file对象--}}
{{--                <span>请上传pdf格式的文档</span>--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            <th>word文件上传<span class="must">*</span></th>--}}
{{--            <td>--}}
{{--                <span class="file_name"></span>--}}
{{--                <input type="hidden" name="resource_id" value="">--}}
{{--                <button type="button" class="btn btn-success  btn-xs import_excel"  onclick="otheraction.importExcel(this)">上传文件</button>--}}
{{--                <div style="display:none;" ><input type="file" data-file_type="doc" class="import_file img_input"></div>--}}{{--导入file对象--}}
{{--                <span>请上传doc格式的文档</span>--}}
{{--                <p class="gray">word文件内容要和pdf文件的内容保持一致!</p>--}}
{{--            </td>--}}
{{--        </tr>--}}
        <tr>
            <th> </th>
            <td>
            <div class="k20"></div>
            <button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
        </tr>

    </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/company/company_new_schedule/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/company_new_schedule')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('company/company/select')}}";// 选择所属企业

    var IMPORT_EXCEL_URL = "{{ url('api/company/company_new_schedule/import') }}";//上传文件地址

    var UPLOAD_WORD_URL = "{{ url('api/company/company_new_schedule/up_word') }}";//上传word地址
    var UPLOAD_PDF_URL = "{{ url('api/company/company_new_schedule/up_pdf') }}";//上传pdf地址

    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/company/QualityControl/CompanyNewSchedule_edit.js') }}?6"  type="text/javascript"></script>

<link href="{{asset('dist/lib/uploader/zui.uploader.min.css') }}" rel="stylesheet">
<script src="{{asset('dist/lib/uploader/zui.uploader.min.js') }}"></script>{{--此文件引用一次就可以了--}}
</body>
</html>
