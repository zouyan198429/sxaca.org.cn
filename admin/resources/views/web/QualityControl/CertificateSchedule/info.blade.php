<!DOCTYPE html>
<html>
	<head>
        <title>{{ $info['company_name'] ?? '' }}_{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力_{{ $info['city_name'] ?? '' }}检验检测能力</title>
        <meta name="keywords" content="{{ $info['company_name'] ?? '' }},{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力,{{ $info['city_name'] ?? '' }}检验检测能力" />
        <meta name="description" content="{{ $info['company_name'] ?? '' }},{{ $info['city_name'] ?? '' }}{{ $info['industry_name'] ?? '' }}检验检测能力,{{ $info['city_name'] ?? '' }}检验检测能力" />
        @include('web.QualityControl.CertificateSchedule.layout_public.pagehead')
		<style>
			a.totop {
				position: fixed; right: 20px; bottom: 150px;
				display: block;
				background-color: #fff; border:1px solid #d5d6d8;
				text-align: center; border-radius: 3px; line-height: 50px; height: 50px; width:50px;
			}
			a.totop:hover {
				border:1px solid #d1d1d2;
				background-color: #e4e5e8;
			}
			mark {
				background: orange;
				color: black;
			}
		</style>
	</head>
	<body>
        @include('web.QualityControl.CertificateSchedule.layout_public.header')
		<div class="details-header" id="top">
			<div class="wrap">
				<!-- <div class="com-logo">

				</div> -->
				<div class="com-name">
                    {{ $info['company_name'] ?? '' }}
				</div>
				<div class="content-info">
					<p>CMA证书编号：<span>{{ $info['company_certificate_no'] ?? '' }}</span></p>
					<p>发证日期：<span>{{ $info['certificate_detail']['ratify_date'] ?? '' }}</span></p>
					<p>证书有效期：<span> {{ $info['certificate_detail']['valid_date'] ?? '' }}</span></p>
					<div class="c"></div>
					<p>联系人：<span>{{ $info['company_contact_name'] ?? '' }}</span></p>
					<p>联系电话：<span>{{ $info['company_contact_mobile'] ?? '' }}</span></p>
					<p>联系地址：<span>{{ $info['addr'] ?? '' }}</span></p>

				</div>
				<div class="c"></div>
			</div>
		</div>





{{--        <script src="https://cdn.jsdelivr.net/mark.js/7.0.0/jquery.mark.min.js"></script>--}}
        <script src="{{asset('quality/CertificateSchedule/js/jquery.mark.min.js')}}"></script>

        <script type="text/javascript">

    $(function() {
        $("input").on("input.highlight", function() {
            // Determine specified search term
            var searchTerm = $(this).val();
            // Highlight search term inside a specific context
            $("#context").unmark().mark(searchTerm);
        }).trigger("input.highlight").focus();
    });

</script>

			<!-- <div class="zhengshu box1">
					<div class="hd">资质证书</div>
					<div class="bd">
						<img src="{{asset('quality/CertificateSchedule/images/icon-zs.jpg')}}" alt="" class="icon-zs">
						<div class="mm">
							<p class="f16">计量认证</p>
							<div class="k10"></div>
							<p class="f14">证书编号：</p>
							<p class="f14">{{ $info['certificate_detail']['certificate_no'] ?? '' }}</p>
							<div class="k10"></div>
							<p>证书有效期：</p>
							<p>{{ $info['certificate_detail']['valid_date'] ?? '' }}</p>
						</div>
					</div>
				</div> -->

				<style>
					.ssnavwrap{   text-align:left;  }
					.ssnavwrap .hd{  height: 44px;   position:relative; }
					.ssnavwrap .hd ul{ float:left;  position:absolute; left:0px; top:0px;   }
					.ssnavwrap .hd ul li{ float:left; height: 44px;  line-height: 42px;  padding:0 15px;   cursor:pointer;   }
					.ssnavwrap .hd ul li.on{ height: 42px; line-height: 42px;  background-color: #fff; border-top:2px solid #0060CD;border-bottom:2px solid #fff; }

					.ssnavwrap .bd ul{   zoom:1;  }
					input.searloc {
						position: absolute; top:4px; right: 20px;
						height: 32px; width: 350px;
						border:1px solid #888;
					}
				</style>
		<div class="ssnavwrap" id="context">
					<div class="ssnav">
						<div class="hd inner">
							<ul><li>批准的授权签字人及领域</li><li>批准的检验检测能力能力范围</li></ul>
							<input type="text" value="" class="searloc" placeholder="输入关键词查询">
						</div>
					</div>
					<div class="bd">
						<ul>
							<div class="det-floor1" >
									<div class="qianziren box1">
										<div class="hd">批准的授权签字人及领域</div>
										<div class="bd">
											<table class="table" style="width: 100%;">
												<colgroup>
													<col width="200">
												    <col width="240">
												    <col width="150">
												    <col width="240">
												    <col>
												</colgroup>
												<thead>
													<tr align="center">
														<th>姓名</th>
														<th>职务</th>
														<th>手机</th>
														<th>身份证号</th>
														<th>批准授权签字范围</th>
													</tr>
												</thead>
												<tbody>
							                    <?php
							                    $user_auth_list = $info['user_auth_list'] ?? [];
							                    ?>
							                    @foreach ($user_auth_list as $k => $user_info)
															<tr>
																<td align="center">{{ $user_info['real_name'] ?? '' }}</td>

																<td>{{ $user_info['role_num_text'] ?? '' }}</td>
																<td>{{ $user_info['mobile'] ?? '' }}</td>
																<td>{{ $user_info['id_number'] ?? '' }}</td>
																<td>{{ $user_info['sign_range'] ?? '' }}</td>
															</tr>
							                    @endforeach
												</tbody>
											</table>
										</div>
									</div>

									<div class="c"></div>

							</div>


						</ul>
						<ul>
                            <?php
                            $i = 1;
                            $certificate_list = $info['certificate_list'] ?? [];
                            $addrArr = \App\Services\Tool::getArrFields($certificate_list, 'addr');
                            ?>

							<div class="wrap" style="width: 80%; margin-top:20px;">

                                <select class="wmini"  name="select_addr"  style="display: block; width:360px; margin-bottom: 20px;">
                                    <option value="">所有地址</option>
                                    @foreach ($addrArr as $addr)
                                        <option value="{{ $addr }}"  >{{ $addr }}</option>
                                    @endforeach
                                </select>
								<div class="box1" style="min-height: 500px;">
									<div class="hd">
										检验检测能力表
									</div>
									<div class="bd">
										<table border="" cellspacing="" cellpadding="" class="table wb100">
											<colgroup>
												  <col width="50">
												  <col width="100">
												  <col width="100">
												  <col width="100">
												  <col width="100">
												  <col width="150">
												  <col >
												  <col>
												  <col>
												  <col width="150">
												  <col width="100">
											</colgroup>
											<thead>
												<tr align="center">
													<th>ID</th>
													<th>
{{--                                                        产品类别--}}
                                                        一级分类
                                                    </th>
													<th>
{{--                                                        检测产品--}}
                                                        二级分类
                                                    </th>
                                                    <th>
{{--                                                        检测产品--}}
                                                        三级分类
                                                    </th>
                                                    <th>
{{--                                                        检测产品--}}
                                                        四级分类
                                                    </th>
													<th>
{{--                                                        检测参数--}}
                                                        项目名称
                                                    </th>
													<th>依据的标准（方法）</th>
													<th>限制范围</th>
							                        <th>说明</th>
													<th>场所地址</th>
													<th>批准日期</th>
												</tr>
											</thead>
											<tbody id="data_list" class="certificate_list">
							                @foreach ($certificate_list as $k => $v)
												<tr>
													<td  align="center" class="ID">{{ $i ?? '' }}</td>
													<td  align="center" class="category_name">{{ $v['category_name'] ?? '' }}<span style="color: #fff;"> {{ $v['id'] ?? '' }}</span></td>
													<td  align="center" class="project_name">{{ $v['project_name'] ?? '' }}</td>
                                                    <td class="param_name">{{ $v['three_name'] ?? '' }}</td>
                                                    <td class="param_name">{{ $v['four_name'] ?? '' }}</td>
													<td class="param_name">{{ $v['param_name'] ?? '' }}</td>
													<td class="method_name">{!! $v['method_name'] ?? '' !!}</td>
													<td class="limit_range">{!! $v['limit_range'] ?? '' !!}</td>
							                        <td class="explain_text">{!! $v['explain_text'] ?? '' !!}</td>
													<td class="addr">{{ $v['addr'] ?? '' }}</td>
													<td class="ratify_date">{{ $v['ratify_date'] ?? '' }}</td>
												</tr>
												<?php
												$i++;
							                ?>
							                @endforeach
											</tbody>
										</table>
									</div>

								</div>
							</div>


						</ul>


					</div>
				</div>
				<script type="text/javascript">jQuery(".ssnavwrap").slide();</script>
		<div class="k20"></div>



		<div class="c"></div>
		<div class="k20"></div>
		<div class="k50"></div>

		<a href="#top" target="_self" class="totop" >顶部</a>
		<!-- <div class="floor2">
			<div class="wrap">
				<div class="adv1 adva1">权威数据</div>
				<div class="adv1 adva2">精确查询</div>
				<div class="adv1 adva3">实时更新</div>
							<div class="c"></div>
			</div>
		</div> -->
        @include('web.QualityControl.CertificateSchedule.layout_public.footer')
	</body>
</html>

<script src="{{asset('static/js/custom/common.js')}}?12"></script>
<script src="{{ asset('/js/web/QualityControl/CertificateSchedule/info.js') }}?4"  type="text/javascript"></script>
