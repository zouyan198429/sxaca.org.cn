

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>开启头部工具栏 - 数据表格</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  @include('admin.layout_public.pagehead')
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

<div class="mm">
  <div class="mmhead" id="mywork">

  <table lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">

      <?php
      $industry_kv = $industry_count['industry_kv'] ?? [];
      $grade_kv = $industry_count['grade_kv'] ?? [];
      $data_list = $industry_count['data_list'] ?? [];
      ?>
    <thead>
    <tr>
      <th>行业</th>
        @foreach ($grade_kv as $k => $v)
        <th>{{ $v ?? '' }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach ($industry_kv as $industry_id => $industry_name)
       <tr>
           <td>{{ $industry_name ?? '' }}</td>
           @foreach ($grade_kv as $grade_id => $grade_name)
               <td>{{ $data_list[$industry_id . '_' . $grade_id]['company_count'] ?? 0 }}</td>
           @endforeach
       </tr>
    @endforeach
    </tbody>
  </table>

</div>
</div>
</body>
</html>
