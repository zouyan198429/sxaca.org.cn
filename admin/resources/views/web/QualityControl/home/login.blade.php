<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    @if(isset($host_type) && $host_type == 2)
        @include('web.QualityControl.layout_public_market.pagehead')
    @else
        @include('web.QualityControl.layout_public.pagehead')
    @endif
{{--  	<script type="text/javascript" src="{{asset('staticweb/js/jquery1.42.min.js')}}"></script>--}}
{{--  	<script type="text/javascript" src="{{asset('staticweb/js/jquery.SuperSlide.2.1.1.js')}}"></script>--}}
</head>
<body id="body-login" >
    @if(isset($host_type) && $host_type == 2)
        @include('web.QualityControl.layout_public_market.header')
    @else
        @include('web.QualityControl.layout_public.header')
    @endif
    <div class="line-blue"></div>
	<div id="main">
		<div class="login">
        <style>

        </style>

			<div class="bd-left">
			</div>
			<div class="bd-right" style="background:#fff;">

                <div class="layui-tab login-tag hd">
                    <input type="hidden" name="form_type" value="compnay_login" />
                    @if(isset($host_type) && $host_type == 2)
                        <ul class="layui-tab-title">
                            <li class="layui-this"  data-type="compnay_login" ><i id="i-com"></i>企业登录</li>
{{--                            <li data-type="user_login"><i id="i-user" ></i>个人登录</li>--}}
                        </ul>
                    @else
                        <ul class="layui-tab-title">
                            <li class="layui-this"  data-type="compnay_login" ><i id="i-com"></i>企业登录</li>
                            <li data-type="user_login"><i id="i-user" ></i>个人登录</li>
                        </ul>
                    @endif
                    <div class="layui-tab-content">

                        <div class="layui-tab-item  layui-show">
                            <div class="bd login-mm" style="width:360px; margin:0 auto;">
                                <ul>
                                    <p>企业帐号登录</p>
                                    <form class="am-form compnay_login" action="#"  method="post" >
                                        <div class="form-group layui-form-item">
                                            <input type="text" name="admin_username" class="form-control" placeholder="输入帐号"   value="">
                                        </div>
                                        <div class="form-group layui-form-item">
                                            <input type="password" name="admin_password" class="form-control fl"   placeholder="输入密码"   value="">
                                            <div class="c"></div>
                                        </div>
                                        <div class="form-group layui-form-item">
<!--                                             <label for="password" class="form-label">图形验证码</label>
 -->                                            <div class="form-input">
                                                <label>
                                                    <input type="text" name="captcha_code" lay-verify="required" placeholder="图形验证码" class="layui-input" style="width:100px; display: inline-block;">
                                                </label>
                                                <input type="hidden" name="captcha_key" />
                                                <img src="" class="layadmin-user-login-codeimg" id="LAY-user-get-vercode" >
                                            </div>
                                        </div>

                                        <a href="javascript:void(0);" class="btn btn-block submitBtn" >登录</a>
                                    </form>

                                </ul>
                            </div>
                        </div>

                        @if(isset($host_type) && $host_type == 2)
                        @else
                        <div class="layui-tab-item">
                            <div class="bd login-mm" style="width:360px; margin:0 auto;">

                            <ul>
                                <p>验证码登录</p>

                                <form class="am-form user_login" action="#"  method="post" >
                                    <div class="form-group layui-form-item">
                                        <input type="text" name="mobile" class="form-control" placeholder="输入手机号"   value="">
                                    </div>
                                    <div class="form-group layui-form-item">
                                        <input type="text" name="mobile_vercode" class="form-control fl" style="width:69.1%;" placeholder="验证码"   value="">
<!--                          <input type="text" name="text" class="form-control fr tc" style="width:34%;" placeholder="发送验证码"   value="">
 -->                                      <button type="button" class="layui-btn LAY-user-getsmscode" id="LAY-user-getsmscode">获取验证码</button>
                                        <div class="c"></div>
                                    </div>
<!--                                      <div class="form-group layui-form-item">- -}}
                                         <label for="password" class="form-label">图形验证码</label>- -}}
                                         <div class="form-input">- -}}
                                             <input type="text" name="captcha_code"  lay-verify="required" placeholder="图形验证码" class="layui-input" style="width:100px;">- -}}
                                             <input type="hidden" name="captcha_key" />- -}}
                                             <img src="" class="layadmin-user-login-codeimg" id="LAY-user-get-vercode" >- -}}
                                         </div>- -}}
                                     </div>- -}}
 -->
                                    <div class="k10">   </div>
                                    <a href="javascript:void(0);" class="btn btn-block submitBtn">登录</a>
                                </form>

                            </ul>
                            </div>

                        </div>
                        @endif
                    </div>
                </div>
				<div class="fd tc">
					还没有帐户？ <a href="{{ url('web/reg') }}" >立即注册</a>
					<div class="k20"></div>
				</div>
                <div class="c"></div>

			</div>
			<div class="c"></div>
		</div>
	</div>

{{--		<script type="text/javascript">jQuery(".bd-right").slide();</script>--}}
    @if(isset($host_type) && $host_type == 2)
        @include('web.QualityControl.layout_public_market.footer')
    @else
        @include('web.QualityControl.layout_public.footer')
    @endif
</body>
</html>

<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
@include('public.dynamic_list_foot')
<script>
    var CAPTCHA_IMG_CLASS = "layadmin-user-login-codeimg";
    // var CAPTCHA_IMG_ID = "LAY-user-get-vercode";
    var CAPTCHA_FORM_ITEM = "layui-form-item";
    var CAPTCHA_KEY_INPUT_NAME = "captcha_key";

    // 企业登录
    var COMPANY_LOGIN_URL = "{{ url('api/company/ajax_login') }}";
    var COMPANY_GET_CAPTCHA_IMG_URL = "{{ url('api/company/ajax_captcha') }}";
    var COMPANY_INDEX_URL = "{{url('company')}}";
    // 个人登录
    var USER_LOGIN_URL = "{{ url('api/user/ajax_login_sms') }}";
    var USER_GET_CAPTCHA_IMG_URL = "{{ url('api/user/ajax_captcha') }}";
    var USER_INDEX_URL = "{{url('user')}}";

    var CODE_TIME = 60 * 2;// 手机短信验证码有效期
    var SEND_MOBILE_CODE_URL = "{{ url('api/user/ajax_send_mobile_vercode') }}";// 发送手机验证码
    var SEND_MOBILE_CODE_VERIFY_URL = "{{ url('api/user/ajax_mobile_code_verify') }}";// 发送手机验证码-验证
</script>
<script src="{{ asset('/js/web/QualityControl/login.js') }}"  type="text/javascript"></script>
