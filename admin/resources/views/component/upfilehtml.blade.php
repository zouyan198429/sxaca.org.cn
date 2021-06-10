{{--
参数及值说明
fileList 文件列表容器元素
         "common"  普通文件列表
         'large'  使用大号文件列表
         "grid" 使用网格文件列表
upload_id 上传对象的 id
upload_url 上传对象 服务器接收地址 your/file/upload/url
--}}
<div class="resourceBlock">
{{--    <div  class="cards upload_img">--}}
        {{--
        <div class="col-md-4 col-sm-6 col-lg-3">
            <div class="card ">
                <img src="http://comp.kezhuisu.net/img/icon-shop.png" alt="">
                <div class="pre with-padding clearfix">
                    <h4 class="text-ellipsis">123456</h4>
                    <p class="text-gray">上传日期：{{ date('Y-m-d',time()) }}</p>
                    <i class="icon icon-times pull-right del"  data-id="1"></i>
                </div>
            </div>
        </div>
        --}}
{{--    </div>--}}
    <div class="cards upload_img uploader-files file-list file-list-grid file-rename-by-click">
{{--        <div class="file" id="file-o_1eleee5sg1g4qm4q7tn1kvh1ec13q" data-status="done">--}}
{{--            <div class="file-progress-bar" style="width: 100%;"></div>--}}
{{--            <div class="file-wrapper">--}}
{{--                <div class="file-icon" style="color: rgb(112, 173, 31);">--}}
{{--                    <div class="file-icon-image" style="background-image: url(data:image/jpeg;base64,/9j/4AAQSkZ/Z)"></div>--}}
{{--                </div>--}}
{{--                <div class="content">--}}
{{--                    <div class="file-name">112.jpg</div>--}}
{{--                    <div class="file-size small text-muted">579 KB</div>--}}
{{--                </div>--}}
{{--                <div class="actions">--}}
{{--                    <div class="file-status" data-toggle="tooltip" data-original-title="已上传" title="">--}}
{{--                        <i class="icon"></i>--}}
{{--                        <span class="text"></span>--}}
{{--                    </div>--}}
{{--                    <a data-toggle="tooltip" class="btn btn-link btn-download-file" target="_blank" title="" download="112.jpg" data-original-title="下载" href="https://httpbin.org/post">--}}
{{--                        <i class="icon icon-download-alt"></i>--}}
{{--                    </a>--}}
{{--                    <button type="button" data-toggle="tooltip" class="btn btn-link btn-reset-file" title="" data-original-title="重新上传">--}}
{{--                        <i class="icon icon-repeat"></i>--}}
{{--                    </button>--}}
{{--                    <button type="button" data-toggle="tooltip" class="btn btn-link btn-rename-file" title="" data-original-title="重命名">--}}
{{--                        <i class="icon icon-pencil"></i>--}}
{{--                    </button>--}}
{{--                    <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-delete-file" data-original-title="移除">--}}
{{--                        <i class="icon icon-trash text-danger"></i>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="file" id="file-o_1eleeebvp1brb1t3n1hqe1ll81bo841" data-status="done">--}}
{{--            <div class="file-progress-bar" style="width: 100%;"></div>--}}
{{--            <div class="file-wrapper">--}}
{{--                <div class="file-icon" style="color: rgb(173, 35, 31);">--}}
{{--                    <i class="icon icon-file-excel file-icon-xlsx" data-type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-ext="xlsx"></i>--}}
{{--                </div>--}}
{{--                <div class="content">--}}
{{--                    <div class="file-name">aaaa.xlsx</div>--}}
{{--                    <div class="file-size small text-muted">6 KB</div>--}}
{{--                </div>--}}
{{--                <div class="actions">--}}
{{--                    <div class="file-status" data-toggle="tooltip" data-original-title="已上传" title="">--}}
{{--                        <i class="icon"></i>--}}
{{--                        <span class="text"></span>--}}
{{--                    </div>--}}
{{--                    <a data-toggle="tooltip" class="btn btn-link btn-download-file" target="_blank" title="" download="aaaa.xlsx" data-original-title="下载" href="https://httpbin.org/post">--}}
{{--                        <i class="icon icon-download-alt"></i>--}}
{{--                    </a>--}}
{{--                    <button type="button" data-toggle="tooltip" class="btn btn-link btn-reset-file" title="" data-original-title="重新上传">--}}
{{--                        <i class="icon icon-repeat"></i>--}}
{{--                    </button>--}}
{{--                    <button type="button" data-toggle="tooltip" class="btn btn-link btn-rename-file" title="" data-original-title="重命名">--}}
{{--                        <i class="icon icon-pencil"></i>--}}
{{--                    </button>--}}
{{--                    <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-delete-file" data-original-title="移除">--}}
{{--                        <i class="icon icon-trash text-danger"></i>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>
    {{--<form  method="post" enctype="multipart/form-data" >--}}
    @switch($fileList)
        {{--普通文件列表--}}
        @case("common")
            <div id="{{ $upload_id }}" class="uploader">
                <div class="file-list" data-drag-placeholder="请拖拽文件到此处"></div>
                <button type="button" class="btn btn-primary uploader-btn-browse"><i class="icon icon-cloud-upload"></i> 选择文件</button>
            </div>
            @break
        {{--'large'：使用大号文件列表--}}
        @case("large")
            <div id='{{ $upload_id }}' class="uploader" data-ride="uploader" data-url="{{ $upload_url }}">
            <div class="uploader-message text-center">
                <div class="content"></div>
                <button type="button" class="close">×</button>
            </div>
            <div class="uploader-files file-list file-list-lg" data-drag-placeholder="请拖拽文件到此处"></div>
            <div class="uploader-actions">
                <div class="uploader-status pull-right text-muted"></div>
                <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
                <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
            </div>
            </div>
            @break
        {{--'grid'：使用网格文件列表；--}}
        @case("grid")
            <div id='{{ $upload_id }}' class="uploader" data-ride="uploader" data-url="{{ $upload_url }}">
                <div class="uploader-message text-center">
                    <div class="content"></div>
                    <button type="button" class="close">×</button>
                </div>
                <div class="uploader-files file-list file-list-grid"></div>
                <div>
                    <div class="uploader-status pull-right text-muted"></div>
                    <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
                    <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
                </div>
            </div>
            @break
    @endswitch
</div>
