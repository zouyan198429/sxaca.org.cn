<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">

    @if(isset($host_type) && $host_type == 2)
        @include('web.QualityControl.layout_public_market.pagehead')
    @else
        @include('web.QualityControl.layout_public.pagehead')
    @endif
</head>
<body  id="body-reg1"   >
    @if(isset($host_type) && $host_type == 2)
        @include('web.QualityControl.layout_public_market.header')
    @else
        @include('web.QualityControl.layout_public.header')
    @endif
    <div class="line-blue"></div>
    <form class="am-form" action="#"  method="post"  id="addForm">
	<div id="main">
		<div class="reg"  >

			<div class="hd-reg" >
				<h2>新用户注册</h2>
                <span>  Register</span>
			</div>
			<div class="bd" style="width:800px; margin:0 auto;">

                @if(isset($host_type) && $host_type == 2)
                    <div class="form-item" style="display: none;">
                        <label for="password" class="form-label">帐户类型</label>
                        <div class="form-input">
                            <label  for="company_type_radio"><input type="radio" id="company_type_radio" name="admin_type" value="2" title="企业帐号" checked>企业帐号</label>&nbsp;&nbsp;
                            <label  for="user_type_radio"><input type="radio" id="user_type_radio" name="admin_type" value="4" title="个人帐号" disabled>个人帐号（暂不开放）</label>&nbsp;&nbsp;
                        </div>
                    </div>
                @else
                    <div class="form-item">
                        <label for="password" class="form-label">帐户类型</label>
                        <div class="form-input">
                            <label  for="company_type_radio"><input type="radio" id="company_type_radio" name="admin_type" value="2" title="企业帐号" checked>企业帐号</label>&nbsp;&nbsp;
                            <label  for="user_type_radio"><input type="radio" id="user_type_radio" name="admin_type" value="4" title="个人帐号" disabled>个人帐号（暂不开放）</label>&nbsp;&nbsp;
                        </div>
                    </div>
                @endif

				<div class="form-item company_input">
                    <label for="username" class="form-label">登录帐号</label>
                    <div class="form-input">
                    	<input type="text" name="admin_username"  autocomplete="off" value="" class="w480">
                        <p class="gray">用户名以字母数字组合，长度4~20位。可以包含数字、字母。注册成功后不可修改！</p>
                   	</div>

                </div>
                <div class="form-item company_input">
                    <label for="password" class="form-label">密码</label>
                    <div class="form-input">
                   		<input type="password" name="admin_password"  autocomplete="off" value="" class="w480">
                        <p class="gray">请输入帐号密码。密码需由6-16个字符（数字、字母、下划线）组成，区分大小写</p>
                    </div>

                </div>
                <div class="form-item company_input">
                    <label for="password" class="form-label">确认密码</label>
                    <div class="form-input">
                    	<input type="password" name="repass"  autocomplete="off" value="" class="w480">
                        <p class="gray">再输入一次登录密码</p>
                    </div>

                </div>
                <div class="form-item company_input">
                    <label for="password" class="form-label">图形验证码</label>
                    <div class="form-input">
                        <input type="text" name="captcha_code" id="LAY-user-login-vercode" lay-verify="required" placeholder="图形验证码" class="layui-input" style="width:100px; display: inline-block;">
                        <input type="hidden" name="captcha_key" />
                        <img src="" class="layadmin-user-login-codeimg" id="LAY-user-get-vercode" >
                    </div>
                </div>

{{--                <div class="line"></div>--}}

                <div class="form-item user_input">
                    <label for="username" class="form-label"> 姓名 <span class="red">*</span> </label>
                    <div class="form-input">
                        <input type="text" name="real_name" autocomplete="off" value="{{ $info['real_name'] ?? '' }}" class="w480">
                        <p class="gray">请输入真实姓名</p>
                    </div>
                </div>
                <div class="form-item user_input">
                    <label for="username" class="form-label"> 性别 <span class="red">*</span> </label>
                    <div class="form-input">
                        <label  for="nan"><input type="radio" id="nan" name="sex" value="1"  checked >男</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <label  for="nv"><input type="radio" id="nv" name="sex" value="2"  >女</label>
                    </div>
                </div>
                <div class="form-item user_input">
                    <label for="text" class="form-label">手机号 <span class="red">*</span></label>
                    <div class="form-input">
                    <input type="text" name="mobile" autocomplete="off" value="{{ $info['mobile'] ?? '' }}" class="w480">
                    </div>
                </div>
                <div class="form-item user_input">
                    <label for="text" class="form-label">手机验证码 <span class="red">*</span></label>
                    <div class="form-input">
                        <input type="text" name="mobile_vercode" class="form-control fl" style="width:50.1%;" placeholder="验证码"   value="">
                        <button type="button" class="layui-btn LAY-user-getsmscode" id="LAY-user-getsmscode">获取验证码</button>
                        <div class="c"></div>
                    </div>
                </div>

{{--                <div class="line"></div>--}}


                <div class="form-item">
                    <label for="password" class="form-label"></label>
                	<div class="form-input">
	                	<a href="javascript:void(0);" class="btn btn-default btn-block w150"  id="submitBtn" >注册</a>
 	                </div>
                </div>
                <div class="fd">
                    已经有账户了？<a href="{{ url('web/login') }}">登录</a>
                </div>
			</div>
			<div class="c"></div>
		</div>
	</div>
    </form>
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

<script>
    var REG_URL = "{{ url('api/web/ajax_reg') }}";
    var GET_CAPTCHA_IMG_URL = "{{ url('api/web/ajax_captcha') }}";
    var CAPTCHA_IMG_ID = "LAY-user-get-vercode";
    var CAPTCHA_KEY_INPUT_NAME = "captcha_key";
    // var INDEX_URL = "{{url('web')}}";
    var PERFECT_COMPANY_URL = "{{url('web/perfect_company')}}";// 补充企业资料
    var PERFECT_USER_URL = "{{url('web/perfect_user')}}";// 补充用户资料

    var CODE_TIME = 60 * 2;// 手机短信验证码有效期
    var SEND_MOBILE_CODE_URL = "{{ url('api/user/reg/ajax_send_mobile_vercode') }}";// 发送手机验证码
    var SEND_MOBILE_CODE_VERIFY_URL = "{{ url('api/user/reg/ajax_mobile_code_verify') }}";// 发送手机验证码-验证
</script>
<script src="{{ asset('/js/web/QualityControl/reg.js') }}"  type="text/javascript"></script>
