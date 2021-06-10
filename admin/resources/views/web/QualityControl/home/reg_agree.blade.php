<!doctype html>
<html lang="en">
<head>
    @if(isset($host_type) && $host_type == 2)
        @include('web.QualityControl.layout_public_market.pagehead')
    @else
        @include('web.QualityControl.layout_public.pagehead')
    @endif
</head>
<body style=" background:#eee; ">
	<div id="main">
		<div class="reg" style="width:980px; margin:40px auto 20px auto; border:1px solid #eee; min-height:500px;  padding:20px 20px; background:#fff;  ">

			<div class="hd tc" style="padding:30px 0;">
				<h2>注册服务协议</h2>
			</div>
			<div class="bd" style="width:800px; margin:0 auto;">
					<p>本协会实行会员制。会员分团体会员与个人会员。 申请加入本协会的会员,必须具备下列条件：</p>
					<p>（一）拥护本协会的章程；</p>
					<p>（二）有自愿加入本协会的意愿；</p>
					<p>（三）在认证领域内具有一定的影响；</p>
					<p>（四）个人从事管理工作5年以上的管理人员和从事理论研究的专家、学者。</p>
					<p><b>会员入会程序：</b></p>
					<p>（一）提交入会申请书；</p>
					<p>（二）经本协会理事会( 或常务理事会 )讨论通过；</p>
					<p>（三）由理事会或理事会秘书处发给会员证。</p>
					<p><b>会员享有下列权利：</b></p>
					<p>（一）本协会的选举权、被选举权和表决权；</p>
					<p>（二）参加本协会的活动；</p>
					<p>（三）获得本协会服务的优先权； </p>
					<p>（四）对本协会工作的批评建议权和监督权； </p>
					<p>（五）对本协会会费收支情况提出质询的权力； </p>
					<p>（六）入会自愿、退会自由。</p>

                <div class="k20"></div>

			</div>
			<div class="c"></div>
		</div>
	</div>
</body>
</html>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script src="{{ asset('/js/web/QualityControl/reg_agree.js') }}"  type="text/javascript"></script>

