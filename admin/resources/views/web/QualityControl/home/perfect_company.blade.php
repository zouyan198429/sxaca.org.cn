<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
@if(isset($host_type) && $host_type == 2)
    @include('web.QualityControl.layout_public_market.pagehead')
@else
    @include('web.QualityControl.layout_public.pagehead')
@endif
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
</head>
<body style=" background:#f8f8f8; ">
    @if(isset($host_type) && $host_type == 2)
        @include('web.QualityControl.layout_public_market.header')
    @else
        @include('web.QualityControl.layout_public.header')
    @endif
    <div class="line-blue"></div>
	<div id="main">
		<div class="reg" style="box-shadow:  0 0 8px #ddd">

			<div class="hd-reg" >
				<h2>完善企业资料</h2>
			</div>

            <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
			<div class="bd" style="width:800px; margin:0 auto;">
				<p>带 <span class="red">*</span> 为必填项</p> <br>
				<div class="form-item">
                    <label for="username" class="form-label"> 单位名称 <span class="red">*</span> </label>
                    <div class="form-input"><input type="text" name="company_name" class="form-control" autocomplete="off" value="{{ $info['company_name'] ?? '' }}"></div>
                </div>
				<div class="form-item">
				    <label for="username" class="form-label"> 统一社会信用代码 <span class="red">*</span> </label>
				    <div class="form-input"><input type="text" name="company_credit_code" class="form-control" autocomplete="off" value="{{ $info['company_credit_code'] ?? '' }}"></div>
				</div>

				<div class="form-item company_is_legal_persion">
				    <label for="username" class="form-label"> 是否独立法人：  </label>
				    <div class="form-input"><input type="checkbox" name="company_is_legal_persion" class="form-control" style="width:22px; margin:0;display: inline-block;  vertical-align: middle;" autocomplete="off" value="1"  @if(isset($info['company_is_legal_persion']) && $info['company_is_legal_persion'] == 1) checked="checked"  @endif >非独立法人 <span class="gray">企业类型为非独立法人时请填写主体单位信息</span></div>

				</div>
				<div class="form-item company_is_legal_persion_item">
                    <label for="username" class="form-label"> 主体机构统一社会信用代码   </label>
                    <div class="form-input"><input type="text" name="company_legal_credit_code" class="form-control" autocomplete="off" value="{{ $info['company_legal_credit_code'] ?? '' }}"></div>
                </div>
				<div class="form-item company_is_legal_persion_item">
				    <label for="username" class="form-label"> 主体机构  </label>
				    <div class="form-input"><input type="text" name="company_legal_name" class="form-control" autocomplete="off" value="{{ $info['company_legal_name'] ?? '' }}"></div>
				</div>

				<hr>
				<div class="k20"></div>

				<div class="form-item">
				    <label for="text" class="form-label">所在城市 <span class="red">*</span> </label>
				    <div class="form-input">
				    	<select class="form-control" name="city_id">
						  <option value="">请选择</option>
                            @foreach ($citys_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCity) && $defaultCity == $k) selected @endif >{{ $txt }}</option>
                            @endforeach
						</select>

					</div>
				</div>

				<div class="form-item">
				    <label for="text" class="form-label">企业类别 <span class="red">*</span> </label>
				    <div class="form-input">
                        <label  for="company_type_one"> <input type="radio" id="company_type_one" name="company_type" value="1" title="检测机构" style="margin:0; " @if (isset($info['company_type']) && $info['company_type'] == 1 ) checked @endif> 检验检测机构</label>&nbsp;
                        <label  for="company_type_two"><input type="radio" id="company_type_two"  name="company_type" value="2" title="生产企业" style="margin:0; " @if (isset($info['company_type']) && $info['company_type'] == 2 ) checked @endif> 生产企业内部实验室</label>&nbsp;
					</div>
				</div>
				<div class="form-item">
				    <label for="text" class="form-label">企业性质 <span class="red">*</span> </label>
				    <div class="form-input">
						<select name="company_prop" id="drpNature" class="ipt"  style="width: 360px;">
							<option value="">请选择</option>
                            @foreach ($companyProp as $k=>$txt)
                                <option value="{{ $k }}"  @if(isset($defaultCompanyProp) && $defaultCompanyProp == $k) selected @endif >{{ $txt }}</option>
                            @endforeach

						</select>
					</div>
				</div>


                <div class="form-item">
                    <label for="text" class="form-label">通讯地址 <span class="red">*</span> </label>
                    <div class="form-input"><input type="text" name="addr" class="form-control" autocomplete="off" value="{{ $info['addr'] ?? '' }}"></div>
                </div>
				<div class="form-item">
				    <label for="text" class="form-label">邮编</label>
				    <div class="form-input"><input type="text" name="zip_code" class="form-control" autocomplete="off" value="{{ $info['zip_code'] ?? '' }}"></div>
				</div>
				<div class="form-item" style="display: none;">
				    <label for="text" class="form-label">传真</label>
				    <div class="form-input"><input type="text" name="fax" class="form-control" autocomplete="off" value="{{ $info['fax'] ?? '' }}"></div>
				</div>
                 <div class="form-item">
                    <label for="text" class="form-label">企业邮箱<span class="red">*</span> </label>
                    <div class="form-input">
                    	<input type="text" name="email" class="form-control" autocomplete="off" value="{{ $info['email'] ?? '' }}">
                    	<p class="gray">用于接收通知等。</p>
                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">法人代表 <span class="red">*</span></label>
                    <div class="form-input"><input type="text" name="company_legal" class="form-control" autocomplete="off" value="{{ $info['company_legal'] ?? '' }}"></div>
                </div>
				<div class="form-item">
				    <label for="text" class="form-label">营业执照 <span class="red">*</span></label>
				    <div class="form-input">
{{--                        <!-- <input type="file" name="text" class="form-control" autocomplete="off" value="{{ $info['aaaa'] ?? '' }}"> -->--}}

                        <div class="alert alert-warning alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <p>一次最多上传1张图片，jpg或png格式，小于4M。</p>
                        </div>
                        <div class="row  baguetteBoxOne gallery ">
                            <div class="col-xs-12">
                                @component('component.upfileone.piconecode')
                                    @slot('fileList')
                                        grid
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



                    </div>


				</div>
                <div class="form-item">
                    <label for="text" class="form-label">单位人数 <span class="red">*</span> </label>
                    <div class="form-input">
                        <select class="form-control" style="width: 360px;"  name="company_peoples_num">
    					  <option value="">请选择</option>
                            @foreach ($companyPeoples as $k=>$txt)
                                <option value="{{ $k }}"  @if(isset($defaultCompanyPeoples) && $defaultCompanyPeoples == $k) selected @endif >{{ $txt }}</option>
                            @endforeach
    					</select>
                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">所属行业 <span class="red">*</span> </label>
                    <div class="form-input">
                        <select class="form-control" style="width: 360px;"   name="company_industry_id">
    					  <option value="">请选择</option>
                            @foreach ($industry_kv as $k=>$txt)
                                <option value="{{ $k }}"  @if(isset($defaultIndustry) && $defaultIndustry == $k) selected @endif >{{ $txt }}</option>
                            @endforeach
    					</select>
                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">资质认定证书编号 <span class="red">*</span></label>
                    <div class="form-input"><input type="text" name="company_certificate_no" class="form-control" autocomplete="off" value="{{ $info['company_certificate_no'] ?? '' }}">

                        <p class="gray">是12位数字！</p>

                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">资质认定证书有效起止时间 <span class="red">*</span></label>
                    <div class="form-input">
                        <input type="text" class="inp wlong ratify_date" name="ratify_date" value="{{ $info['ratify_date'] ?? '' }}" placeholder="请选择批准日期" style="width: 150px;"  readonly="true"/>
                        -
                        <input type="text" class="inp wlong valid_date" name="valid_date" value="{{ $info['valid_date'] ?? '' }}" placeholder="请选择有效期至"  style="width: 150px;" readonly="true"/>

                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">实验室地址<span class="must"></span></label>
                    <div class="form-input">
                        <input type="text" class="form-control inp wnormal"  name="laboratory_addr" value="{{ $info['laboratory_addr'] ?? '' }}" placeholder="请输入实验室地址"/>
                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">联系人<span class="red">*</span></label>
                    <div class="form-input"><input type="text" name="company_contact_name" class="form-control" autocomplete="off" value="{{ $info['company_contact_name'] ?? '' }}"></div>
                </div>
				<div class="form-item">
				    <label for="text" class="form-label">联系人手机<span class="red">*</span></label>
				    <div class="form-input"><input type="text" name="company_contact_mobile" class="form-control" autocomplete="off" value="{{ $info['company_contact_mobile'] ?? '' }}"></div>
				</div>
                <div class="form-item">
                    <label for="text" class="form-label">固定电话<span class="red"></span></label>
                    <div class="form-input"><input type="text" name="company_contact_tel" class="form-control" autocomplete="off" value="{{ $info['company_contact_tel'] ?? '' }}"></div>
                </div>
				<div class="form-item read_and_agree">
				    <label for="text" class="form-label"> </label>
				    <div class="form-input"><input type="checkbox" name="read_and_agree" autocomplete="off" value="1" style="width:26px;line-height: 28px; margin:0;  display: inline-block;  vertical-align: middle;" > <span style="line-height: 28px;">我已阅读并同意<a href="javascript:void(0);" class="blue reg_agree_info">注册服务协议</a></span></div>
				</div>
                <div class="k20"></div>
                <a href="javascript:void(0);" class="btn btn-default btn-block"   id="submitBtn">提交</a>
                <div class="k20"></div>

			</div>
            </form>
			<div class="c"></div>
		</div>
	</div>
    @if(isset($host_type) && $host_type == 2)
        @include('web.QualityControl.layout_public_market.footer')
    @else
        @include('web.QualityControl.layout_public.footer')
    @endif
</body>
</html>
<script type="text/javascript" src="{{asset('laydate/laydate.js')}}"></script>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/web/ajax_perfect_company') }}";// ajax保存记录地址
    var LOG_OUT_URL = "{{url('web/logout')}}";//保存成功后跳转到的地址

    var BEGIN_TIME = "{{ $info['ratify_date'] ?? '' }}" ;//批准日期
    var END_TIME = "{{ $info['valid_date'] ?? '' }}" ;//有效期至

    // 上传图片变量
    var FILE_UPLOAD_URL = "{{ url('api/web/upload') }}";// 文件上传提交地址 'your/file/upload/url'
    var PIC_DEL_URL = "{{ url('api/web/upload/ajax_del') }}";// 删除图片url
    var MULTIPART_PARAMS = {pro_unit_id:'0'};// 附加参数	函数或对象，默认 {}
    var LIMIT_FILES_COUNT = 1;//   限制文件上传数目	false（默认）或数字
    var MULTI_SELECTION = false;//  是否可用一次选取多个文件	默认 true false
    var FLASH_SWF_URL = "{{asset('dist/lib/uploader/Moxie.swf') }}";// flash 上传组件地址  默认为 lib/uploader/Moxie.swf
    var SILVERLIGHT_XAP_URL = "{{asset('dist/lib/uploader/Moxie.xap') }}";// silverlight_xap_url silverlight 上传组件地址  默认为 lib/uploader/Moxie.xap  请确保在文件上传页面能够通过此地址访问到此文件。
    var SELF_UPLOAD = true;//  是否自己触发上传 TRUE/1自己触发上传方法 FALSE/0控制上传按钮
    var FILE_UPLOAD_METHOD = 'initPic()';// 单个上传成功后执行方法 格式 aaa();  或  空白-没有
    var FILE_UPLOAD_COMPLETE = '';  // 所有上传成功后执行方法 格式 aaa();  或  空白-没有
    var FILE_RESIZE = {quuality: 40};
    // resize:{// 图片修改设置 使用一个对象来设置如果在上传图片之前对图片进行修改。该对象可以包含如下属性的一项或全部：
    //     // width: 128,// 图片压缩后的宽度，如果不指定此属性则保持图片的原始宽度；
    //     // height: 128,// 图片压缩后的高度，如果不指定此属性则保持图片的原始高度；
    //     // crop: true,// 是否对图片进行裁剪；
    //     quuality: 50,// 图片压缩质量，可取值为 0~100，数值越大，图片质量越高，压缩比例越小，文件体积也越大，默认为 90，只对 .jpg 图片有效；
    //     // preserve_headers: false // 是否保留图片的元数据，默认为 true 保留，如果为 false 不保留。
    // },
    var RESOURCE_LIST = @json($info['resource_list'] ?? []) ;
    var PIC_LIST_JSON =  {'data_list': RESOURCE_LIST };// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}


    var REG_AGREE_URL = "{{url('web/reg_agree')}}";// 注册服务协议
</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/web/QualityControl/perfect_company.js') }}?4"  type="text/javascript"></script>
@component('component.upfileincludejs')
@endcomponent
