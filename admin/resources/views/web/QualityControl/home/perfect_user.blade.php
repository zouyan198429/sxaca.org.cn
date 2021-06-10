<!doctype html>
<html lang="en">
<head>
    @if(isset($host_type) && $host_type == 2)
        @include('web.QualityControl.layout_public_market.pagehead')
    @else
        @include('web.QualityControl.layout_public.pagehead')
    @endif
</head>
<body style=" background:#f8f8f8; ">
    @if(isset($host_type) && $host_type == 2)
        @include('web.QualityControl.layout_public_market.header')
    @else
        @include('web.QualityControl.layout_public.header')
    @endif
    <div class="line-blue"></div>
	<div id="main">
		<div class="reg" style="box-shadow:  0 0 8px #ddd" >

            <div class="hd-reg" >
                <h2>完善个人资料</h2>
            </div>
            <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
			<div class="bd" style="width:800px; margin:0 auto;">
				<p>带 <span class="red">*</span> 为必填项</p> <br>
				<div class="form-item">
				    <label for="username" class="form-label"> 单位名称 <span class="red">*</span> </label>
				    <div class="form-input">
                        <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                        <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                        <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
{{--                        <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectShop(this)">选择所属店铺</button>--}}
{{--						<input type="text" name="username" class="form-control" autocomplete="off" value="">--}}
						<button   type="button" class="btn btn-gray"   onclick="otheraction.selectCompany(this)">选择所属企业</button>
					</div>
				</div>


                <div class="form-item">
                    <label for="text" class="form-label">邮箱<span class="red">*</span> </label>
                    <div class="form-input">
                    <input type="text" name="email" autocomplete="off" value="{{ $info['email'] ?? '' }}" class="w480">
                    <p class="gray">用于接收通知等。</p>
                    </div>
                </div>


                <div class="form-item">
                    <label for="text" class="form-label">微信号<span class="red">*</span></label>
                    <div class="form-input">
                    <input type="text" name="qq_number" autocomplete="off" value="{{ $info['qq_number'] ?? '' }}" class="w480">
                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">身份证号<span class="red">*</span></label>
                    <div class="form-input">
                    <input type="text" name="id_number" autocomplete="off" value="{{ $info['id_number'] ?? '' }}" class="w480">
                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">职位<span class="red"></span></label>
                    <div class="form-input">
                        <input type="text" name="position_name" autocomplete="off" value="{{ $info['position_name'] ?? '' }}" class="w480">
                    </div>
                </div>
                <div class="form-item">
                    <label for="text" class="form-label">所在城市 <span class="red">*</span> </label>
                    <div class="form-input">
                    <select class="form-control"  name="city_id">
					  <option value="">请选择</option>
                        @foreach ($citys_kv as $k=>$txt)
                            <option value="{{ $k }}"  @if(isset($defaultCity) && $defaultCity == $k) selected @endif >{{ $txt }}</option>
                        @endforeach
					</select>
					</div>

                </div>
                <div class="form-item">
                    <label for="text" class="form-label">通讯地址</label>
                    <div class="form-input">
	                    <input type="text" name="addr" autocomplete="off" value="{{ $info['addr'] ?? '' }}" class="w480">
	                </div>
                </div>
                <div class="k20"></div>
                <div class="form-item">
                    <label for="password" class="form-label"></label>
                	<div class="form-input">
	                	<a href="javascript:void(0);" class="btn btn-default btn-block"   id="submitBtn">提交</a>
	                </div>
                </div>
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
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/web/ajax_perfect_user') }}";// ajax保存记录地址
    var LOG_OUT_URL = "{{url('web/logout')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('web/select_company')}}";// 选择所属企业
</script>
<script src="{{ asset('/js/web/QualityControl/perfect_user.js') }}?3"  type="text/javascript"></script>

