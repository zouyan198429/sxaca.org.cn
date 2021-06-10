<!DOCTYPE html>
<html>
	<head>
        <title>秦检通_陕西省质量认证认可协会_陕西质量认证咨询中心_检验检测能力</title>
        <meta name="keywords" content="秦检通,陕西省质量认证认可协会,陕西质量认证咨询中心,检验检测能力" />
        <meta name="description" content="秦检通,陕西省质量认证认可协会,陕西质量认证咨询中心,检验检测能力" />
        @include('web.QualityControl.CertificateSchedule.layout_public.pagehead')
	</head>
	<body>
        @include('web.QualityControl.CertificateSchedule.layout_public.header')
        @include('web.QualityControl.CertificateSchedule.layout_public.search')

		<div class="dataview">
			<div class="wrap">
				<dl class="dv1 dva1">
					<dt></dt>
					<dd>
						<span>入驻企业</span>
						<strong>{{ $company_count ?? '0' }}</strong>
					</dd>
				</dl>
				<dl class="dv1 dva2">
					<dt></dt>
					<dd><span>可检测产品</span>
						<strong>23846</strong>
					</dd>
				</dl>
				<dl class="dv1 dva3">
					<dt></dt>
					<dd>
						<span>可检测项目</span>
						<strong>255548</strong>
					</dd>
				</dl>
				<div class="c"></div>
			</div>
		</div>




		<div class="floor1">
			<div class="wrap">
				<div class="comtab">
					<div class="hd">
						<ul><li>查询最新注册企业</li><li>最新变更企业</li></ul>
					</div>
					<div class="bd">
						<ul class="comtabul">
                            @foreach ($company_new_list as $k => $v)
							<li>
								<div class="com-logo">

								</div>
								<div class="name">
                                   <a href="{{url('jigou/info/' . $v['id'])}}" target="_blank" >{{ $v['company_name'] ?? '' }}</a>
								</div>
								<div class="date">
									<!-- 注册日期： {{ $v['created_at_fmt'] ?? '' }} -->
									CMA证书编号：<span>{{ $v['company_certificate_no'] ?? '' }}
								</div>
							</li>
                           @endforeach
							<div class="c"></div>
						</ul>
						<ul class="comtabul">
                            @foreach ($company_update_list as $k => $v)
							<li>
								<div class="com-logo">

								</div>
								<div class="name">
                                   <a href="{{url('jigou/info/' . $v['id'])}}" target="_blank" > {{ $v['company_name'] ?? '' }}</a>
								</div>
								<div class="date">
									变更日期：{{ $v['updated_at_fmt'] ?? '' }}
								</div>
							</li>
                            @endforeach
							<div class="c"></div>
						</ul>

					</div>
				</div>
				<script type="text/javascript">jQuery(".comtab").slide();</script>

			</div>


		</div>

		<div class="floor2">
			<div class="wrap">
				<div class="adv1 adva1">权威数据</div>
				<div class="adv1 adva2">精确查询</div>
				<div class="adv1 adva3">实时更新</div>
							<div class="c"></div>
			</div>
		</div>


		<div class="floor3">
			<div class="wrap">

				<h1>企业分布</h1>
				<div class="comtypetab">
					<div class="hd">
						<ul><li>行业分布</li><li>地区分布</li></ul>
					</div>
					<div class="bd">
						<ul class="typetab">

                            @foreach ($industry_list as $k => $v)
                                <li>
                                    <div class="type-name">
                                        {{ $v['industry_name'] ?? '' }}
                                    </div>
                                    <div class="com-data">
                                        {{ $v['company_count'] ?? '0' }}
                                    </div>
                                </li>
                            @endforeach
							<div class="c"></div>
						</ul>
						<ul class="typetab">
                            @foreach ($city_list as $k => $v)
							<li>
								<div class="type-name">
                                    {{ $v['city_name'] ?? '' }}
								</div>
								<div class="com-data">
                                    {{ $v['company_count'] ?? '0' }}
								</div>
							</li>
                            @endforeach
							<div class="c"></div>
						</ul>

					</div>
				</div>
				<script type="text/javascript">jQuery(".comtypetab").slide();</script>


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

