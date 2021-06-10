

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
            <tr  @if (isset($staff_hidden) && $staff_hidden == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must">*</span></th>
                <td>
                    <input type="hidden" class="select_id"  name="staff_id"  value="{{ $info['staff_id'] ?? '' }}" />
                    <span class="select_name staff_name">{{ $info['user_staff_name'] ?? '' }}</span>
                    <i class=" select_close staff_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectStaff(this)">选择所属企业</button>
                </td>
            </tr>
            <tr>
                <th>应用名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="app_name" value="{{ $info['app_name'] ?? '' }}" placeholder="请输入应用名称"/>
                </td>
            </tr>
            <tr>
                <th>应用图片<span class="must">*</span></th>
                <td>
                    <input type="hidden" name="resource_id_pdf" value=""/>

                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>请上传1个文件，上传格式支持:.jpg .gif .bmp .png .jpeg</p>
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
                        </div>
                    </div>
                    {{--                <span>请上传pdf格式的文档</span>--}}
                </td>
            </tr>
            <tr>
                <th>备注说明<span class="must">*</span></th>
                <td>
                    <textarea name="remarks" placeholder="请输入备注说明" class="layui-textarea">{{ replace_enter_char($info['remarks'] ?? '',2) }}</textarea>

                </td>
            </tr>
            <tr>
                <th>审核状态<span class="must">*</span></th>
                <td>
                    @foreach ($openStatus as $k=>$txt)
                        <label><input type="radio"  name="open_status"  value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) checked="checked"  @endif />{{ $txt }} </label>
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
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/apply/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/apply')}}";//保存成功后跳转到的地址

    var SELECT_STAFF_URL = "{{url('admin/third_service/select')}}";// 选择所属人员

    // 文件上传相关的~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // var UPLOAD_COMMON_URL = "{ { url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    // var UPLOAD_WORD_URL = "{ { url('api/admin/apply/up_word') }}";//上传word地址
    var UPLOAD_LARGE_URL = "{{ url('api/admin/apply/up_file') }}";//上传excel地址
    // var UPLOAD_GRID_URL = "{ { url('api/admin/apply/up_pdf') }}";//上传pdf地址

    var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载网页打印机驱动
    var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

    // 初始化的资源信息
    // var RESOURCE_LIST_COMMON = @ json($info['resource_list'] ?? []) ;
    var RESOURCE_LIST_LARGE = @json($info['resource_list'] ?? []) ;
    // var RESOURCE_LIST_GRID = @ json($info['resource_list'] ?? []) ;
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/QualityControl/Apply_edit.js') }}?17"  type="text/javascript"></script>

@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
