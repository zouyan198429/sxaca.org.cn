<!DOCTYPE html>
<html>
	<head>
        <title>秦检通_{{ $key_str ?? '' }}资质认定获证机构_陕西{{ $key_str ?? '' }}资质认定获证机构第{{ $page ?? '' }}页_检验检测能力</title>
        <meta name="keywords" content="秦检通,{{ $key_str ?? '' }}资质认定获证机构,陕西{{ $key_str ?? '' }}资质认定获证机构第{{ $page ?? '' }}页,检验检测能力" />
        <meta name="description" content="秦检通,{{ $key_str ?? '' }}资质认定获证机构,陕西{{ $key_str ?? '' }}资质认定获证机构第{{ $page ?? '' }}页,检验检测能力" />
        @include('web.QualityControl.CertificateSchedule.layout_public.pagehead')
        <link href="{{asset('static/css/bootstrap.css')}}" rel="stylesheet" type="text/css" />
	</head>
	<body>
        @include('web.QualityControl.CertificateSchedule.layout_public.header')
        @include('web.QualityControl.CertificateSchedule.layout_public.search')
		<div class="keyword">
			<div class="wrap">
				<p>关键词：<strong>{{ $key_str ?? '' }}</strong></p>
				<div class="c"></div>
			</div>
		</div>
		<div class="list-wrap">
			<div class="wrap">

				<div class="list">

					<ul class="comlist">
                        @foreach ($company_list as $k => $v)
						<li>
							<div class="com-logo">

							</div>
							<div class="com-name">
                                <a href="{{url('jigou/info/' . $v['id'])}}" target="_blank" >{{ $v['company_name'] ?? '' }}</a>
							</div>
							<div class="more">
								<a href="{{url('jigou/info/' . $v['id'])}}" target="_blank" >查看详情</a>
							</div>
							<div class="content-info">
								<p>CMA证书编号：<span>{{ $v['company_certificate_no'] ?? '' }}</span></p>
								<p>证书有效期：<span> {{ $v['certificate_detail']['valid_date'] ?? '' }}</span></p>
								<p>联系人：<span>{{ $v['company_contact_name'] ?? '' }}</span></p>
								<p>联系电话：<span>{{ $v['company_contact_mobile'] ?? '' }}/{{ $v['company_contact_tel'] ?? '' }}</span></p>
								<p style="width: 80%;">联系地址：<span>{{ $v['addr'] ?? '' }}</span></p>
							</div>
							<div class="c"></div>
						</li>
                        @endforeach

					</ul>
                    <div class="mmfoot"><!--
                        <div class="mmfleft"></div> -->
                        <div class="pagination">
                            {!! $pageInfoLink ?? ''  !!}
                        </div>
                    </div>

				</div>

				<div class="list-side">


					<div class="tjcom">
						<div class="hd">
							推荐企业
						</div>
						<div class="bd">
							<ul class="txtlist">
                                @foreach ($company_update_list as $k => $v)
								<li><a href="{{url('jigou/info/' . $v['id'])}}" target="_blank">{{ $v['company_name'] ?? '' }}</a></li>
                                @endforeach
							</ul>
						</div>
					</div>




				</div>

				<div class="c"></div>

			</div>
		</div>

		<div class="c"></div>


		<div class="floor2">
			<div class="wrap">
				<div class="adv1 adva1">权威数据</div>
				<div class="adv1 adva2">精确查询</div>
				<div class="adv1 adva3">实时更新</div>
							<div class="c"></div>
			</div>
		</div>
        @include('web.QualityControl.CertificateSchedule.layout_public.footer')
	</body>
</html>

<script type="text/javascript">
    // var SEARCH_COMPANY_URL = "{ {url('web/certificate/company/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址
    var SEARCH_COMPANY_URL = "{{url('jigou/list/' . ($city_id ?? 0)  . '_' . ($industry_id ?? 0)  . '_0_1')}}";//保存成功后跳转到的地址

</script>
{{--<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>--}}
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script src="{{ asset('/js/web/QualityControl/CertificateSchedule/search.js') }}?2"  type="text/javascript"></script>
