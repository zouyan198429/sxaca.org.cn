<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <title>证书打印---管理后台</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <style>
        html, body, * {
            font: 16px/1.5 "微软雅黑", Helvetica, Tahoma, Arial, "Microsoft jhengHei", sans-serif;	color:#000; background-color:#fff;}
        }
        * {margin: 0; padding: 0;font-size:8pt;color:#000;line-height: 1.5;}
        .zsmain {
            width: 640px;
            margin:20px auto;
            padding:40px 0;
            min-height: 1200px;
            position: relative;
            background:#fEf8e5 url() repeat fixed center;
            box-shadow: 0 0 20px #aaa;

        }
        .zsmain h1 {
            text-align: center;
            line-height: 140%;
            margin-top: 150px;
            margin-bottom: 300px;
            font-size: 36px;
        }
        .company {
            font-size: 20px;
            text-indent: 1em;
        }
        .table-wrap {
            padding: 20px 0;
        }
        .table-wrap table {
            width: 100%;
            border:2px solid #000;
            border-collapse: collapse;
        }
        .table-wrap td,.table-wrap th {
            border-collapse: collapse;
            border:1px solid #000;
            background:#fff;
            padding:10px 5px;
            text-align: center;
            font-size: 14px;
        }
        .tr { text-align: right;}
        .tc { text-align: center;}
        .foot {
            position: absolute;
            right: 80px;
            bottom: 150px;
            z-index: 100;
            font-size: 20px;
            line-height: 180%;
        }
    </style>
</head>
<body>

<div class="layui-card">
    <div class="layui-card-body">
        {{--        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">--}}
        {{--            <legend>证书样式</legend>--}}
        {{--        </fieldset>--}}

        {{--        <div class="layui-card tc">--}}
        {{--            <button type="button" class="layui-btn">打印</button>--}}
        {{--        </div>--}}

        <div class="zsmain">

            <h1>陕西省<br> {{ $info['ability_code_info']['number_year'] ?? '' }}年检验检测能力验证证书</h1>
            <div class="company">
                机构名称：{{ $info['company_name'] ?? '' }}
            </div>
            <div class="table-wrap">
                <table>
                    <colgroup>
                        <col width="250">
                        <col>
                        <col width="150">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>证书编号</th>
                        <th>参加项目</th>
                        <th>结果</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $join_items_print = $info['join_items_print'] ?? [];
                    $item_count = count($join_items_print);
                    ?>
                    @foreach ($join_items_print as $k => $item_info)
                        <tr>
                            @if(isset($k) && $k == 0) <td rowspan="{{ $item_count ?? 1 }}" align="center" valign="top" >{{ $info['company_certificate_no'] ?? '' }}</td> @endif
                            <td>{{ $item_info['ability_name'] ?? '' }}</td>
                            <td class="tc">满意</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="foot tr">
                <h3>{{ $info['ability_code_info']['sign_name'] ?? '' }}</h3>
                <p>{{ $info['ability_code_info']['sign_date'] ?? '' }}</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
