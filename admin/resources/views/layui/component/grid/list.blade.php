

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>等比例列表排列</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
  <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>


  <style>
  /* 这段样式只是用于演示 */
  #LAY-component-grid-list .demo-list .layui-card{height: 267px;}
  </style>

  <div class="layui-fluid" id="LAY-component-grid-list">
    <div class="layui-row layui-col-space10 demo-list">
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <!-- 填充内容 -->
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">  
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
      <div class="layui-col-sm4 layui-col-md3 layui-col-lg2">
        <div class="layui-card">
          
        </div>
      </div>
    </div>
  </div>

  <script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>
  <script>
  layui.config({
    base: '/layui-admin-v1.2.1/src/layuiadmin/' //静态资源所在路径
  }).extend({
    index: 'lib/index' //主入口模块
  }).use(['index']);
  </script>
</body>
</html>