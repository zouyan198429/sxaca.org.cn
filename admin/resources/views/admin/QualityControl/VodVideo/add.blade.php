

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
            <tr  @if (isset($hidden_option) && (($hidden_option & 2) == 2) ) style="display: none;"  @endif>
                <th>所属课程<span class="must">*</span></th>
                <td class="sel_vod_id">
                    @foreach ($vod_kv as $k=>$txt)
                        <label><input type="radio"  name="vod_id"  value="{{ $k }}"  @if(isset($defaultVod) && $defaultVod == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>所属章节<span class="must">*</span></th>
                <td class="sel_parent_video_id_kv">
                    <select name="parent_video_id">
                        <option value="0">父级章节</option>
                        @foreach ($level_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultParentVideoId) && $defaultParentVideoId == $k) selected @endif >{!! $txt !!}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>课件名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="video_name" value="{{ $info['video_name'] ?? '' }}" placeholder="请输入课件名称"/>
                </td>
            </tr>
            <tr>
                <th>简要概述<span class="must"></span></th>
                <td>
                    <textarea name="explain_remarks" placeholder="请输入简要概述" class="layui-textarea">{{ replace_enter_char($info['explain_remarks'] ?? '',2) }}</textarea>

                </td>
            </tr>
            <tr>
                <th>图片<span class="must">*</span></th>
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

                </td>
            </tr>
            <tr>
                <th>视频类型<span class="must">*</span></th>
                <td class="sel_vod_id">
                    @foreach ($videoType as $k=>$txt)
                        <label><input type="radio"  name="video_type"  value="{{ $k }}"  @if(isset($defaultVideoType) && $defaultVideoType == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr id="video_url_id"  @if(isset($defaultVideoType) && $defaultVideoType != 2) style="display: none;"  @endif >
                <th>视频网络地址<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="video_url" value="{{ $info['video_url'] ?? '' }}" placeholder="请输入视频网络地址" style="width: 80%;"/>
                </td>
            </tr>
            <tr  id="video_upload_id"  @if(isset($defaultVideoType) && $defaultVideoType != 1) style="display: none;"  @endif>
                <th>视频文件<span class="must">*</span></th>
                <td>
                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>请上传1个文件，上传格式支持: .mp4 .avi .rmvb .rm .flv .mkv .wmv .mpg .mpeg .dat</p>
                        <p>建议点播优先使用mp4，其次使用m3u8。直播优先使用m3u8,这样可以兼容各平台。尽量不要使用flv来做点播，也不要使用rtmp协议来做直播,移动端不支持flv格式的点播放，也不支持rtmp协议的直播</p>
                    </div>
                    <div class="row  baguetteBoxOne gallery ">
                        <div class="col-xs-6">
                            @component('component.upfileone.piconecode')
                                @slot('fileList')
                                    large
                                @endslot
                                @slot('upload_id')
                                    videoUploaderLarge
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
                <th>附件资料文件<span class="must"></span></th>
                <td>
                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p>请上传1-9个文件，上传格式支持:.jpg .gif .bmp .png .jpeg .doc .docx .pdf .xls .xlsx .ppt .pptx .zip</p>
                    </div>
                    <div class="row  baguetteBoxOne gallery ">
                        <div class="col-xs-6">
                            @component('component.upfileone.piconecode')
                                @slot('fileList')
                                    large
                                @endslot
                                @slot('upload_id')
                                     coursewareUploaderLarge
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
                <th>开启状态<span class="must">*</span></th>
                <td class="sel_vod_type_kv">
                    @foreach ($statusOnline as $k=>$txt)
                        <label><input type="radio"  name="status_online"  value="{{ $k }}"  @if(isset($defaultStatusOnline) && $defaultStatusOnline == $k) checked="checked"  @endif />{{ $txt }} </label>
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
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/vod_video/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/vod_video')}}";//保存成功后跳转到的地址

    var VOD_DIR_URL = "{{ url('api/admin/vod_video/ajax_get_vod_dir') }}";// ajax获得课程对应的目录

    // 文件上传相关的~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // var UPLOAD_COMMON_URL = "{ { url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    // var UPLOAD_WORD_URL = "{ { url('api/admin/vod_video/up_word') }}";//上传word地址
    var UPLOAD_LARGE_URL = "{{ url('api/admin/vod_video/up_file') }}";//上传excel地址
    // var UPLOAD_GRID_URL = "{ { url('api/admin/vod_video/up_pdf') }}";//上传pdf地址

    var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载网页打印机驱动
    var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

    // 初始化的资源信息
    // var RESOURCE_LIST_COMMON = @ json($info['resource_list'] ?? []) ;
    var RESOURCE_LIST_LARGE = @json($info['resource_list'] ?? []) ;
    // var RESOURCE_LIST_GRID = @ json($info['resource_list'] ?? []) ;
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    // 文件上传相关的~~~~~~~~~~~~~视频~~~~~~~~~~~~~~~~~~~~~~~
    // var UPLOAD_COMMON_URL = "{ { url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    // var UPLOAD_WORD_URL = "{ { url('api/admin/vod_video/up_word') }}";//上传word地址
    var UPLOAD_VIDEO_LARGE_URL = "{{ url('api/admin/vod_video/up_file_video') }}";//上传excel地址
    // var UPLOAD_GRID_URL = "{ { url('api/admin/vod_video/up_pdf') }}";//上传pdf地址

    var DOWN_VIDEO_FILE_URL = "{{ url('admin/down_file') }}";// 下载网页打印机驱动
    var DEL_VIDEO_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

    var FLASH_VIDEO_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_VIDEO_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

    // 初始化的资源信息
    // var RESOURCE_LIST_COMMON = @ json($info['resource_list'] ?? []) ;
    var RESOURCE_VIDEO_LIST_LARGE = @json($info['resource_list_video'] ?? []) ;
    // var RESOURCE_LIST_GRID = @ json($info['resource_list'] ?? []) ;
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    // 文件上传相关的~~~~~~~~~~~~~附件课件资料~~~~~~~~~~~~~~~~~~~~~~~
    // var UPLOAD_COMMON_URL = "{ { url('api/admin/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    // var UPLOAD_WORD_URL = "{ { url('api/admin/vod_video/up_word') }}";//上传word地址
    var UPLOAD_COURSEWARE_LARGE_URL = "{{ url('api/admin/vod_video/up_file_courseware') }}";//上传excel地址
    // var UPLOAD_GRID_URL = "{ { url('api/admin/vod_video/up_pdf') }}";//上传pdf地址

    var DOWN_COURSEWARE_FILE_URL = "{{ url('admin/down_file') }}";// 下载网页打印机驱动
    var DEL_COURSEWARE_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址

    var FLASH_COURSEWARE_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_COURSEWARE_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。

    // 初始化的资源信息
    // var RESOURCE_LIST_COMMON = @ json($info['resource_list'] ?? []) ;
    var RESOURCE_COURSEWARE_LIST_LARGE = @json($info['resource_list_courseware'] ?? []) ;
    // var RESOURCE_LIST_GRID = @ json($info['resource_list'] ?? []) ;
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/QualityControl/VodVideo_edit.js') }}?7"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
