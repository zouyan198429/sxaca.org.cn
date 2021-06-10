<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// welcome
//Route::get('/welcome', 'IndexController@welcome');
Route::get('/', 'IndexController@index');// 首页
//Route::get('/aaa', function () {
//    echo 'aaa';
//    // return view('welcome');
//});
//Route::view('clients', 'clients');

 Route::get('/test', 'IndexController@test');// 测试
//Route::get('/test2', 'IndexController@test2');// 测试
// Route::get('/', 'IndexController@index');// 首页-- 用这个

//Route::get('reg', 'IndexController@reg');// 注册
//Route::get('login', 'IndexController@login');// 登陆
//Route::get('logout', 'IndexController@logout');// 注销
//Route::get('404', 'IndexController@err404');// 404错误


// layuiAdmin
Route::get('layui/index', 'Layui\IndexController@index');// index.html
Route::get('layui/iframe/layer/iframe', 'Layui\Iframe\LayerController@iframe');// iframe/layer/iframe.html layer iframe 示例
Route::get('layui/system/about', 'Layui\SystemController@about');// system/about.html 版本信息 --***
Route::get('layui/system/get', 'Layui\SystemController@get');// system/get.html 授权获得 layuiAdmin --***
Route::get('layui/system/more', 'Layui\SystemController@more');// system/more.html 更多面板的模板 --***
Route::get('layui/system/theme', 'Layui\SystemController@theme');// system/theme.html 主题设置模板 --***
// 主页
Route::get('layui/home/console', 'Layui\HomeController@console');// 控制台 home/console.html
Route::get('layui/home/homepage1', 'Layui\HomeController@homepage1');// 主页一 home/homepage1.html
Route::get('layui/home/homepage2', 'Layui\HomeController@homepage2');// 主页二 home/homepage2.html
// 组件
Route::get('layui/component/laytpl/index', 'Layui\Component\LaytplController@index');// component/laytpl/index.html  模板引擎  --***
// 栅格
Route::get('layui/component/grid/list', 'Layui\Component\GridController@list');// 等比例列表排列 component/grid/list.html
Route::get('layui/component/grid/mobile', 'Layui\Component\GridController@mobile');// 按移动端排列 component/grid/mobile.html
Route::get('layui/component/grid/mobile-pc', 'Layui\Component\GridController@mobilePc');// 移动桌面端组合 component/grid/mobile-pc.html
Route::get('layui/component/grid/all', 'Layui\Component\GridController@all');// 全端复杂组合 component/grid/all.html
Route::get('layui/component/grid/stack', 'Layui\Component\GridController@stack');// 低于桌面堆叠排列 component/grid/stack.html
Route::get('layui/component/grid/speed-dial', 'Layui\Component\GridController@speedDial');// 九宫格 component/grid/speed-dial.html

Route::get('layui/component/button/index', 'Layui\Component\ButtonController@index');// 按钮  component/button/index.html
// 表单
Route::get('layui/component/form/element', 'Layui\Component\FormController@element');// 表单元素 component/form/element.html
Route::get('layui/component/form/group', 'Layui\Component\FormController@group');// 表单组合 component/form/group.html

Route::get('layui/component/nav/index', 'Layui\Component\NavController@index');// 导航  component/nav/index.html
Route::get('layui/component/tabs/index', 'Layui\Component\TabsController@index');// 选项卡 component/tabs/index.html
Route::get('layui/component/progress/index', 'Layui\Component\ProgressController@index');// 进度条 component/progress/index.html
Route::get('layui/component/panel/index', 'Layui\Component\PanelController@index');// 面板 component/panel/index.html
Route::get('layui/component/badge/index', 'Layui\Component\BadgeController@index');// 徽章 component/badge/index.html
Route::get('layui/component/timeline/index', 'Layui\Component\TimelineController@index');// 时间线 component/timeline/index.html
Route::get('layui/component/anim/index', 'Layui\Component\AnimController@index');// 动画 component/anim/index.html
Route::get('layui/component/auxiliar/index', 'Layui\Component\AuxiliarController@index');// 辅助 component/auxiliar/index.html
// 通用弹层
Route::get('layui/component/layer/list', 'Layui\Component\LayerController@list');// 功能演示 component/layer/list.html
Route::get('layui/component/layer/special-demo', 'Layui\Component\LayerController@specialDemo');// 特殊示例 component/layer/special-demo.html
Route::get('layui/component/layer/theme', 'Layui\Component\LayerController@theme');// 风格定制 component/layer/theme.html
// 日期时间
Route::get('layui/component/laydate/index', 'Layui\Component\LaydateController@index');// component/laydate/index.html  日期组件 --***
Route::get('layui/component/laydate/demo1', 'Layui\Component\LaydateController@demo1');// 功能演示一 component/laydate/demo1.html
Route::get('layui/component/laydate/demo2', 'Layui\Component\LaydateController@demo2');// 功能演示二 component/laydate/demo2.html
Route::get('layui/component/laydate/theme', 'Layui\Component\LaydateController@theme');// 设定主题 component/laydate/theme.html
Route::get('layui/component/laydate/special-demo', 'Layui\Component\LaydateController@specialDemo');// 特殊示例 component/laydate/special-demo.html

Route::get('layui/component/table/static', 'Layui\Component\TableController@static');// 静态表格 component/table/static.html
// 数据表格
Route::get('layui/component/table/index', 'Layui\Component\TableController@index');// component/table/index.html  表格 --***
Route::get('layui/component/temp', 'Layui\Component\TableController@temp');// component/temp.html  简单用法 - 数据表格 --***
Route::get('layui/component/table/simple', 'Layui\Component\TableController@simple');// 简单数据表格 component/table/simple.html
Route::get('layui/component/table/auto', 'Layui\Component\TableController@auto');// 列宽自动分配 component/table/auto.html
Route::get('layui/component/table/data', 'Layui\Component\TableController@data');// 赋值已知数据 component/table/data.html
Route::get('layui/component/table/tostatic', 'Layui\Component\TableController@tostatic');// 转化静态表格 component/table/tostatic.html
Route::get('layui/component/table/page', 'Layui\Component\TableController@page');// 开启分页 component/table/page.html
Route::get('layui/component/table/resetPage', 'Layui\Component\TableController@resetPage');// 自定义分页 component/table/resetPage.html
Route::get('layui/component/table/toolbar', 'Layui\Component\TableController@toolbar');// 开启头部工具栏 component/table/toolbar.html
Route::get('layui/component/table/totalRow', 'Layui\Component\TableController@totalRow');// 开启合计行 component/table/totalRow.html
Route::get('layui/component/table/height', 'Layui\Component\TableController@height');// 高度最大适应 component/table/height.html
Route::get('layui/component/table/checkbox', 'Layui\Component\TableController@checkbox');// 开启复选框 component/table/checkbox.html
Route::get('layui/component/table/radio', 'Layui\Component\TableController@radio');// 开启单选框 component/table/radio.html
Route::get('layui/component/table/cellEdit', 'Layui\Component\TableController@cellEdit');// 开启单元格编辑 component/table/cellEdit.html
Route::get('layui/component/table/form', 'Layui\Component\TableController@form');// 加入表单元素 component/table/form.html
Route::get('layui/component/table/style', 'Layui\Component\TableController@style');// 设置单元格样式 component/table/style.html
Route::get('layui/component/table/fixed', 'Layui\Component\TableController@fixed');// 固定列 component/table/fixed.html
Route::get('layui/component/table/operate', 'Layui\Component\TableController@operate');// 数据操作 component/table/operate.html
Route::get('layui/component/table/parseData', 'Layui\Component\TableController@parseData');// 解析任意数据格式 component/table/parseData.html
Route::get('layui/component/table/onrow', 'Layui\Component\TableController@onrow');// 监听行事件 component/table/onrow.html
Route::get('layui/component/table/reload', 'Layui\Component\TableController@reload');// 数据表格的重载 component/table/reload.html
Route::get('layui/component/table/initSort', 'Layui\Component\TableController@initSort');// 设置初始排序 component/table/initSort.html
Route::get('layui/component/table/cellEvent', 'Layui\Component\TableController@cellEvent');// 监听单元格事件 component/table/cellEvent.html
Route::get('layui/component/table/thead', 'Layui\Component\TableController@thead');// 复杂表头 component/table/thead.html
// 分页
Route::get('layui/component/laypage/index', 'Layui\Component\LaypageController@index');// component/laypage/index.html  通用分页组件 --***
Route::get('layui/component/laypage/demo1', 'Layui\Component\LaypageController@demo1');// 功能演示一 component/laypage/demo1.html
Route::get('layui/component/laypage/demo2', 'Layui\Component\LaypageController@demo2');// 功能演示二 component/laypage/demo2.html
// 上传
Route::get('layui/component/upload/index', 'Layui\Component\UploadController@index');// component/upload/index.html 上传 --***
Route::get('layui/component/upload/demo1', 'Layui\Component\UploadController@demo1');// 功能演示一 component/upload/demo1.html
Route::get('layui/component/upload/demo2', 'Layui\Component\UploadController@demo2');// 功能演示二 component/upload/demo2.html

Route::get('layui/component/colorpicker/index', 'Layui\Component\ColorpickerController@index');// 颜色选择器 component/colorpicker/index.html
Route::get('layui/component/slider/index', 'Layui\Component\SliderController@index');// 滑块组件 component/slider/index.html
Route::get('layui/component/rate/index', 'Layui\Component\RateController@index');// 评分 component/rate/index.html
Route::get('layui/component/carousel/index', 'Layui\Component\CarouselController@index');// 轮播 component/carousel/index.html
Route::get('layui/component/flow/index', 'Layui\Component\FlowController@index');// 流加载 component/flow/index.html
Route::get('layui/component/util/index', 'Layui\Component\UtilController@index');// 工具 component/util/index.html
Route::get('layui/component/code/index', 'Layui\Component\CodeController@index');// 代码修饰 component/code/index.html

// 页面
Route::get('layui/template/personalpage', 'Layui\TemplateController@personalpage');// 个人主页 template/personalpage.html
Route::get('layui/template/addresslist', 'Layui\TemplateController@addresslist');// 通讯录 template/addresslist.html
Route::get('layui/template/caller', 'Layui\TemplateController@caller');// 客户列表 template/caller.html
Route::get('layui/template/goodslist', 'Layui\TemplateController@goodslist');// 商品列表 template/goodslist.html
Route::get('layui/template/msgboard', 'Layui\TemplateController@msgboard');// 留言板 template/msgboard.html
Route::get('layui/template/search', 'Layui\TemplateController@search');// 搜索结果 template/search.html
Route::get('layui/template/temp', 'Layui\TemplateController@temp');// template/temp.html --***


Route::get('layui/user/reg', 'Layui\UserController@reg');// 注册 user/reg.html
Route::get('layui/user/login', 'Layui\UserController@login');// 登入 user/login.html
Route::get('layui/user/forget', 'Layui\UserController@forget');// 忘记密码 user/forget.html

Route::get('layui/template/tips/404', 'Layui\Template\TipsController@err404');// 404页面不存在 template/tips/404.html
Route::get('layui/template/tips/error', 'Layui\Template\TipsController@error');// 错误提示 template/tips/error.html
// 百度一下 //www.baidu.com/
// layui官网 //www.layui.com/
// layuiAdmin官网 //www.layui.com/admin/
// 应用
//    内容系统
Route::get('layui/app/content/list', 'Layui\App\ContentController@list');// 文章列表 app/content/list.html
Route::get('layui/app/content/tags', 'Layui\App\ContentController@tags');// 分类管理 app/content/tags.html
Route::get('layui/app/content/comment', 'Layui\App\ContentController@comment');// 评论管理 app/content/comment.html
Route::get('layui/app/content/contform', 'Layui\App\ContentController@contform');// app/content/contform.html  评论管理 iframe 框 --***
Route::get('layui/app/content/listform', 'Layui\App\ContentController@listform');// app/content/listform.html  文章管理 iframe 框 --***
Route::get('layui/app/content/tagsform', 'Layui\App\ContentController@tagsform');// app/content/tagsform.html  分类管理 iframe 框
//    社区系统
Route::get('layui/app/forum/list', 'Layui\App\ForumController@list');// 帖子列表 app/forum/list.html
Route::get('layui/app/forum/replys', 'Layui\App\ForumController@replys');// 回帖列表 app/forum/replys.html
Route::get('layui/app/forum/listform', 'Layui\App\ForumController@listform');// app/forum/listform.html  帖子管理 iframe 框 --***
Route::get('layui/app/forum/replysform', 'Layui\App\ForumController@replysform');// app/forum/replysform.html  回帖管理 iframe 框 --***

Route::get('layui/app/message/index', 'Layui\App\MessageController@index');// 消息中心 app/message/index.html
Route::get('layui/app/message/detail', 'Layui\App\MessageController@detail');// app/message/detail.html  消息详情标题 --***

Route::get('layui/app/workorder/list', 'Layui\App\WorkorderController@list');// 工单系统 app/workorder/list.html
Route::get('layui/app/workorder/listform', 'Layui\App\WorkorderController@listform');// app/workorder/listform.html 工单管理 iframe 框

Route::get('layui/app/mall/category', 'Layui\App\MallController@category');// app/mall/category.html  分类管理 --***
Route::get('layui/app/mall/list', 'Layui\App\MallController@list');// app/mall/list.html  商品列表 --***
Route::get('layui/app/mall/specs', 'Layui\App\MallController@specs');// app/mall/specs.html  规格管理 --***
//  高级
//    LayIM 通讯系统
Route::get('layui/senior/im/index', 'Layui\Senior\ImController@index');// senior/im/index.html  LayIM 社交聊天 --***
//    Echarts集成
Route::get('layui/senior/echarts/line', 'Layui\Senior\EchartsController@line');// 折线图 senior/echarts/line.html
Route::get('layui/senior/echarts/bar', 'Layui\Senior\EchartsController@bar');// 柱状图 senior/echarts/bar.html
Route::get('layui/senior/echarts/map', 'Layui\Senior\EchartsController@map');// 地图  senior/echarts/map.html
// 用户
Route::get('layui/user/user/list', 'Layui\User\UserController@list');// 网站用户 user/user/list.html
Route::get('layui/user/user/userform', 'Layui\User\UserController@userform');// user/user/userform.html  网站用户 iframe 框

Route::get('layui/user/administrators/list', 'Layui\User\AdministratorsController@list');// 后台管理员 user/administrators/list.html
Route::get('layui/user/administrators/role', 'Layui\User\AdministratorsController@role');// 角色管理 user/administrators/role.html
Route::get('layui/user/administrators/adminform', 'Layui\User\AdministratorsController@adminform');// user/administrators/adminform.html 管理员 iframe 框
Route::get('layui/user/administrators/roleform', 'Layui\User\AdministratorsController@roleform');// user/administrators/roleform.html 角色管理 iframe 框

// 设置
//    系统设置
Route::get('layui/set/system/website', 'Layui\Set\SystemController@website');// 网站设置 set/system/website.html
Route::get('layui/set/system/email', 'Layui\Set\SystemController@email');// 邮件服务 set/system/email.html
//    我的设置
Route::get('layui/set/user/info', 'Layui\Set\UserController@info');// 基本资料 set/user/info.html
Route::get('layui/set/user/password', 'Layui\Set\UserController@password');// 修改密码 set/user/password.html
// 授权  //www.layui.com/admin/#get

// --- 质量认证认可协会
// --  数据查看人员后台

// 首页
Route::get('expert/test', 'Expert\QualityControl\IndexController@test');// 测试
Route::get('expert/index', 'Expert\QualityControl\IndexController@index');// 首页--ok
Route::get('expert', 'Expert\QualityControl\IndexController@index');// --ok
Route::get('expert/login', 'Expert\QualityControl\IndexController@login');//login.html 登录--ok
Route::get('expert/logout', 'Expert\QualityControl\IndexController@logout');// 注销--ok
Route::get('expert/password', 'Expert\QualityControl\IndexController@password');//psdmodify.html 个人信息-修改密码--ok
Route::get('expert/info', 'Expert\QualityControl\IndexController@info');//myinfo.html 个人信息--显示--ok
Route::get('expert/down_drive', 'Expert\QualityControl\IndexController@down_drive');// 下载网页打印机驱动

Route::get('expert/down_file', 'Expert\QualityControl\IndexController@down_file');// 下载文件

// 企业帐号管理
Route::get('expert/company', 'Expert\QualityControl\CompanyController@index');// 列表
//Route::get('expert/company/add/{id}', 'Expert\QualityControl\CompanyController@add');// 添加
Route::get('expert/company/select', 'Expert\QualityControl\CompanyController@select');// 选择-弹窗
//Route::get('expert/company/export', 'Expert\QualityControl\CompanyController@export');// 导出
//Route::get('expert/company/import_template', 'Expert\QualityControl\CompanyController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

//Route::get('expert/company/grade_area', 'Expert\QualityControl\CompanyController@grade_area');// 会员地区分布统计
//Route::get('expert/company/grade_industry', 'Expert\QualityControl\CompanyController@grade_industry');// 会员行业分布统计

// 能力验证
Route::get('expert/abilitys', 'Expert\QualityControl\AbilitysController@index');// 列表
//Route::get('expert/abilitys/add/{id}', 'Expert\QualityControl\AbilitysController@add');// 添加
Route::get('expert/abilitys/info/{id}', 'Expert\QualityControl\AbilitysController@info');// 查看-详情
Route::get('expert/abilitys/select', 'Expert\QualityControl\AbilitysController@select');// 选择-弹窗
Route::get('expert/abilitys/export', 'Expert\QualityControl\AbilitysController@export');// 导出
Route::get('expert/abilitys/export_join/{ability_id}', 'Expert\QualityControl\AbilitysController@export_join');// 导出报名的企业信息
Route::get('expert/abilitys/import_template', 'Expert\QualityControl\AbilitysController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面
//Route::get('expert/abilitys/publish/{id}', 'Expert\QualityControl\AbilitysController@publish');// 公布结果页面

//****************************************************************************
// 能力验证管理
Route::get('expert/abilitys_admin/{ability_id}', 'Expert\QualityControl\Abilitys\IndexController@index');// 首页
Route::get('expert/abilitys_admin/{ability_id}/basic', 'Expert\QualityControl\Abilitys\IndexController@basic');// 基础信息

// 能力验证--报名管理--参加单位
Route::get('expert/abilitys_admin/{ability_id}/ability_join_items', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@index');// 列表
//Route::get('expert/abilitys_admin/{ability_id}/ability_join_items/add/{id}', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@add');// 添加
// Route::get('expert/abilitys_admin/{ability_id}/ability_join_items/info/{id}', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@info');// 查看-详情
// Route::get('expert/abilitys_admin/{ability_id}/ability_join_items/select', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@select');// 选择-弹窗
//Route::get('expert/abilitys_admin/{ability_id}/ability_join_items/export', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@export');// 导出
//Route::get('expert/abilitys_admin/{ability_id}/ability_join_items/import_template', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

Route::get('expert/abilitys_admin/{ability_id}/ability_join_items/sample_result_info/{item_id}/{retry_no}', 'Expert\QualityControl\Abilitys\AbilityJoinItemsController@sample_result_info');// 查看上传的数据

// 能力验证结果--报名管理--参加单位
Route::get('expert/abilitys_admin/{ability_id}/ability_join_items_results', 'Expert\QualityControl\Abilitys\AbilityJoinItemsResultsController@index');// 列表
Route::get('expert/abilitys_admin/{ability_id}/ability_join_items_results/add/{id}', 'Expert\QualityControl\Abilitys\AbilityJoinItemsResultsController@add');// 添加--判定
Route::get('expert/abilitys_admin/{ability_id}/ability_join_items_results/export', 'Expert\QualityControl\Abilitys\AbilityJoinItemsResultsController@export');// 导出
Route::get('expert/abilitys_admin/{ability_id}/ability_join_items_results/get_sample/{id}', 'Expert\QualityControl\Abilitys\AbilityJoinItemsResultsController@get_sample');// 取样

//****************************************************************************

// --- 质量认证认可协会
// -- 后台


// 首页
Route::get('admin/test', 'Admin\QualityControl\IndexController@test');// 测试
Route::get('admin/index', 'Admin\QualityControl\IndexController@index');// 首页--ok
Route::get('admin', 'Admin\QualityControl\IndexController@index');// --ok
Route::get('admin/login', 'Admin\QualityControl\IndexController@login');//login.html 登录--ok
Route::get('admin/logout', 'Admin\QualityControl\IndexController@logout');// 注销--ok
Route::get('admin/password', 'Admin\QualityControl\IndexController@password');//psdmodify.html 个人信息-修改密码--ok
Route::get('admin/info', 'Admin\QualityControl\IndexController@info');//myinfo.html 个人信息--显示--ok
Route::get('admin/down_drive', 'Admin\QualityControl\IndexController@down_drive');// 下载网页打印机驱动

Route::get('admin/down_file', 'Admin\QualityControl\IndexController@down_file');// 下载文件

// 系统管理员
Route::get('admin/staff', 'Admin\QualityControl\StaffController@index');// 列表
Route::get('admin/staff/add/{id}', 'Admin\QualityControl\StaffController@add');// 添加
// Route::get('admin/staff/select', 'Admin\QualityControl\StaffController@select');// 选择-弹窗
Route::get('admin/staff/export', 'Admin\QualityControl\StaffController@export');// 导出
Route::get('admin/staff/import_template', 'Admin\QualityControl\StaffController@import_template');// 导入模版
Route::get('admin/staff/sms_send', 'Admin\QualityControl\StaffController@sms_send');// 选择短信模板页面

// 专家
Route::get('admin/expert', 'Admin\QualityControl\ExpertController@index');// 列表
Route::get('admin/expert/add/{id}', 'Admin\QualityControl\ExpertController@add');// 添加
// Route::get('admin/expert/select', 'Admin\QualityControl\ExpertController@select');// 选择-弹窗
Route::get('admin/expert/export', 'Admin\QualityControl\ExpertController@export');// 导出
Route::get('admin/expert/import_template', 'Admin\QualityControl\ExpertController@import_template');// 导入模版
Route::get('admin/expert/sms_send', 'Admin\QualityControl\ExpertController@sms_send');// 选择短信模板页面

// 第三方服务商
Route::get('admin/third_service', 'Admin\QualityControl\ThirdServiceController@index');// 列表
Route::get('admin/third_service/add/{id}', 'Admin\QualityControl\ThirdServiceController@add');// 添加
Route::get('admin/third_service/select', 'Admin\QualityControl\ThirdServiceController@select');// 选择-弹窗
Route::get('admin/third_service/export', 'Admin\QualityControl\ThirdServiceController@export');// 导出
Route::get('admin/third_service/import_template', 'Admin\QualityControl\ThirdServiceController@import_template');// 导入模版
Route::get('admin/third_service/sms_send', 'Admin\QualityControl\ThirdServiceController@sms_send');// 选择短信模板页面

// 企业帐号管理
Route::get('admin/company', 'Admin\QualityControl\CompanyController@index');// 列表
Route::get('admin/company/add/{id}', 'Admin\QualityControl\CompanyController@add');// 添加
 Route::get('admin/company/select', 'Admin\QualityControl\CompanyController@select');// 选择-弹窗
Route::get('admin/company/export', 'Admin\QualityControl\CompanyController@export');// 导出
Route::get('admin/company/import_template', 'Admin\QualityControl\CompanyController@import_template');// 导入模版
Route::get('admin/company/sms_send', 'Admin\QualityControl\CompanyController@sms_send');// 选择短信模板页面

Route::get('admin/company/grade_area', 'Admin\QualityControl\CompanyController@grade_area');// 会员地区分布统计
Route::get('admin/company/grade_industry', 'Admin\QualityControl\CompanyController@grade_industry');// 会员行业分布统计
// 个从帐号管理
Route::get('admin/user', 'Admin\QualityControl\UserController@index');// 列表
Route::get('admin/user/add/{id}', 'Admin\QualityControl\UserController@add');// 添加
Route::get('admin/user/show/{company_id}', 'Admin\QualityControl\UserController@show');// 查看
Route::get('admin/user/show_add/{id}', 'Admin\QualityControl\UserController@show_add');// 添加
// Route::get('admin/user/select', 'Admin\QualityControl\UserController@select');// 选择-弹窗
Route::get('admin/user/export', 'Admin\QualityControl\UserController@export');// 导出
Route::get('admin/user/import_template', 'Admin\QualityControl\UserController@import_template');// 导入模版
Route::get('admin/user/sms_send', 'Admin\QualityControl\UserController@sms_send');// 选择短信模板页面
Route::get('admin/user/import_bath/{company_id}', 'Admin\QualityControl\UserController@import_bath');// 导入批量

// 选民组表
Route::get('admin/voter_group', 'Admin\QualityControl\VoterGroupController@index');// 列表
Route::get('admin/voter_group/add/{id}', 'Admin\QualityControl\VoterGroupController@add');// 添加
// Route::get('admin/voter_group/select', 'Admin\QualityControl\VoterGroupController@select');// 选择-弹窗
Route::get('admin/voter_group/export', 'Admin\QualityControl\VoterGroupController@export');// 导出
Route::get('admin/voter_group/import_template', 'Admin\QualityControl\VoterGroupController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 选民表
Route::get('admin/voters', 'Admin\QualityControl\VotersController@index');// 列表
Route::get('admin/voters/add/{id}', 'Admin\QualityControl\VotersController@add');// 添加
// Route::get('admin/voters/select', 'Admin\QualityControl\VotersController@select');// 选择-弹窗
Route::get('admin/voters/export', 'Admin\QualityControl\VotersController@export');// 导出
Route::get('admin/voters/import_template', 'Admin\QualityControl\VotersController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 行业[一级分类]
Route::get('admin/industry', 'Admin\QualityControl\IndustryController@index');// 列表
Route::get('admin/industry/add/{id}', 'Admin\QualityControl\IndustryController@add');// 添加
// Route::get('admin/industry/select', 'Admin\QualityControl\IndustryController@select');// 选择-弹窗
Route::get('admin/industry/export', 'Admin\QualityControl\IndustryController@export');// 导出
Route::get('admin/industry/import_template', 'Admin\QualityControl\IndustryController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 课程管理
Route::get('admin/course', 'Admin\QualityControl\CourseController@index');// 列表
Route::get('admin/course/add/{id}', 'Admin\QualityControl\CourseController@add');// 添加
// Route::get('admin/course/select', 'Admin\QualityControl\CourseController@select');// 选择-弹窗
Route::get('admin/course/export', 'Admin\QualityControl\CourseController@export');// 导出
Route::get('admin/course/import_template', 'Admin\QualityControl\CourseController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 培训班管理
Route::get('admin/course_class', 'Admin\QualityControl\CourseClassController@index');// 列表
Route::get('admin/course_class/add/{id}', 'Admin\QualityControl\CourseClassController@add');// 添加
// Route::get('admin/course_class/select', 'Admin\QualityControl\CourseClassController@select');// 选择-弹窗
Route::get('admin/course_class/export', 'Admin\QualityControl\CourseClassController@export');// 导出
Route::get('admin/course_class/import_template', 'Admin\QualityControl\CourseClassController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 报名企业(主表)
Route::get('admin/course_order', 'Admin\QualityControl\CourseOrderController@index');// 列表
//Route::get('admin/course_order/add/{id}', 'Admin\QualityControl\CourseOrderController@add');// 添加
Route::get('admin/course_order/info/{id}', 'Admin\QualityControl\CourseOrderController@info');// 查看-详情
// Route::get('admin/course_order/select', 'Admin\QualityControl\CourseOrderController@select');// 选择-弹窗
Route::get('admin/course_order/export', 'Admin\QualityControl\CourseOrderController@export');// 导出
// Route::get('admin/course_order/import_template', 'Admin\QualityControl\CourseOrderController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

Route::get('admin/course_order/pay', 'Admin\QualityControl\CourseOrderController@pay');// 缴费

// 培训班企业管理
Route::get('admin/course_class_company', 'Admin\QualityControl\CourseClassCompanyController@index');// 列表
//Route::get('admin/course_class_company/add/{id}', 'Admin\QualityControl\CourseClassCompanyController@add');// 添加
Route::get('admin/course_class_company/info/{id}', 'Admin\QualityControl\CourseClassCompanyController@info');// 查看-详情
// Route::get('admin/course_class_company/select', 'Admin\QualityControl\CourseClassCompanyController@select');// 选择-弹窗
Route::get('admin/course_class_company/export', 'Admin\QualityControl\CourseClassCompanyController@export');// 导出
// Route::get('admin/course_class_company/import_template', 'Admin\QualityControl\CourseClassCompanyController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 报名学员
Route::get('admin/course_order_staff', 'Admin\QualityControl\CourseOrderStaffController@index');// 列表
Route::get('admin/course_order_staff/add/{id}', 'Admin\QualityControl\CourseOrderStaffController@add');// 添加
// Route::get('admin/course_order_staff/select', 'Admin\QualityControl\CourseOrderStaffController@select');// 选择-弹窗
Route::get('admin/course_order_staff/export', 'Admin\QualityControl\CourseOrderStaffController@export');// 导出
//Route::get('admin/course_order_staff/import_template', 'Admin\QualityControl\CourseOrderStaffController@import_template');// 导入模版
Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

Route::get('admin/course_order_staff/join_class', 'Admin\QualityControl\CourseOrderStaffController@join_class');// 分班
Route::get('admin/course_order_staff/pay', 'Admin\QualityControl\CourseOrderStaffController@pay');// 缴费
Route::get('admin/course_order_staff/pay_save', 'Admin\QualityControl\CourseOrderStaffController@pay_save');// 缴费页面
Route::get('admin/course_order_staff/test', 'Admin\QualityControl\CourseOrderStaffController@test');// 测试

// 面授操作日志
Route::get('admin/course_log', 'Admin\QualityControl\CourseLogController@index');// 列表
Route::get('admin/course_log/add/{id}', 'Admin\QualityControl\CourseLogController@add');// 添加
// Route::get('admin/course_log/select', 'Admin\QualityControl\CourseLogController@select');// 选择-弹窗
Route::get('admin/course_log/export', 'Admin\QualityControl\CourseLogController@export');// 导出
Route::get('admin/course_log/import_template', 'Admin\QualityControl\CourseLogController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

//  收款方式配置
Route::get('admin/order_pay_method', 'Admin\QualityControl\OrderPayMethodController@index');// 列表
Route::get('admin/order_pay_method/add/{id}', 'Admin\QualityControl\OrderPayMethodController@add');// 添加
// Route::get('admin/order_pay_method/select', 'Admin\QualityControl\OrderPayMethodController@select');// 选择-弹窗
Route::get('admin/order_pay_method/export', 'Admin\QualityControl\OrderPayMethodController@export');// 导出
Route::get('admin/order_pay_method/import_template', 'Admin\QualityControl\OrderPayMethodController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 收款帐号配置
Route::get('admin/order_pay_config', 'Admin\QualityControl\OrderPayConfigController@index');// 列表
Route::get('admin/order_pay_config/add/{id}', 'Admin\QualityControl\OrderPayConfigController@add');// 添加
// Route::get('admin/order_pay_config/select', 'Admin\QualityControl\OrderPayConfigController@select');// 选择-弹窗
Route::get('admin/order_pay_config/export', 'Admin\QualityControl\OrderPayConfigController@export');// 导出
Route::get('admin/order_pay_config/import_template', 'Admin\QualityControl\OrderPayConfigController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 收款订单
Route::get('admin/orders', 'Admin\QualityControl\OrdersController@index');// 列表
//Route::get('admin/orders/add/{id}', 'Admin\QualityControl\OrdersController@add');// 添加
Route::get('admin/orders/info/{id}', 'Admin\QualityControl\OrdersController@info');// 查看-详情
// Route::get('admin/orders/select', 'Admin\QualityControl\OrdersController@select');// 选择-弹窗
Route::get('admin/orders/export', 'Admin\QualityControl\OrdersController@export');// 导出
// Route::get('admin/orders/import_template', 'Admin\QualityControl\OrdersController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面
Route::get('admin/orders/invoices', 'Admin\QualityControl\OrdersController@invoices');// 电子发票
Route::get('admin/orders/invoices_cancel', 'Admin\QualityControl\OrdersController@invoices_cancel');// 电子发票全额冲红

// 收款订单财务流水
Route::get('admin/order_flow', 'Admin\QualityControl\OrderFlowController@index');// 列表
//Route::get('admin/order_flow/add/{id}', 'Admin\QualityControl\OrderFlowController@add');// 添加
Route::get('admin/order_flow/info/{id}', 'Admin\QualityControl\OrderFlowController@info');// 查看-详情
// Route::get('admin/order_flow/select', 'Admin\QualityControl\OrderFlowController@select');// 选择-弹窗
Route::get('admin/order_flow/export', 'Admin\QualityControl\OrderFlowController@export');// 导出
// Route::get('admin/order_flow/import_template', 'Admin\QualityControl\OrderFlowController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 第三方对帐单
Route::get('admin/order_pay', 'Admin\QualityControl\OrderPayController@index');// 列表
Route::get('admin/order_pay/add/{id}', 'Admin\QualityControl\OrderPayController@add');// 添加
Route::get('admin/order_pay/info/{id}', 'Admin\QualityControl\OrderPayController@info');// 查看-详情
// Route::get('admin/order_pay/select', 'Admin\QualityControl\OrderPayController@select');// 选择-弹窗
Route::get('admin/order_pay/export', 'Admin\QualityControl\OrderPayController@export');// 导出
Route::get('admin/order_pay/import_template', 'Admin\QualityControl\OrderPayController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 发票配置沪友
Route::get('admin/invoice_config_hydzfp', 'Admin\QualityControl\InvoiceConfigHydzfpController@index');// 列表
Route::get('admin/invoice_config_hydzfp/add/{id}', 'Admin\QualityControl\InvoiceConfigHydzfpController@add');// 添加
// Route::get('admin/invoice_config_hydzfp/select', 'Admin\QualityControl\InvoiceConfigHydzfpController@select');// 选择-弹窗
Route::get('admin/invoice_config_hydzfp/export', 'Admin\QualityControl\InvoiceConfigHydzfpController@export');// 导出
Route::get('admin/invoice_config_hydzfp/import_template', 'Admin\QualityControl\InvoiceConfigHydzfpController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

Route::get('admin/invoice_config_hydzfp/test', 'Admin\QualityControl\InvoiceConfigHydzfpController@test');// 测试

// 发票配置销售方
Route::get('admin/invoice_seller', 'Admin\QualityControl\InvoiceSellerController@index');// 列表
Route::get('admin/invoice_seller/add/{id}', 'Admin\QualityControl\InvoiceSellerController@add');// 添加
// Route::get('admin/invoice_seller/select', 'Admin\QualityControl\InvoiceSellerController@select');// 选择-弹窗
Route::get('admin/invoice_seller/export', 'Admin\QualityControl\InvoiceSellerController@export');// 导出
Route::get('admin/invoice_seller/import_template', 'Admin\QualityControl\InvoiceSellerController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 发票配置购买方
Route::get('admin/invoice_buyer', 'Admin\QualityControl\InvoiceBuyerController@index');// 列表
Route::get('admin/invoice_buyer/add/{id}', 'Admin\QualityControl\InvoiceBuyerController@add');// 添加
Route::get('admin/invoice_buyer/info/{id}', 'Admin\QualityControl\InvoiceBuyerController@info');// 查看-详情
// Route::get('admin/invoice_buyer/select', 'Admin\QualityControl\InvoiceBuyerController@select');// 选择-弹窗
Route::get('admin/invoice_buyer/export', 'Admin\QualityControl\InvoiceBuyerController@export');// 导出
Route::get('admin/invoice_buyer/import_template', 'Admin\QualityControl\InvoiceBuyerController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 发票开票模板
Route::get('admin/invoice_template', 'Admin\QualityControl\InvoiceTemplateController@index');// 列表
Route::get('admin/invoice_template/add/{id}', 'Admin\QualityControl\InvoiceTemplateController@add');// 添加
Route::get('admin/invoice_template/info/{id}', 'Admin\QualityControl\InvoiceTemplateController@info');// 查看-详情
// Route::get('admin/invoice_template/select', 'Admin\QualityControl\InvoiceTemplateController@select');// 选择-弹窗
Route::get('admin/invoice_template/export', 'Admin\QualityControl\InvoiceTemplateController@export');// 导出
Route::get('admin/invoice_template/import_template', 'Admin\QualityControl\InvoiceTemplateController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 发票商品项目模板
Route::get('admin/invoice_project_template', 'Admin\QualityControl\InvoiceProjectTemplateController@index');// 列表
Route::get('admin/invoice_project_template/add/{id}', 'Admin\QualityControl\InvoiceProjectTemplateController@add');// 添加
// Route::get('admin/invoice_project_template/select', 'Admin\QualityControl\InvoiceProjectTemplateController@select');// 选择-弹窗
Route::get('admin/invoice_project_template/export', 'Admin\QualityControl\InvoiceProjectTemplateController@export');// 导出
Route::get('admin/invoice_project_template/import_template', 'Admin\QualityControl\InvoiceProjectTemplateController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 发票主表
Route::get('admin/invoices', 'Admin\QualityControl\InvoicesController@index');// 列表
Route::get('admin/invoices/add/{id}', 'Admin\QualityControl\InvoicesController@add');// 添加
Route::get('admin/invoices/info/{id}', 'Admin\QualityControl\InvoicesController@info');// 查看-详情
// Route::get('admin/invoices/select', 'Admin\QualityControl\InvoicesController@select');// 选择-弹窗
Route::get('admin/invoices/export', 'Admin\QualityControl\InvoicesController@export');// 导出
Route::get('admin/invoices/import_template', 'Admin\QualityControl\InvoicesController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 企业到期配置
Route::get('admin/company_expire', 'Admin\QualityControl\CompanyExpireController@index');// 列表
Route::get('admin/company_expire/add/{id}', 'Admin\QualityControl\CompanyExpireController@add');// 添加
// Route::get('admin/company_expire/select', 'Admin\QualityControl\CompanyExpireController@select');// 选择-弹窗
Route::get('admin/company_expire/export', 'Admin\QualityControl\CompanyExpireController@export');// 导出
Route::get('admin/company_expire/import_template', 'Admin\QualityControl\CompanyExpireController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 企业会员等级配置
Route::get('admin/company_grade_config', 'Admin\QualityControl\CompanyGradeConfigController@index');// 列表
Route::get('admin/company_grade_config/add/{id}', 'Admin\QualityControl\CompanyGradeConfigController@add');// 添加
// Route::get('admin/company_grade_config/select', 'Admin\QualityControl\CompanyGradeConfigController@select');// 选择-弹窗
Route::get('admin/company_grade_config/export', 'Admin\QualityControl\CompanyGradeConfigController@export');// 导出
Route::get('admin/company_grade_config/import_template', 'Admin\QualityControl\CompanyGradeConfigController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 城市[一级分类]
Route::get('admin/citys', 'Admin\QualityControl\CitysController@index');// 列表
Route::get('admin/citys/add/{id}', 'Admin\QualityControl\CitysController@add');// 添加
// Route::get('admin/citys/select', 'Admin\QualityControl\CitysController@select');// 选择-弹窗
Route::get('admin/citys/export', 'Admin\QualityControl\CitysController@export');// 导出
Route::get('admin/citys/import_template', 'Admin\QualityControl\CitysController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 证书设置[一级分类]
Route::get('admin/ability_code', 'Admin\QualityControl\AbilityCodeController@index');// 列表
Route::get('admin/ability_code/add/{id}', 'Admin\QualityControl\AbilityCodeController@add');// 添加
// Route::get('admin/ability_code/select', 'Admin\QualityControl\AbilityCodeController@select');// 选择-弹窗
//Route::get('admin/ability_code/export', 'Admin\QualityControl\AbilityCodeController@export');// 导出
//Route::get('admin/ability_code/import_template', 'Admin\QualityControl\AbilityCodeController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 登录验证码 验证码
Route::get('admin/sms_code', 'Admin\QualityControl\SmsCodeController@index');// 列表
Route::get('admin/sms_code/add/{id}', 'Admin\QualityControl\SmsCodeController@add');// 添加
// Route::get('admin/sms_code/select', 'Admin\QualityControl\SmsCodeController@select');// 选择-弹窗
Route::get('admin/sms_code/export', 'Admin\QualityControl\SmsCodeController@export');// 导出
Route::get('admin/sms_code/import_template', 'Admin\QualityControl\SmsCodeController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 证书
Route::get('admin/certificate', 'Admin\QualityControl\CertificateController@index');// 列表
Route::get('admin/certificate/add/{id}', 'Admin\QualityControl\CertificateController@add');// 添加
// Route::get('admin/certificate/select', 'Admin\QualityControl\CertificateController@select');// 选择-弹窗
Route::get('admin/certificate/export', 'Admin\QualityControl\CertificateController@export');// 导出
Route::get('admin/certificate/import_template', 'Admin\QualityControl\CertificateController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 实验室地址
Route::get('admin/laboratory_addr', 'Admin\QualityControl\LaboratoryAddrController@index');// 列表
Route::get('admin/laboratory_addr/add/{id}', 'Admin\QualityControl\LaboratoryAddrController@add');// 添加
// Route::get('admin/laboratory_addr/select', 'Admin\QualityControl\LaboratoryAddrController@select');// 选择-弹窗
Route::get('admin/laboratory_addr/export', 'Admin\QualityControl\LaboratoryAddrController@export');// 导出
Route::get('admin/laboratory_addr/import_template', 'Admin\QualityControl\LaboratoryAddrController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 证书-能力范围
Route::get('admin/certificate_schedule', 'Admin\QualityControl\CertificateScheduleController@index');// 列表
Route::get('admin/certificate_schedule/add/{id}', 'Admin\QualityControl\CertificateScheduleController@add');// 添加
// Route::get('admin/certificate_schedule/select', 'Admin\QualityControl\CertificateScheduleController@select');// 选择-弹窗
Route::get('admin/certificate_schedule/export', 'Admin\QualityControl\CertificateScheduleController@export');// 导出
Route::get('admin/certificate_schedule/import_template', 'Admin\QualityControl\CertificateScheduleController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

Route::get('admin/certificate_schedule/add_excel/{id}', 'Admin\QualityControl\CertificateScheduleController@add_excel');// 添加--导入

// 证书-证书导入批次
Route::get('admin/certificate_import_log', 'Admin\QualityControl\CertificateImportLogController@index');// 列表
Route::get('admin/certificate_import_log/add/{id}', 'Admin\QualityControl\CertificateImportLogController@add');// 添加
Route::get('admin/certificate_import_log/info/{id}', 'Admin\QualityControl\CertificateImportLogController@info');// 查看-详情
// Route::get('admin/certificate_import_log/select', 'Admin\QualityControl\CertificateImportLogController@select');// 选择-弹窗
Route::get('admin/certificate_import_log/export', 'Admin\QualityControl\CertificateImportLogController@export');// 导出
Route::get('admin/certificate_import_log/import_template', 'Admin\QualityControl\CertificateImportLogController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 资质证书类型[一级分类]
Route::get('admin/company_certificate_type', 'Admin\QualityControl\CompanyCertificateTypeController@index');// 列表
Route::get('admin/company_certificate_type/add/{id}', 'Admin\QualityControl\CompanyCertificateTypeController@add');// 添加
Route::get('admin/company_certificate_type/info/{id}', 'Admin\QualityControl\CompanyCertificateTypeController@info');// 查看-详情
// Route::get('admin/company_certificate_type/select', 'Admin\QualityControl\CompanyCertificateTypeController@select');// 选择-弹窗
Route::get('admin/company_certificate_type/export', 'Admin\QualityControl\CompanyCertificateTypeController@export');// 导出
Route::get('admin/company_certificate_type/import_template', 'Admin\QualityControl\CompanyCertificateTypeController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 能力验证行业分类[一级分类]
Route::get('admin/ability_type', 'Admin\QualityControl\AbilityTypeController@index');// 列表
Route::get('admin/ability_type/add/{id}', 'Admin\QualityControl\AbilityTypeController@add');// 添加
// Route::get('admin/ability_type/select', 'Admin\QualityControl\AbilityTypeController@select');// 选择-弹窗
Route::get('admin/ability_type/export', 'Admin\QualityControl\AbilityTypeController@export');// 导出
Route::get('admin/ability_type/import_template', 'Admin\QualityControl\AbilityTypeController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 企业内容管理
Route::get('admin/company_content', 'Admin\QualityControl\CompanyContentController@index');// 列表
Route::get('admin/company_content/add/{id}', 'Admin\QualityControl\CompanyContentController@add');// 添加
// Route::get('admin/company_content/select', 'Admin\QualityControl\CompanyContentController@select');// 选择-弹窗
Route::get('admin/company_content/export', 'Admin\QualityControl\CompanyContentController@export');// 导出
Route::get('admin/company_content/import_template', 'Admin\QualityControl\CompanyContentController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 能力验证
Route::get('admin/abilitys', 'Admin\QualityControl\AbilitysController@index');// 列表
Route::get('admin/abilitys/add/{id}', 'Admin\QualityControl\AbilitysController@add');// 添加
Route::get('admin/abilitys/info/{id}', 'Admin\QualityControl\AbilitysController@info');// 查看-详情
Route::get('admin/abilitys/select', 'Admin\QualityControl\AbilitysController@select');// 选择-弹窗
Route::get('admin/abilitys/export', 'Admin\QualityControl\AbilitysController@export');// 导出
Route::get('admin/abilitys/export_join/{ability_id}', 'Admin\QualityControl\AbilitysController@export_join');// 导出报名的企业信息
Route::get('admin/abilitys/import_template', 'Admin\QualityControl\AbilitysController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面
Route::get('admin/abilitys/publish/{id}', 'Admin\QualityControl\AbilitysController@publish');// 公布结果页面

Route::get('admin/abilitys/add_excel/{id}', 'Admin\QualityControl\AbilitysController@add_excel');// 添加--导入

//****************************************************************************
// 能力验证管理
Route::get('admin/abilitys_admin/{ability_id}', 'Admin\QualityControl\Abilitys\IndexController@index');// 首页
Route::get('admin/abilitys_admin/{ability_id}/basic', 'Admin\QualityControl\Abilitys\IndexController@basic');// 基础信息

// 能力验证--报名管理--参加单位
Route::get('admin/abilitys_admin/{ability_id}/ability_join_items', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@index');// 列表
//Route::get('admin/abilitys_admin/{ability_id}/ability_join_items/add/{id}', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@add');// 添加
// Route::get('admin/abilitys_admin/{ability_id}/ability_join_items/info/{id}', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@info');// 查看-详情
// Route::get('admin/abilitys_admin/{ability_id}/ability_join_items/select', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@select');// 选择-弹窗
//Route::get('admin/abilitys_admin/{ability_id}/ability_join_items/export', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@export');// 导出
//Route::get('admin/abilitys_admin/{ability_id}/ability_join_items/import_template', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

Route::get('admin/abilitys_admin/{ability_id}/ability_join_items/sample_result_info/{item_id}/{retry_no}', 'Admin\QualityControl\Abilitys\AbilityJoinItemsController@sample_result_info');// 查看上传的数据

// 能力验证结果--报名管理--参加单位
Route::get('admin/abilitys_admin/{ability_id}/ability_join_items_results', 'Admin\QualityControl\Abilitys\AbilityJoinItemsResultsController@index');// 列表
Route::get('admin/abilitys_admin/{ability_id}/ability_join_items_results/add/{id}', 'Admin\QualityControl\Abilitys\AbilityJoinItemsResultsController@add');// 添加--判定
Route::get('admin/abilitys_admin/{ability_id}/ability_join_items_results/export', 'Admin\QualityControl\Abilitys\AbilityJoinItemsResultsController@export');// 导出
Route::get('admin/abilitys_admin/{ability_id}/ability_join_items_results/sms_send', 'Admin\QualityControl\Abilitys\AbilityJoinItemsResultsController@sms_send');// 选择短信模板页面
Route::get('admin/abilitys_admin/{ability_id}/ability_join_items_results/get_sample/{id}', 'Admin\QualityControl\Abilitys\AbilityJoinItemsResultsController@get_sample');// 取样

//****************************************************************************
// 课程管理
Route::get('admin/course_admin/{course_id}', 'Admin\QualityControl\Course\IndexController@index');// 首页
Route::get('admin/course_admin/{course_id}/basic', 'Admin\QualityControl\Course\IndexController@basic');// 基础信息
//****************************************************************************
// 课程班级管理
Route::get('admin/course_class_admin/{class_id}', 'Admin\QualityControl\CourseClass\IndexController@index');// 首页
Route::get('admin/course_class_admin/{class_id}/basic', 'Admin\QualityControl\CourseClass\IndexController@basic');// 基础信息
//****************************************************************************
// 能力验证--报名管理
Route::get('admin/ability_join', 'Admin\QualityControl\AbilityJoinController@index');// 列表
//Route::get('admin/ability_join/add/{id}', 'Admin\QualityControl\AbilityJoinController@add');// 添加
Route::get('admin/ability_join/info/{id}', 'Admin\QualityControl\AbilityJoinController@info');// 查看-详情
// Route::get('admin/ability_join/select', 'Admin\QualityControl\AbilityJoinController@select');// 选择-弹窗
//Route::get('admin/ability_join/export', 'Admin\QualityControl\AbilityJoinController@export');// 导出
//Route::get('admin/ability_join/import_template', 'Admin\QualityControl\AbilityJoinController@import_template');// 导入模版
Route::get('admin/ability_join/sms_send', 'Admin\QualityControl\AbilityJoinController@sms_send');// 选择短信模板页面

Route::get('admin/ability_join/get_sample/{id}', 'Admin\QualityControl\AbilityJoinController@get_sample');// 取样
Route::get('admin/ability_join/print/{id}', 'Admin\QualityControl\AbilityJoinController@print');// 打印证书

// 企业能力附表
Route::get('admin/company_schedule', 'Admin\QualityControl\CompanyScheduleController@index');// 列表
Route::get('admin/company_schedule/show/{company_id}', 'Admin\QualityControl\CompanyScheduleController@show');// 查看
Route::get('admin/company_schedule/add/{id}', 'Admin\QualityControl\CompanyScheduleController@add');// 添加
// Route::get('admin/company_schedule/select', 'Admin\QualityControl\CompanyScheduleController@select');// 选择-弹窗
Route::get('admin/company_schedule/export', 'Admin\QualityControl\CompanyScheduleController@export');// 导出
Route::get('admin/company_schedule/import_template', 'Admin\QualityControl\CompanyScheduleController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 企业能力附表-最新
Route::get('admin/company_new_schedule/test/{id}', 'Admin\QualityControl\CompanyNewScheduleController@test');// 测试上传
Route::get('admin/company_new_schedule', 'Admin\QualityControl\CompanyNewScheduleController@index');// 列表
Route::get('admin/company_new_schedule/show/{company_id}', 'Admin\QualityControl\CompanyNewScheduleController@show');// 查看
Route::get('admin/company_new_schedule/add/{id}', 'Admin\QualityControl\CompanyNewScheduleController@add');// 添加
Route::get('admin/company_new_schedule/add_excel/{id}', 'Admin\QualityControl\CompanyNewScheduleController@add_excel');// 添加
// Route::get('admin/company_new_schedule/select', 'Admin\QualityControl\CompanyNewScheduleController@select');// 选择-弹窗
Route::get('admin/company_new_schedule/export', 'Admin\QualityControl\CompanyNewScheduleController@export');// 导出
Route::get('admin/company_new_schedule/import_template', 'Admin\QualityControl\CompanyNewScheduleController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面
Route::get('admin/company_new_schedule/list', 'Admin\QualityControl\CompanyNewScheduleController@list');// 列表--按企业id降序

Route::get('admin/company_new_schedule/down_moban', 'Admin\QualityControl\CompanyNewScheduleController@down_moban');// 下载文件模板


// 应用管理
Route::get('admin/apply', 'Admin\QualityControl\ApplyController@index');// 列表
Route::get('admin/apply/add/{id}', 'Admin\QualityControl\ApplyController@add');// 添加
// Route::get('admin/apply/select', 'Admin\QualityControl\ApplyController@select');// 选择-弹窗
Route::get('admin/apply/export', 'Admin\QualityControl\ApplyController@export');// 导出
Route::get('admin/apply/import_template', 'Admin\QualityControl\ApplyController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 接口日志
Route::get('admin/api_log', 'Admin\QualityControl\ApiLogController@index');// 列表
Route::get('admin/api_log/add/{id}', 'Admin\QualityControl\ApiLogController@add');// 添加
// Route::get('admin/api_log/select', 'Admin\QualityControl\ApiLogController@select');// 选择-弹窗
Route::get('admin/api_log/export', 'Admin\QualityControl\ApiLogController@export');// 导出
Route::get('admin/api_log/import_template', 'Admin\QualityControl\ApiLogController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 短信相关的

// 短信模板所属模块
Route::get('admin/sms_module', 'Admin\QualityControl\SmsModuleController@index');// 列表
Route::get('admin/sms_module/add/{id}', 'Admin\QualityControl\SmsModuleController@add');// 添加
Route::get('admin/sms_module/info/{id}', 'Admin\QualityControl\SmsModuleController@info');// 查看-详情
// Route::get('admin/sms_module/select', 'Admin\QualityControl\SmsModuleController@select');// 选择-弹窗
Route::get('admin/sms_module/export', 'Admin\QualityControl\SmsModuleController@export');// 导出
Route::get('admin/sms_module/import_template', 'Admin\QualityControl\SmsModuleController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 快捷常用参数
Route::get('admin/sms_module_params_common', 'Admin\QualityControl\SmsModuleParamsCommonController@index');// 列表
Route::get('admin/sms_module_params_common/add/{id}', 'Admin\QualityControl\SmsModuleParamsCommonController@add');// 添加
Route::get('admin/sms_module_params_common/info/{id}', 'Admin\QualityControl\SmsModuleParamsCommonController@info');// 查看-详情
// Route::get('admin/sms_module_params_common/select', 'Admin\QualityControl\SmsModuleParamsCommonController@select');// 选择-弹窗
Route::get('admin/sms_module_params_common/export', 'Admin\QualityControl\SmsModuleParamsCommonController@export');// 导出
Route::get('admin/sms_module_params_common/import_template', 'Admin\QualityControl\SmsModuleParamsCommonController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 短信模板所属模块参数
Route::get('admin/sms_module_params', 'Admin\QualityControl\SmsModuleParamsController@index');// 列表
Route::get('admin/sms_module_params/add/{id}', 'Admin\QualityControl\SmsModuleParamsController@add');// 添加
Route::get('admin/sms_module_params/info/{id}', 'Admin\QualityControl\SmsModuleParamsController@info');// 查看-详情
// Route::get('admin/sms_module_params/select', 'Admin\QualityControl\SmsModuleParamsController@select');// 选择-弹窗
Route::get('admin/sms_module_params/export', 'Admin\QualityControl\SmsModuleParamsController@export');// 导出
Route::get('admin/sms_module_params/import_template', 'Admin\QualityControl\SmsModuleParamsController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 限次配置
Route::get('admin/sms_limit', 'Admin\QualityControl\SmsLimitController@index');// 列表
Route::get('admin/sms_limit/add/{id}', 'Admin\QualityControl\SmsLimitController@add');// 添加
Route::get('admin/sms_limit/info/{id}', 'Admin\QualityControl\SmsLimitController@info');// 查看-详情
// Route::get('admin/sms_limit/select', 'Admin\QualityControl\SmsLimitController@select');// 选择-弹窗
Route::get('admin/sms_limit/export', 'Admin\QualityControl\SmsLimitController@export');// 导出
Route::get('admin/sms_limit/import_template', 'Admin\QualityControl\SmsLimitController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 短信模板
Route::get('admin/sms_template', 'Admin\QualityControl\SmsTemplateController@index');// 列表
Route::get('admin/sms_template/add/{id}', 'Admin\QualityControl\SmsTemplateController@add');// 添加
Route::get('admin/sms_template/info/{id}', 'Admin\QualityControl\SmsTemplateController@info');// 查看-详情
// Route::get('admin/sms_template/select', 'Admin\QualityControl\SmsTemplateController@select');// 选择-弹窗
Route::get('admin/sms_template/export', 'Admin\QualityControl\SmsTemplateController@export');// 导出
Route::get('admin/sms_template/import_template', 'Admin\QualityControl\SmsTemplateController@import_template');// 导入模版
Route::get('admin/sms_template/sms_send', 'Admin\QualityControl\SmsTemplateController@sms_send');// 选择短信模板页面 -- 测试短信页面

// 短信日志
Route::get('admin/sms_log', 'Admin\QualityControl\SmsLogController@index');// 列表
Route::get('admin/sms_log/add/{id}', 'Admin\QualityControl\SmsLogController@add');// 添加
Route::get('admin/sms_log/info/{id}', 'Admin\QualityControl\SmsLogController@info');// 查看-详情
// Route::get('admin/sms_log/select', 'Admin\QualityControl\SmsLogController@select');// 选择-弹窗
Route::get('admin/sms_log/export', 'Admin\QualityControl\SmsLogController@export');// 导出
Route::get('admin/sms_log/import_template', 'Admin\QualityControl\SmsLogController@import_template');// 导入模版
Route::get('admin/sms_log/sms_send', 'Admin\QualityControl\SmsLogController@sms_send');// 选择短信模板页面 -- 测试短信页面

// 点播相关

// 点播课程分类
Route::get('admin/vod_type', 'Admin\QualityControl\VodTypeController@index');// 列表
Route::get('admin/vod_type/add/{id}', 'Admin\QualityControl\VodTypeController@add');// 添加
Route::get('admin/vod_type/info/{id}', 'Admin\QualityControl\VodTypeController@info');// 查看-详情
// Route::get('admin/vod_type/select', 'Admin\QualityControl\VodTypeController@select');// 选择-弹窗
Route::get('admin/vod_type/export', 'Admin\QualityControl\VodTypeController@export');// 导出
Route::get('admin/vod_type/import_template', 'Admin\QualityControl\VodTypeController@import_template');// 导入模版
// Route::get('admin/vod_type/sms_send', 'Admin\QualityControl\VodTypeController@sms_send');// 选择短信模板页面

// 点播课程
Route::get('admin/vods', 'Admin\QualityControl\VodsController@index');// 列表
Route::get('admin/vods/add/{id}', 'Admin\QualityControl\VodsController@add');// 添加
Route::get('admin/vods/info/{id}', 'Admin\QualityControl\VodsController@info');// 查看-详情
// Route::get('admin/vods/select', 'Admin\QualityControl\VodsController@select');// 选择-弹窗
Route::get('admin/vods/export', 'Admin\QualityControl\VodsController@export');// 导出
Route::get('admin/vods/import_template', 'Admin\QualityControl\VodsController@import_template');// 导入模版
// Route::get('admin/vods/sms_send', 'Admin\QualityControl\VodsController@sms_send');// 选择短信模板页面

// 点播课程内容
Route::get('admin/vods_content', 'Admin\QualityControl\VodsContentController@index');// 列表
Route::get('admin/vods_content/add/{id}', 'Admin\QualityControl\VodsContentController@add');// 添加
Route::get('admin/vods_content/info/{id}', 'Admin\QualityControl\VodsContentController@info');// 查看-详情
// Route::get('admin/vods_content/select', 'Admin\QualityControl\VodsContentController@select');// 选择-弹窗
Route::get('admin/vods_content/export', 'Admin\QualityControl\VodsContentController@export');// 导出
Route::get('admin/vods_content/import_template', 'Admin\QualityControl\VodsContentController@import_template');// 导入模版
// Route::get('admin/vods_content/sms_send', 'Admin\QualityControl\VodsContentController@sms_send');// 选择短信模板页面

// 点播课程视频目录
Route::get('admin/vod_video', 'Admin\QualityControl\VodVideoController@index');// 列表
Route::get('admin/vod_video/add/{id}', 'Admin\QualityControl\VodVideoController@add');// 添加
Route::get('admin/vod_video/info/{id}', 'Admin\QualityControl\VodVideoController@info');// 查看-详情
// Route::get('admin/vod_video/select', 'Admin\QualityControl\VodVideoController@select');// 选择-弹窗
Route::get('admin/vod_video/export', 'Admin\QualityControl\VodVideoController@export');// 导出
Route::get('admin/vod_video/import_template', 'Admin\QualityControl\VodVideoController@import_template');// 导入模版
// Route::get('admin/vod_video/sms_send', 'Admin\QualityControl\VodVideoController@sms_send');// 选择短信模板页面
Route::get('admin/vod_video/addDir/{id}', 'Admin\QualityControl\VodVideoController@addDir');// 添加--目录

Route::get('admin/vod_video/test', 'Admin\QualityControl\VodVideoController@test');// 测试

// 点播课程订单
Route::get('admin/vod_orders', 'Admin\QualityControl\VodOrdersController@index');// 列表
Route::get('admin/vod_orders/add/{id}', 'Admin\QualityControl\VodOrdersController@add');// 添加
Route::get('admin/vod_orders/info/{id}', 'Admin\QualityControl\VodOrdersController@info');// 查看-详情
// Route::get('admin/vod_orders/select', 'Admin\QualityControl\VodOrdersController@select');// 选择-弹窗
Route::get('admin/vod_orders/export', 'Admin\QualityControl\VodOrdersController@export');// 导出
Route::get('admin/vod_orders/import_template', 'Admin\QualityControl\VodOrdersController@import_template');// 导入模版
// Route::get('admin/vod_orders/sms_send', 'Admin\QualityControl\VodOrdersController@sms_send');// 选择短信模板页面
Route::get('admin/vod_orders/pay', 'Admin\QualityControl\VodOrdersController@pay');// 缴费
Route::get('admin/vod_orders/pay_save', 'Admin\QualityControl\VodOrdersController@pay_save');// 缴费页面


// 点播课程销量统计【流水】
Route::get('admin/vod_sales', 'Admin\QualityControl\VodSalesController@index');// 列表
Route::get('admin/vod_sales/add/{id}', 'Admin\QualityControl\VodSalesController@add');// 添加
Route::get('admin/vod_sales/info/{id}', 'Admin\QualityControl\VodSalesController@info');// 查看-详情
// Route::get('admin/vod_sales/select', 'Admin\QualityControl\VodSalesController@select');// 选择-弹窗
Route::get('admin/vod_sales/export', 'Admin\QualityControl\VodSalesController@export');// 导出
Route::get('admin/vod_sales/import_template', 'Admin\QualityControl\VodSalesController@import_template');// 导入模版
// Route::get('admin/vod_sales/sms_send', 'Admin\QualityControl\VodSalesController@sms_send');// 选择短信模板页面

// 点播课程评论
Route::get('admin/vod_comment', 'Admin\QualityControl\VodCommentController@index');// 列表
Route::get('admin/vod_comment/add/{id}', 'Admin\QualityControl\VodCommentController@add');// 添加
Route::get('admin/vod_comment/info/{id}', 'Admin\QualityControl\VodCommentController@info');// 查看-详情
// Route::get('admin/vod_comment/select', 'Admin\QualityControl\VodCommentController@select');// 选择-弹窗
Route::get('admin/vod_comment/export', 'Admin\QualityControl\VodCommentController@export');// 导出
Route::get('admin/vod_comment/import_template', 'Admin\QualityControl\VodCommentController@import_template');// 导入模版
// Route::get('admin/vod_comment/sms_send', 'Admin\QualityControl\VodCommentController@sms_send');// 选择短信模板页面

// 点播课程学员学习进度
Route::get('admin/vod_rate', 'Admin\QualityControl\VodRateController@index');// 列表
Route::get('admin/vod_rate/add/{id}', 'Admin\QualityControl\VodRateController@add');// 添加
Route::get('admin/vod_rate/info/{id}', 'Admin\QualityControl\VodRateController@info');// 查看-详情
// Route::get('admin/vod_rate/select', 'Admin\QualityControl\VodRateController@select');// 选择-弹窗
Route::get('admin/vod_rate/export', 'Admin\QualityControl\VodRateController@export');// 导出
Route::get('admin/vod_rate/import_template', 'Admin\QualityControl\VodRateController@import_template');// 导入模版
// Route::get('admin/vod_rate/sms_send', 'Admin\QualityControl\VodRateController@sms_send');// 选择短信模板页面

// 直播

// 直播公告
Route::get('admin/live_notice', 'Admin\QualityControl\LiveNoticeController@index');// 列表
Route::get('admin/live_notice/add/{id}', 'Admin\QualityControl\LiveNoticeController@add');// 添加
Route::get('admin/live_notice/info/{id}', 'Admin\QualityControl\LiveNoticeController@info');// 查看-详情
// Route::get('admin/live_notice/select', 'Admin\QualityControl\LiveNoticeController@select');// 选择-弹窗
Route::get('admin/live_notice/export', 'Admin\QualityControl\LiveNoticeController@export');// 导出
Route::get('admin/live_notice/import_template', 'Admin\QualityControl\LiveNoticeController@import_template');// 导入模版
// Route::get('admin/live_notice/sms_send', 'Admin\QualityControl\LiveNoticeController@sms_send');// 选择短信模板页面


// 省局企业相关的

// 监督检查信息管理
Route::get('admin/company_supervise', 'Admin\QualityControl\CompanySuperviseController@index');// 列表
Route::get('admin/company_supervise/add/{id}', 'Admin\QualityControl\CompanySuperviseController@add');// 添加
// Route::get('admin/company_supervise/select', 'Admin\QualityControl\CompanySuperviseController@select');// 选择-弹窗
Route::get('admin/company_supervise/export', 'Admin\QualityControl\CompanySuperviseController@export');// 导出
Route::get('admin/company_supervise/import_template', 'Admin\QualityControl\CompanySuperviseController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 机构自我声明
Route::get('admin/company_statement', 'Admin\QualityControl\CompanyStatementController@index');// 列表
Route::get('admin/company_statement/add/{id}', 'Admin\QualityControl\CompanyStatementController@add');// 添加
// Route::get('admin/company_statement/select', 'Admin\QualityControl\CompanyStatementController@select');// 选择-弹窗
Route::get('admin/company_statement/export', 'Admin\QualityControl\CompanyStatementController@export');// 导出
Route::get('admin/company_statement/import_template', 'Admin\QualityControl\CompanyStatementController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 能力验证
Route::get('admin/company_ability', 'Admin\QualityControl\CompanyAbilityController@index');// 列表
Route::get('admin/company_ability/add/{id}', 'Admin\QualityControl\CompanyAbilityController@add');// 添加
Route::get('admin/company_ability/info/{id}', 'Admin\QualityControl\CompanyAbilityController@info');// 查看-详情
// Route::get('admin/company_ability/select', 'Admin\QualityControl\CompanyAbilityController@select');// 选择-弹窗
Route::get('admin/company_ability/export', 'Admin\QualityControl\CompanyAbilityController@export');// 导出
Route::get('admin/company_ability/import_template', 'Admin\QualityControl\CompanyAbilityController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

Route::get('admin/company_ability/add_excel/{id}', 'Admin\QualityControl\CompanyAbilityController@add_excel');// 添加--导入

// 监督检查
Route::get('admin/company_inspect', 'Admin\QualityControl\CompanyInspectController@index');// 列表
Route::get('admin/company_inspect/add/{id}', 'Admin\QualityControl\CompanyInspectController@add');// 添加
Route::get('admin/company_inspect/info/{id}', 'Admin\QualityControl\CompanyInspectController@info');// 查看-详情
// Route::get('admin/company_inspect/select', 'Admin\QualityControl\CompanyInspectController@select');// 选择-弹窗
Route::get('admin/company_inspect/export', 'Admin\QualityControl\CompanyInspectController@export');// 导出
Route::get('admin/company_inspect/import_template', 'Admin\QualityControl\CompanyInspectController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 企业其它【新闻】
Route::get('admin/company_news', 'Admin\QualityControl\CompanyNewsController@index');// 列表
Route::get('admin/company_news/add/{id}', 'Admin\QualityControl\CompanyNewsController@add');// 添加
Route::get('admin/company_news/info/{id}', 'Admin\QualityControl\CompanyNewsController@info');// 查看-详情
// Route::get('admin/company_news/select', 'Admin\QualityControl\CompanyNewsController@select');// 选择-弹窗
Route::get('admin/company_news/export', 'Admin\QualityControl\CompanyNewsController@export');// 导出
Route::get('admin/company_news/import_template', 'Admin\QualityControl\CompanyNewsController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 机构处罚
Route::get('admin/company_punish', 'Admin\QualityControl\CompanyPunishController@index');// 列表
Route::get('admin/company_punish/add/{id}', 'Admin\QualityControl\CompanyPunishController@add');// 添加
// Route::get('admin/company_punish/select', 'Admin\QualityControl\CompanyPunishController@select');// 选择-弹窗
Route::get('admin/company_punish/export', 'Admin\QualityControl\CompanyPunishController@export');// 导出
Route::get('admin/company_punish/import_template', 'Admin\QualityControl\CompanyPunishController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 通知公告
Route::get('admin/platform_notices', 'Admin\QualityControl\PlatformNoticesController@index');// 列表
Route::get('admin/platform_notices/add/{id}', 'Admin\QualityControl\PlatformNoticesController@add');// 添加
// Route::get('admin/platform_notices/select', 'Admin\QualityControl\PlatformNoticesController@select');// 选择-弹窗
Route::get('admin/platform_notices/export', 'Admin\QualityControl\PlatformNoticesController@export');// 导出
Route::get('admin/platform_notices/import_template', 'Admin\QualityControl\PlatformNoticesController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 表格下载
Route::get('admin/platform_down_files', 'Admin\QualityControl\PlatformDownFilesController@index');// 列表
Route::get('admin/platform_down_files/add/{id}', 'Admin\QualityControl\PlatformDownFilesController@add');// 添加
// Route::get('admin/platform_down_files/select', 'Admin\QualityControl\PlatformDownFilesController@select');// 选择-弹窗
Route::get('admin/platform_down_files/export', 'Admin\QualityControl\PlatformDownFilesController@export');// 导出
Route::get('admin/platform_down_files/import_template', 'Admin\QualityControl\PlatformDownFilesController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

// 在线考试

// 试题分类[一级分类]
Route::get('admin/company_subject_type', 'Admin\QualityControl\CompanySubjectTypeController@index');// 列表
Route::get('admin/company_subject_type/add/{id}', 'Admin\QualityControl\CompanySubjectTypeController@add');// 添加
// Route::get('admin/company_subject_type/select', 'Admin\QualityControl\CompanySubjectTypeController@select');// 选择-弹窗
Route::get('admin/company_subject_type/export', 'Admin\QualityControl\CompanySubjectTypeController@export');// 导出
Route::get('admin/company_subject_type/import_template', 'Admin\QualityControl\CompanySubjectTypeController@import_template');// 导入模版
// Route::get('admin/company_subject_type/sms_send', 'Admin\QualityControl\CompanySubjectTypeController@sms_send');// 选择短信模板页面

// 试题
Route::get('admin/company_subject', 'Admin\QualityControl\CompanySubjectController@index');// 列表
Route::get('admin/company_subject/add/{id}', 'Admin\QualityControl\CompanySubjectController@add');// 添加
// Route::get('admin/company_subject/select', 'Admin\QualityControl\CompanySubjectController@select');// 选择-弹窗
Route::get('admin/company_subject/export', 'Admin\QualityControl\CompanySubjectController@export');// 导出
Route::get('admin/company_subject/import_template', 'Admin\QualityControl\CompanySubjectController@import_template');// 导入模版
// Route::get('admin/company_subject/sms_send', 'Admin\QualityControl\CompanySubjectController@sms_send');// 选择短信模板页面

// 试卷
Route::get('admin/company_paper', 'Admin\QualityControl\CompanyPaperController@index');// 列表
Route::get('admin/company_paper/add/{id}', 'Admin\QualityControl\CompanyPaperController@add');// 添加
// Route::get('admin/company_paper/select', 'Admin\QualityControl\CompanyPaperController@select');// 选择-弹窗
Route::get('admin/company_paper/export', 'Admin\QualityControl\CompanyPaperController@export');// 导出
Route::get('admin/company_paper/import_template', 'Admin\QualityControl\CompanyPaperController@import_template');// 导入模版
// Route::get('admin/company_paper/sms_send', 'Admin\QualityControl\CompanyPaperController@sms_send');// 选择短信模板页面

// 分数等级
Route::get('admin/company_core_grade', 'Admin\QualityControl\CompanyCoreGradeController@index');// 列表
Route::get('admin/company_core_grade/add/{id}', 'Admin\QualityControl\CompanyCoreGradeController@add');// 添加
// Route::get('admin/company_core_grade/select', 'Admin\QualityControl\CompanyCoreGradeController@select');// 选择-弹窗
Route::get('admin/company_core_grade/export', 'Admin\QualityControl\CompanyCoreGradeController@export');// 导出
Route::get('admin/company_core_grade/import_template', 'Admin\QualityControl\CompanyCoreGradeController@import_template');// 导入模版
// Route::get('admin/company_core_grade/sms_send', 'Admin\QualityControl\CompanyCoreGradeController@sms_send');// 选择短信模板页面

// 考次
Route::get('admin/company_exam', 'Admin\QualityControl\CompanyExamController@index');// 列表
Route::get('admin/company_exam/add/{id}', 'Admin\QualityControl\CompanyExamController@add');// 添加
// Route::get('admin/company_exam/select', 'Admin\QualityControl\CompanyExamController@select');// 选择-弹窗
Route::get('admin/company_exam/export', 'Admin\QualityControl\CompanyExamController@export');// 导出
Route::get('admin/company_exam/import_template', 'Admin\QualityControl\CompanyExamController@import_template');// 导入模版
// Route::get('admin/company_exam/sms_send', 'Admin\QualityControl\CompanyExamController@sms_send');// 选择短信模板页面

// 考次的人员
Route::get('admin/company_exam_staff', 'Admin\QualityControl\CompanyExamStaffController@index');// 列表
Route::get('admin/company_exam_staff/add/{id}', 'Admin\QualityControl\CompanyExamStaffController@add');// 添加
// Route::get('admin/company_exam_staff/select', 'Admin\QualityControl\CompanyExamStaffController@select');// 选择-弹窗
Route::get('admin/company_exam_staff/export', 'Admin\QualityControl\CompanyExamStaffController@export');// 导出
Route::get('admin/company_exam_staff/import_template', 'Admin\QualityControl\CompanyExamStaffController@import_template');// 导入模版
// Route::get('admin/company_exam_staff/sms_send', 'Admin\QualityControl\CompanyExamStaffController@sms_send');// 选择短信模板页面

// 考次的人员试题答案
Route::get('admin/company_exam_staff_subject', 'Admin\QualityControl\CompanyExamStaffSubjectController@index');// 列表
Route::get('admin/company_exam_staff_subject/add/{id}', 'Admin\QualityControl\CompanyExamStaffSubjectController@add');// 添加
// Route::get('admin/company_exam_staff_subject/select', 'Admin\QualityControl\CompanyExamStaffSubjectController@select');// 选择-弹窗
Route::get('admin/company_exam_staff_subject/export', 'Admin\QualityControl\CompanyExamStaffSubjectController@export');// 导出
Route::get('admin/company_exam_staff_subject/import_template', 'Admin\QualityControl\CompanyExamStaffSubjectController@import_template');// 导入模版
// Route::get('admin/company_exam_staff_subject/sms_send', 'Admin\QualityControl\CompanyExamStaffSubjectController@sms_send');// 选择短信模板页面

// 付款/收款相关的

// 付款/收款类型
Route::get('admin/payment_type', 'Admin\QualityControl\PaymentTypeController@index');// 列表
Route::get('admin/payment_type/add/{id}', 'Admin\QualityControl\PaymentTypeController@add');// 添加
// Route::get('admin/payment_type/select', 'Admin\QualityControl\PaymentTypeController@select');// 选择-弹窗
Route::get('admin/payment_type/export', 'Admin\QualityControl\PaymentTypeController@export');// 导出
Route::get('admin/payment_type/import_template', 'Admin\QualityControl\PaymentTypeController@import_template');// 导入模版
// Route::get('admin/payment_type/sms_send', 'Admin\QualityControl\PaymentTypeController@sms_send');// 选择短信模板页面

// 付款/收款项目
Route::get('admin/payment_project', 'Admin\QualityControl\PaymentProjectController@index');// 列表
Route::get('admin/payment_project/add/{id}', 'Admin\QualityControl\PaymentProjectController@add');// 添加
// Route::get('admin/payment_project/select', 'Admin\QualityControl\PaymentProjectController@select');// 选择-弹窗
Route::get('admin/payment_project/export', 'Admin\QualityControl\PaymentProjectController@export');// 导出
Route::get('admin/payment_project/import_template', 'Admin\QualityControl\PaymentProjectController@import_template');// 导入模版
// Route::get('admin/payment_project/sms_send', 'Admin\QualityControl\PaymentProjectController@sms_send');// 选择短信模板页面
Route::get('admin/payment_project/add_pay/{id}', 'Admin\QualityControl\PaymentProjectController@add_pay');// 收款填写页面

// 付款/收款记录
Route::get('admin/payment_record', 'Admin\QualityControl\PaymentRecordController@index');// 列表
Route::get('admin/payment_record/add/{id}', 'Admin\QualityControl\PaymentRecordController@add');// 添加
// Route::get('admin/payment_record/select', 'Admin\QualityControl\PaymentRecordController@select');// 选择-弹窗
Route::get('admin/payment_record/export', 'Admin\QualityControl\PaymentRecordController@export');// 导出
Route::get('admin/payment_record/import_template', 'Admin\QualityControl\PaymentRecordController@import_template');// 导入模版
// Route::get('admin/payment_record/sms_send', 'Admin\QualityControl\PaymentRecordController@sms_send');// 选择短信模板页面
Route::get('admin/payment_record/pay', 'Admin\QualityControl\PaymentRecordController@pay');// 缴费
Route::get('admin/payment_record/pay_save', 'Admin\QualityControl\PaymentRecordController@pay_save');// 缴费页面

// 付款/收款记录流水
Route::get('admin/payment_record_flow', 'Admin\QualityControl\PaymentRecordFlowController@index');// 列表
Route::get('admin/payment_record_flow/add/{id}', 'Admin\QualityControl\PaymentRecordFlowController@add');// 添加
// Route::get('admin/payment_record_flow/select', 'Admin\QualityControl\PaymentRecordFlowController@select');// 选择-弹窗
Route::get('admin/payment_record_flow/export', 'Admin\QualityControl\PaymentRecordFlowController@export');// 导出
Route::get('admin/payment_record_flow/import_template', 'Admin\QualityControl\PaymentRecordFlowController@import_template');// 导入模版
// Route::get('admin/payment_record_flow/sms_send', 'Admin\QualityControl\PaymentRecordFlowController@sms_send');// 选择短信模板页面

// 付款/收款记录操作日志
Route::get('admin/payment_record_log', 'Admin\QualityControl\PaymentRecordLogController@index');// 列表
Route::get('admin/payment_record_log/add/{id}', 'Admin\QualityControl\PaymentRecordLogController@add');// 添加
// Route::get('admin/payment_record_log/select', 'Admin\QualityControl\PaymentRecordLogController@select');// 选择-弹窗
Route::get('admin/payment_record_log/export', 'Admin\QualityControl\PaymentRecordLogController@export');// 导出
Route::get('admin/payment_record_log/import_template', 'Admin\QualityControl\PaymentRecordLogController@import_template');// 导入模版
// Route::get('admin/payment_record_log/sms_send', 'Admin\QualityControl\PaymentRecordLogController@sms_send');// 选择短信模板页面


// 对外提供接口
// 证书-能力范围
Route::get('admin/API/certificate_schedule', 'Admin\QualityControl\API\CertificateScheduleController@index');// 列表
Route::get('admin/API/certificate_schedule/add/{id}', 'Admin\QualityControl\API\CertificateScheduleController@add');// 添加
// Route::get('admin/API/certificate_schedule/select', 'Admin\QualityControl\API\CertificateScheduleController@select');// 选择-弹窗
//Route::get('admin/API/certificate_schedule/export', 'Admin\QualityControl\API\CertificateScheduleController@export');// 导出
//Route::get('admin/API/certificate_schedule/import_template', 'Admin\QualityControl\API\CertificateScheduleController@import_template');// 导入模版
// Route::get('admin/course_order_staff/sms_send', 'Admin\QualityControl\CourseOrderStaffController@sms_send');// 选择短信模板页面

//Route::get('admin/API/certificate_schedule/add_excel/{id}', 'Admin\QualityControl\API\CertificateScheduleController@add_excel');// 添加--导入

Route::get('admin/API/certificate_schedule/get_alist_api/{id}', 'Admin\QualityControl\API\CertificateScheduleController@get_alist_api');// 企业文件信息
Route::get('admin/API/certificate_schedule/add_bath_api/{id}', 'Admin\QualityControl\API\CertificateScheduleController@add_bath_api');// 能力范围及能力附表添加--导入
Route::get('admin/API/certificate_schedule/add_files_api/{id}', 'Admin\QualityControl\API\CertificateScheduleController@add_files_api');// 企业文件信息
Route::get('admin/API/certificate_schedule/add_bath_modify_api/{id}', 'Admin\QualityControl\API\CertificateScheduleController@add_bath_modify_api');// 能力范围删除或新加-修改
Route::get('admin/API/certificate_schedule/add_modify_api/{id}', 'Admin\QualityControl\API\CertificateScheduleController@add_modify_api');// 注册/修改企业信息
Route::get('admin/API/certificate_schedule/add_update_api/{id}', 'Admin\QualityControl\API\CertificateScheduleController@add_update_api');// 根据条件修改能力范围

// 企业后台 company
Route::get('company/login', 'WebFront\Company\QualityControl\IndexController@login');// login.html 登录
Route::get('company/reg', 'WebFront\Company\QualityControl\IndexController@reg');// 注册
Route::get('company/perfect_company', 'WebFront\Company\QualityControl\IndexController@perfect_company');// 注册-补充企业资料
Route::get('company/user_company', 'WebFront\Company\QualityControl\IndexController@user_company');// 注册-补充用户资料

// 首页
Route::get('company/test', 'WebFront\Company\QualityControl\IndexController@test');// 测试
Route::get('company/index', 'WebFront\Company\QualityControl\IndexController@index');// 首页--ok
Route::get('company', 'WebFront\Company\QualityControl\IndexController@index');// --ok
Route::get('company/login', 'WebFront\Company\QualityControl\IndexController@login');//login.html 登录--ok
Route::get('company/logout', 'WebFront\Company\QualityControl\IndexController@logout');// 注销--ok
Route::get('company/password', 'WebFront\Company\QualityControl\IndexController@password');//psdmodify.html 个人信息-修改密码--ok
Route::get('company/info', 'WebFront\Company\QualityControl\IndexController@info');//myinfo.html 个人信息--显示--ok
//Route::get('company/down_drive', 'WebFront\Company\QualityControl\IndexController@down_drive');// 下载网页打印机驱动
Route::get('company/basic', 'WebFront\Company\QualityControl\IndexController@basic');// 修改企业基本信息

Route::get('company/down_file', 'WebFront\Company\QualityControl\IndexController@down_file');// 下载文件

// 个从帐号管理
Route::get('company/user', 'WebFront\Company\QualityControl\UserController@index');// 列表
Route::get('company/user/add/{id}', 'WebFront\Company\QualityControl\UserController@add');// 添加
// Route::get('company/user/show/{company_id}', 'WebFront\Company\QualityControl\UserController@show');// 查看
//Route::get('company/user/show_add/{id}', 'WebFront\Company\QualityControl\UserController@show_add');// 添加
Route::get('company/user/select', 'WebFront\Company\QualityControl\UserController@select');// 选择-弹窗
Route::get('company/user/export', 'WebFront\Company\QualityControl\UserController@export');// 导出
Route::get('company/user/import_template', 'WebFront\Company\QualityControl\UserController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面
Route::get('company/user/import_bath/{company_id}', 'WebFront\Company\QualityControl\UserController@import_bath');// 导入批量

// 企业内容管理
Route::get('company/company_content', 'WebFront\Company\QualityControl\CompanyContentController@index');// 列表
Route::get('company/company_content/add/{id}', 'WebFront\Company\QualityControl\CompanyContentController@add');// 添加
// Route::get('company/company_content/select', 'WebFront\Company\QualityControl\CompanyContentController@select');// 选择-弹窗
Route::get('company/company_content/export', 'WebFront\Company\QualityControl\CompanyContentController@export');// 导出
Route::get('company/company_content/import_template', 'WebFront\Company\QualityControl\CompanyContentController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

Route::get('company/company_content/basic/{id}', 'WebFront\Company\QualityControl\CompanyContentController@basic');// 添加
// 能力验证
Route::get('company/abilitys', 'WebFront\Company\QualityControl\AbilitysController@index');// 列表
Route::get('company/abilitys/info/{id}', 'WebFront\Company\QualityControl\AbilitysController@info');// 查看-详情
Route::get('company/abilitys/join/{ids}', 'WebFront\Company\QualityControl\AbilitysController@join');// 报名
//Route::get('company/abilitys/add/{id}', 'WebFront\Company\QualityControl\AbilitysController@add');// 添加
// Route::get('company/abilitys/select', 'WebFront\Company\QualityControl\AbilitysController@select');// 选择-弹窗
//Route::get('company/abilitys/export', 'WebFront\Company\QualityControl\AbilitysController@export');// 导出
//Route::get('company/abilitys/import_template', 'WebFront\Company\QualityControl\AbilitysController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

// 能力验证--报名管理
Route::get('company/ability_join', 'WebFront\Company\QualityControl\AbilityJoinController@index');// 列表
//Route::get('company/ability_join/add/{id}', 'WebFront\Company\QualityControl\AbilityJoinController@add');// 添加
Route::get('company/ability_join/info/{id}', 'WebFront\Company\QualityControl\AbilityJoinController@info');// 查看-详情
// Route::get('company/ability_join/select', 'WebFront\Company\QualityControl\AbilityJoinController@select');// 选择-弹窗
//Route::get('company/ability_join/export', 'WebFront\Company\QualityControl\AbilityJoinController@export');// 导出
//Route::get('company/ability_join/import_template', 'WebFront\Company\QualityControl\AbilityJoinController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

// 能力验证--项目管理
Route::get('company/ability_join_item', 'WebFront\Company\QualityControl\AbilityJoinItemsController@index');// 列表
//Route::get('company/ability_join_item/add/{id}', 'WebFront\Company\QualityControl\AbilityJoinItemsController@add');// 添加
//Route::get('company/ability_join_item/info/{id}', 'WebFront\Company\QualityControl\AbilityJoinItemsController@info');// 查看-详情
// Route::get('company/ability_join_item/select', 'WebFront\Company\QualityControl\AbilityJoinItemsController@select');// 选择-弹窗
//Route::get('company/ability_join_item/export', 'WebFront\Company\QualityControl\AbilityJoinItemsController@export');// 导出
//Route::get('company/ability_join_item/import_template', 'WebFront\Company\QualityControl\AbilityJoinItemsController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

Route::get('company/ability_join_item/sample_result/{id}', 'WebFront\Company\QualityControl\AbilityJoinItemsController@sample_result');// 数据上报
Route::get('company/ability_join_item/sample_result_info/{id}/{retry_no}', 'WebFront\Company\QualityControl\AbilityJoinItemsController@sample_result_info');// 获得指定测试序号的 单次测试数据

// 企业能力附表
Route::get('company/company_schedule', 'WebFront\Company\QualityControl\CompanyScheduleController@index');// 列表
Route::get('company/company_schedule/add/{id}', 'WebFront\Company\QualityControl\CompanyScheduleController@add');// 添加
// Route::get('company/company_schedule/select', 'WebFront\Company\QualityControl\CompanyScheduleController@select');// 选择-弹窗
//Route::get('company/company_schedule/export', 'WebFront\Company\QualityControl\CompanyScheduleController@export');// 导出
//Route::get('company/company_schedule/import_template', 'WebFront\Company\QualityControl\CompanyScheduleController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

// 企业能力附表
Route::get('company/company_new_schedule', 'WebFront\Company\QualityControl\CompanyNewScheduleController@index');// 列表
Route::get('company/company_new_schedule/add/{id}', 'WebFront\Company\QualityControl\CompanyNewScheduleController@add');// 添加
Route::get('company/company_new_schedule/add_excel/{id}', 'WebFront\Company\QualityControl\CompanyNewScheduleController@add_excel');// 添加
// Route::get('company/company_new_schedule/select', 'WebFront\Company\QualityControl\CompanyNewScheduleController@select');// 选择-弹窗
//Route::get('company/company_new_schedule/export', 'WebFront\Company\QualityControl\CompanyNewScheduleController@export');// 导出
//Route::get('company/company_new_schedule/import_template', 'WebFront\Company\QualityControl\CompanyNewScheduleController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

Route::get('company/company_new_schedule/down_moban', 'WebFront\Company\QualityControl\CompanyNewScheduleController@down_moban');// 下载文件模板


// 课程管理
Route::get('company/course', 'WebFront\Company\QualityControl\CourseController@index');// 列表
//Route::get('company/course/add/{id}', 'WebFront\Company\QualityControl\CourseController@add');// 添加
// Route::get('company/course/select', 'WebFront\Company\QualityControl\CourseController@select');// 选择-弹窗
//Route::get('company/course/export', 'WebFront\Company\QualityControl\CourseController@export');// 导出
//Route::get('company/course/import_template', 'WebFront\Company\QualityControl\CourseController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面
Route::get('company/course/join/{id}', 'WebFront\Company\QualityControl\CourseController@join');// 报名

// 报名企业(主表)
Route::get('company/course_order', 'WebFront\Company\QualityControl\CourseOrderController@index');// 列表
//Route::get('company/course_order/add/{id}', 'WebFront\Company\QualityControl\CourseOrderController@add');// 添加
Route::get('company/course_order/info/{id}', 'WebFront\Company\QualityControl\CourseOrderController@info');// 查看-详情
// Route::get('company/course_order/select', 'WebFront\Company\QualityControl\CourseOrderController@select');// 选择-弹窗
Route::get('company/course_order/export', 'WebFront\Company\QualityControl\CourseOrderController@export');// 导出
// Route::get('company/course_order/import_template', 'WebFront\Company\QualityControl\CourseOrderController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

Route::get('company/course_order/pay', 'WebFront\Company\QualityControl\CourseOrderController@pay');// 缴费

// 报名学员
Route::get('company/course_order_staff', 'WebFront\Company\QualityControl\CourseOrderStaffController@index');// 列表
//Route::get('company/course_order_staff/add/{id}', 'WebFront\Company\QualityControl\CourseOrderStaffController@add');// 添加
// Route::get('company/course_order_staff/select', 'WebFront\Company\QualityControl\CourseOrderStaffController@select');// 选择-弹窗
Route::get('company/course_order_staff/export', 'WebFront\Company\QualityControl\CourseOrderStaffController@export');// 导出
//Route::get('company/course_order_staff/import_template', 'WebFront\Company\QualityControl\CourseOrderStaffController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

// Route::get('company/course_order_staff/join_class', 'WebFront\Company\QualityControl\CourseOrderStaffController@join_class');// 分班
Route::get('company/course_order_staff/pay', 'WebFront\Company\QualityControl\CourseOrderStaffController@pay');// 缴费
Route::get('company/course_order_staff/pay_save', 'WebFront\Company\QualityControl\CourseOrderStaffController@pay_save');// 缴费页面

// 收款订单
Route::get('company/orders', 'WebFront\Company\QualityControl\OrdersController@index');// 列表
//Route::get('company/orders/add/{id}', 'WebFront\Company\QualityControl\OrdersController@add');// 添加
Route::get('company/orders/info/{id}', 'WebFront\Company\QualityControl\OrdersController@info');// 查看-详情
// Route::get('company/orders/select', 'WebFront\Company\QualityControl\OrdersController@select');// 选择-弹窗
Route::get('company/orders/export', 'WebFront\Company\QualityControl\OrdersController@export');// 导出
// Route::get('company/orders/import_template', 'WebFront\Company\QualityControl\OrdersController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面
Route::get('company/orders/invoices', 'WebFront\Company\QualityControl\OrdersController@invoices');// 开电子发票
Route::get('company/orders/invoices_cancel', 'WebFront\Company\QualityControl\OrdersController@invoices_cancel');// 电子发票全额冲红

// 收款订单财务流水
Route::get('company/order_flow', 'WebFront\Company\QualityControl\OrderFlowController@index');// 列表
//Route::get('company/order_flow/add/{id}', 'WebFront\Company\QualityControl\OrderFlowController@add');// 添加
Route::get('company/order_flow/info/{id}', 'WebFront\Company\QualityControl\OrderFlowController@info');// 查看-详情
// Route::get('company/order_flow/select', 'WebFront\Company\QualityControl\OrderFlowController@select');// 选择-弹窗
Route::get('company/order_flow/export', 'WebFront\Company\QualityControl\OrderFlowController@export');// 导出
// Route::get('company/order_flow/import_template', 'WebFront\Company\QualityControl\OrderFlowController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

// 第三方对帐单
Route::get('company/order_pay', 'WebFront\Company\QualityControl\OrderPayController@index');// 列表
Route::get('company/order_pay/add/{id}', 'WebFront\Company\QualityControl\OrderPayController@add');// 添加
Route::get('company/order_pay/info/{id}', 'WebFront\Company\QualityControl\OrderPayController@info');// 查看-详情
// Route::get('company/order_pay/select', 'WebFront\Company\QualityControl\OrderPayController@select');// 选择-弹窗
Route::get('company/order_pay/export', 'WebFront\Company\QualityControl\OrderPayController@export');// 导出
Route::get('company/order_pay/import_template', 'WebFront\Company\QualityControl\OrderPayController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

// 发票配置购买方
Route::get('company/invoice_buyer', 'WebFront\Company\QualityControl\InvoiceBuyerController@index');// 列表
Route::get('company/invoice_buyer/add/{id}', 'WebFront\Company\QualityControl\InvoiceBuyerController@add');// 添加
Route::get('company/invoice_buyer/info/{id}', 'WebFront\Company\QualityControl\InvoiceBuyerController@info');// 查看-详情
// Route::get('company/invoice_buyer/select', 'WebFront\Company\QualityControl\InvoiceBuyerController@select');// 选择-弹窗
Route::get('company/invoice_buyer/export', 'WebFront\Company\QualityControl\InvoiceBuyerController@export');// 导出
Route::get('company/invoice_buyer/import_template', 'WebFront\Company\QualityControl\InvoiceBuyerController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

// 发票主表
Route::get('company/invoices', 'WebFront\Company\QualityControl\InvoicesController@index');// 列表
Route::get('company/invoices/add/{id}', 'WebFront\Company\QualityControl\InvoicesController@add');// 添加
Route::get('company/invoices/info/{id}', 'WebFront\Company\QualityControl\InvoicesController@info');// 查看-详情
// Route::get('company/invoices/select', 'WebFront\Company\QualityControl\InvoicesController@select');// 选择-弹窗
Route::get('company/invoices/export', 'WebFront\Company\QualityControl\InvoicesController@export');// 导出
Route::get('company/invoices/import_template', 'WebFront\Company\QualityControl\InvoicesController@import_template');// 导入模版
// Route::get('company/user/sms_send', 'WebFront\Company\QualityControl\UserController@sms_send');// 选择短信模板页面

// 点播课程
Route::get('company/vods', 'WebFront\Company\QualityControl\VodsController@index');// 列表
//Route::get('company/vods/add/{id}', 'WebFront\Company\QualityControl\VodsController@add');// 添加
Route::get('company/vods/info/{id}', 'WebFront\Company\QualityControl\VodsController@info');// 查看-详情
// Route::get('company/vods/select', 'WebFront\Company\QualityControl\VodsController@select');// 选择-弹窗
//Route::get('company/vods/export', 'WebFront\Company\QualityControl\VodsController@export');// 导出
//Route::get('company/vods/import_template', 'WebFront\Company\QualityControl\VodsController@import_template');// 导入模版
// Route::get('company/vods/sms_send', 'WebFront\Company\QualityControl\VodsController@sms_send');// 选择短信模板页面

// 点播课程订单
Route::get('company/vod_orders', 'WebFront\Company\QualityControl\VodOrdersController@index');// 列表
//Route::get('company/vod_orders/add/{id}', 'WebFront\Company\QualityControl\VodOrdersController@add');// 添加
Route::get('company/vod_orders/info/{id}', 'WebFront\Company\QualityControl\VodOrdersController@info');// 查看-详情
// Route::get('company/vod_orders/select', 'WebFront\Company\QualityControl\VodOrdersController@select');// 选择-弹窗
//Route::get('company/vod_orders/export', 'WebFront\Company\QualityControl\VodOrdersController@export');// 导出
//Route::get('company/vod_orders/import_template', 'WebFront\Company\QualityControl\VodOrdersController@import_template');// 导入模版
// Route::get('company/vod_orders/sms_send', 'WebFront\Company\QualityControl\VodOrdersController@sms_send');// 选择短信模板页面
Route::get('company/vod_orders/pay', 'WebFront\Company\QualityControl\VodOrdersController@pay');// 缴费
Route::get('company/vod_orders/pay_save', 'WebFront\Company\QualityControl\VodOrdersController@pay_save');// 缴费页面


// 用户中心 user
Route::get('user/login', 'WebFront\User\QualityControl\IndexController@login');// login.html 登录
Route::get('user/reg', 'WebFront\User\QualityControl\IndexController@reg');// 注册
Route::get('user/perfect_company', 'WebFront\User\QualityControl\IndexController@perfect_company');// 注册-补充企业资料
Route::get('user/user_company', 'WebFront\User\QualityControl\IndexController@user_company');// 注册-补充用户资料

// 首页
Route::get('user/test', 'WebFront\User\QualityControl\IndexController@test');// 测试
Route::get('user/index', 'WebFront\User\QualityControl\IndexController@index');// 首页--ok
Route::get('user', 'WebFront\User\QualityControl\IndexController@index');// --ok
Route::get('user/login', 'WebFront\User\QualityControl\IndexController@login');//login.html 登录--ok
Route::get('user/logout', 'WebFront\User\QualityControl\IndexController@logout');// 注销--ok
Route::get('user/password', 'WebFront\User\QualityControl\IndexController@password');//psdmodify.html 个人信息-修改密码--ok
Route::get('user/info', 'WebFront\User\QualityControl\IndexController@info');//myinfo.html 个人信息--显示--ok
//Route::get('user/down_drive', 'WebFront\User\QualityControl\IndexController@down_drive');// 下载网页打印机驱动
Route::get('user/down_file', 'WebFront\User\QualityControl\IndexController@down_file');// 下载文件

// 点播课程
Route::get('user/vods', 'WebFront\User\QualityControl\VodsController@index');// 列表
//Route::get('user/vods/add/{id}', 'WebFront\User\QualityControl\VodsController@add');// 添加
Route::get('user/vods/info/{id}', 'WebFront\User\QualityControl\VodsController@info');// 查看-详情
// Route::get('user/vods/select', 'WebFront\User\QualityControl\VodsController@select');// 选择-弹窗
//Route::get('user/vods/export', 'WebFront\User\QualityControl\VodsController@export');// 导出
//Route::get('user/vods/import_template', 'WebFront\User\QualityControl\VodsController@import_template');// 导入模版
// Route::get('user/vods/sms_send', 'WebFront\User\QualityControl\VodsController@sms_send');// 选择短信模板页面

// 点播课程订单
Route::get('user/vod_orders', 'WebFront\User\QualityControl\VodOrdersController@index');// 列表
//Route::get('user/vod_orders/add/{id}', 'WebFront\User\QualityControl\VodOrdersController@add');// 添加
Route::get('user/vod_orders/info/{id}', 'WebFront\User\QualityControl\VodOrdersController@info');// 查看-详情
// Route::get('user/vod_orders/select', 'WebFront\User\QualityControl\VodOrdersController@select');// 选择-弹窗
//Route::get('user/vod_orders/export', 'WebFront\User\QualityControl\VodOrdersController@export');// 导出
//Route::get('user/vod_orders/import_template', 'WebFront\User\QualityControl\VodOrdersController@import_template');// 导入模版
// Route::get('user/vod_orders/sms_send', 'WebFront\User\QualityControl\VodOrdersController@sms_send');// 选择短信模板页面
Route::get('user/vod_orders/pay', 'WebFront\User\QualityControl\VodOrdersController@pay');// 缴费
Route::get('user/vod_orders/pay_save', 'WebFront\User\QualityControl\VodOrdersController@pay_save');// 缴费页面

// 付款/收款相关的
// 付款/收款项目
Route::get('user/payment_project', 'WebFront\User\QualityControl\PaymentProjectController@index');// 列表
Route::get('user/payment_project/add/{id}', 'WebFront\User\QualityControl\PaymentProjectController@add');// 添加
// Route::get('user/payment_project/select', 'WebFront\User\QualityControl\PaymentProjectController@select');// 选择-弹窗
Route::get('user/payment_project/export', 'WebFront\User\QualityControl\PaymentProjectController@export');// 导出
Route::get('user/payment_project/import_template', 'WebFront\User\QualityControl\PaymentProjectController@import_template');// 导入模版
// Route::get('user/payment_project/sms_send', 'WebFront\User\QualityControl\PaymentProjectController@sms_send');// 选择短信模板页面
Route::get('user/payment_project/add_pay/{id}', 'WebFront\User\QualityControl\PaymentProjectController@add_pay');// 收款填写页面

// 付款/收款记录
Route::get('user/payment_record', 'WebFront\User\QualityControl\PaymentRecordController@index');// 列表
Route::get('user/payment_record/add/{id}', 'WebFront\User\QualityControl\PaymentRecordController@add');// 添加
// Route::get('user/payment_record/select', 'WebFront\User\QualityControl\PaymentRecordController@select');// 选择-弹窗
Route::get('user/payment_record/export', 'WebFront\User\QualityControl\PaymentRecordController@export');// 导出
Route::get('user/payment_record/import_template', 'WebFront\User\QualityControl\PaymentRecordController@import_template');// 导入模版
// Route::get('user/payment_record/sms_send', 'WebFront\User\QualityControl\PaymentRecordController@sms_send');// 选择短信模板页面
Route::get('user/payment_record/pay', 'WebFront\User\QualityControl\PaymentRecordController@pay');// 缴费
Route::get('user/payment_record/pay_save', 'WebFront\User\QualityControl\PaymentRecordController@pay_save');// 缴费页面

// 付款/收款记录流水
Route::get('user/payment_record_flow', 'WebFront\User\QualityControl\PaymentRecordFlowController@index');// 列表
Route::get('user/payment_record_flow/add/{id}', 'WebFront\User\QualityControl\PaymentRecordFlowController@add');// 添加
// Route::get('user/payment_record_flow/select', 'WebFront\User\QualityControl\PaymentRecordFlowController@select');// 选择-弹窗
Route::get('user/payment_record_flow/export', 'WebFront\User\QualityControl\PaymentRecordFlowController@export');// 导出
Route::get('user/payment_record_flow/import_template', 'WebFront\User\QualityControl\PaymentRecordFlowController@import_template');// 导入模版
// Route::get('user/payment_record_flow/sms_send', 'WebFront\User\QualityControl\PaymentRecordFlowController@sms_send');// 选择短信模板页面

// 付款/收款记录操作日志
Route::get('user/payment_record_log', 'WebFront\User\QualityControl\PaymentRecordLogController@index');// 列表
Route::get('user/payment_record_log/add/{id}', 'WebFront\User\QualityControl\PaymentRecordLogController@add');// 添加
// Route::get('user/payment_record_log/select', 'WebFront\User\QualityControl\PaymentRecordLogController@select');// 选择-弹窗
Route::get('user/payment_record_log/export', 'WebFront\User\QualityControl\PaymentRecordLogController@export');// 导出
Route::get('user/payment_record_log/import_template', 'WebFront\User\QualityControl\PaymentRecordLogController@import_template');// 导入模版
// Route::get('user/payment_record_log/sms_send', 'WebFront\User\QualityControl\PaymentRecordLogController@sms_send');// 选择短信模板页面

// 前台 web
Route::get('web/test', 'WebFront\Web\QualityControl\HomeController@test');// 测试
Route::get('web/login', 'WebFront\Web\QualityControl\HomeController@login');// login.html 登录
Route::get('web/reg_agree', 'WebFront\Web\QualityControl\HomeController@reg_agree');// 注册服务协议
Route::get('web/reg', 'WebFront\Web\QualityControl\HomeController@reg');// 注册
Route::get('web/perfect_company', 'WebFront\Web\QualityControl\HomeController@perfect_company');// 注册-补充企业资料
Route::get('web/select_company', 'WebFront\Web\QualityControl\HomeController@select_company');// 注册-补充用户资料--选择所属企业
Route::get('web/perfect_user', 'WebFront\Web\QualityControl\HomeController@perfect_user');// 注册-补充用户资料
Route::get('web/logout', 'WebFront\Web\QualityControl\HomeController@logout');// 注销--ok


Route::get('web/login_company', 'WebFront\Web\QualityControl\HomeController@login_company');// login.html 登录--为登录测试  补充资料用
Route::get('web/login_user', 'WebFront\Web\QualityControl\HomeController@login_user');// login.html 登录--为登录测试  补充资料用

// 资质认定获证机构查询
Route::get('web/certificate', 'WebFront\Web\QualityControl\Certificate\CertificateScheduleController@index');// 查询
Route::get('web/certificate/index', 'WebFront\Web\QualityControl\Certificate\CertificateScheduleController@index');// 查询
//Route::get('web/certificate/list', 'WebFront\Web\QualityControl\Certificate\CertificateScheduleController@list');// 企业列表


//  上面的另一种路由方式
Route::get('jigou', 'WebFront\Web\QualityControl\Certificate\CertificateScheduleController@index');// 查询
Route::get('jigou/index', 'WebFront\Web\QualityControl\Certificate\CertificateScheduleController@index');// 查询
// Route::get('jigou/certificate/index', 'WebFront\Web\QualityControl\Certificate\CertificateScheduleController@index');// 查询
//Route::get('web/certificate/list', 'WebFront\Web\QualityControl\Certificate\CertificateScheduleController@list');// 企业列表

// 企业列表
// 城市id  city_id
// 行业id  industry_id
// field   检验机构名称  统一社会信用代码或组织机构代码   证书号
// keyword
// 每页数量  pagesize
// 当前页号 page
//total
//?field=&keyword=
Route::get('web/certificate/company/{city_id}_{industry_id}_{pagesize}_{page}', 'WebFront\Web\QualityControl\Certificate\CertificateScheduleController@company');

Route::get('web/certificate/info/{id}', 'WebFront\Web\QualityControl\Certificate\CertificateScheduleController@info');// 企业资质认定详情列表


//  上面的另一种路由方式
Route::get('jigou/list/{city_id}_{industry_id}_{pagesize}_{page}', 'WebFront\Web\QualityControl\Certificate\CertificateScheduleController@company');

Route::get('jigou/info/{id}', 'WebFront\Web\QualityControl\Certificate\CertificateScheduleController@info');// 企业资质认定详情列表

// 企业帐号管理
//Route::get('web/company', 'WebFront\Web\QualityControl\Site\CompanyController@index');// 列表
//Route::get('web/company/add/{id}', 'WebFront\Web\QualityControl\Site\CompanyController@add');// 添加
Route::get('web/company/info/{id}', 'WebFront\Web\QualityControl\Site\CompanyController@info');// 查看-详情
//Route::get('web/company/select', 'WebFront\Web\QualityControl\Site\CompanyController@select');// 选择-弹窗
//Route::get('web/company/export', 'WebFront\Web\QualityControl\Site\CompanyController@export');// 导出
//Route::get('web/company/import_template', 'WebFront\Web\QualityControl\Site\CompanyController@import_template');// 导入模版
//Route::get('web/company/sms_send', 'WebFront\Web\QualityControl\Site\CompanyController@sms_send');// 选择短信模板页面

Route::get('web/company/iframe', 'WebFront\Web\QualityControl\Site\CompanyController@iframe');// iframe切换
Route::get('web/company', 'WebFront\Web\QualityControl\Site\CompanyController@company');// 列表

// 企业列表
// 城市id  city_id
// 行业id  industry_id
// field   检验机构名称  统一社会信用代码或组织机构代码   证书号
// keyword
// 每页数量  pagesize
// 当前页号 page
//total
//?field=&keyword=
Route::get('web/company/{city_id}_{industry_id}_{pagesize}_{page}', 'WebFront\Web\QualityControl\Site\CompanyController@company');

// 直播公告管理
//Route::get('web/live_notice', 'WebFront\Web\QualityControl\Site\LiveNoticeController@index');// 列表
//Route::get('web/live_notice/add/{id}', 'WebFront\Web\QualityControl\Site\LiveNoticeController@add');// 添加
Route::get('web/live_notice/info/{id}', 'WebFront\Web\QualityControl\Site\LiveNoticeController@info');// 查看-详情
//Route::get('web/live_notice/select', 'WebFront\Web\QualityControl\Site\LiveNoticeController@select');// 选择-弹窗
//Route::get('web/live_notice/export', 'WebFront\Web\QualityControl\Site\LiveNoticeController@export');// 导出
//Route::get('web/live_notice/import_template', 'WebFront\Web\QualityControl\Site\LiveNoticeController@import_template');// 导入模版
//Route::get('web/live_notice/sms_send', 'WebFront\Web\QualityControl\Site\LiveNoticeController@sms_send');// 选择短信模板页面

Route::get('web/live_notice', 'WebFront\Web\QualityControl\Site\LiveNoticeController@list');// 列表

// 列表
// field   检验机构名称  统一社会信用代码或组织机构代码   证书号
// keyword
// 每页数量  pagesize
// 当前页号 page
//total
//?field=&keyword=
Route::get('web/live_notice/{pagesize}_{page}', 'WebFront\Web\QualityControl\Site\LiveNoticeController@list');

// 点播课程
//Route::get('web/vods', 'WebFront\Web\QualityControl\Site\VodsController@index');// 列表
//Route::get('web/vods/add/{id}', 'WebFront\Web\QualityControl\Site\VodsController@add');// 添加
Route::get('web/vods/info/{id}', 'WebFront\Web\QualityControl\Site\VodsController@info');// 查看-详情
// Route::get('web/vods/select', 'WebFront\Web\QualityControl\Site\VodsController@select');// 选择-弹窗
//Route::get('web/vods/export', 'WebFront\Web\QualityControl\Site\VodsController@export');// 导出
//Route::get('web/vods/import_template', 'WebFront\Web\QualityControl\Site\VodsController@import_template');// 导入模版
// Route::get('web/vods/sms_send', 'WebFront\Web\QualityControl\Site\VodsController@sms_send');// 选择短信模板页面
Route::get('web/vods/create_order/{id}', 'WebFront\Web\QualityControl\Site\VodsController@create_order');// 查看-详情


Route::get('web/vods', 'WebFront\Web\QualityControl\Site\VodsController@vods');// 列表
// 列表
// 分类id  vod_type_id
// 推荐   recommend_status
// 每页数量  pagesize
// 当前页号 page
//total
//?field=&keyword=
Route::get('web/vods/{vod_type_id}_{recommend_status}_{pagesize}_{page}', 'WebFront\Web\QualityControl\Site\VodsController@vods');
Route::get('web/vods/{vod_type_id}_{recommend_status}_{pagesize}', 'WebFront\Web\QualityControl\Site\VodsController@vods');
Route::get('web/vods/{vod_type_id}_{recommend_status}', 'WebFront\Web\QualityControl\Site\VodsController@vods');
Route::get('web/vods/{vod_type_id}', 'WebFront\Web\QualityControl\Site\VodsController@vods');

// 点播课程视频目录
Route::get('web/vod_video', 'WebFront\Web\QualityControl\Site\VodVideoController@index');// 列表
//Route::get('web/vod_video/add/{id}', 'WebFront\Web\QualityControl\Site\VodVideoController@add');// 添加
Route::get('web/vod_video/info/{id}', 'WebFront\Web\QualityControl\Site\VodVideoController@info');// 查看-详情
// Route::get('web/vod_video/select', 'WebFront\Web\QualityControl\Site\VodVideoController@select');// 选择-弹窗
//Route::get('web/vod_video/export', 'WebFront\Web\QualityControl\Site\VodVideoController@export');// 导出
//Route::get('web/vod_video/import_template', 'WebFront\Web\QualityControl\Site\VodVideoController@import_template');// 导入模版
// Route::get('web/vod_video/sms_send', 'WebFront\Web\QualityControl\Site\VodVideoController@sms_send');// 选择短信模板页面
//Route::get('web/vod_video/addDir/{id}', 'WebFront\Web\QualityControl\Site\VodVideoController@addDir');// 添加--目录
// 列表
// 所属点播课程id  vod_id
// 推荐   video_type
// 每页数量  pagesize
// 当前页号 page
//total
//?field=&keyword=
Route::get('web/vod_video/{vod_id}_{video_type}_{pagesize}_{page}', 'WebFront\Web\QualityControl\Site\VodVideoController@videos');
Route::get('web/vod_video/{vod_id}_{video_type}_{pagesize}', 'WebFront\Web\QualityControl\Site\VodVideoController@videos');
Route::get('web/vod_video/{vod_id}_{video_type}', 'WebFront\Web\QualityControl\Site\VodVideoController@videos');
Route::get('web/vod_video/{vod_id}', 'WebFront\Web\QualityControl\Site\VodVideoController@videos');

// 点播课程订单
Route::get('web/vod_orders', 'WebFront\Web\QualityControl\Site\VodOrdersController@index');// 列表
//Route::get('web/vod_orders/add/{id}', 'WebFront\Web\QualityControl\Site\VodOrdersController@add');// 添加
Route::get('web/vod_orders/info/{id}', 'WebFront\Web\QualityControl\Site\VodOrdersController@info');// 查看-详情
// Route::get('web/vod_orders/select', 'WebFront\Web\QualityControl\Site\VodOrdersController@select');// 选择-弹窗
//Route::get('web/vod_orders/export', 'WebFront\Web\QualityControl\Site\VodOrdersController@export');// 导出
//Route::get('web/vod_orders/import_template', 'WebFront\Web\QualityControl\Site\VodOrdersController@import_template');// 导入模版
// Route::get('web/vod_orders/sms_send', 'WebFront\Web\QualityControl\Site\VodOrdersController@sms_send');// 选择短信模板页面
Route::get('web/vod_orders/pay', 'WebFront\Web\QualityControl\Site\VodOrdersController@pay');// 缴费
Route::get('web/vod_orders/pay_save', 'WebFront\Web\QualityControl\Site\VodOrdersController@pay_save');// 缴费页面

// 陕西省市场监督管理局 market
// 陕西省检验机构信息管理平台

// 资质认定获证机构查询
Route::get('web/market', 'WebFront\Web\QualityControl\Market\MarketController@index');// 查询
Route::get('web/market/index', 'WebFront\Web\QualityControl\Market\MarketController@index');// 查询
//Route::get('web/market/list', 'WebFront\Web\QualityControl\Market\MarketController@list');// 企业列表

// 企业列表
   // 城市id  city_id
   // 行业id  industry_id
// field   检验机构名称  统一社会信用代码或组织机构代码   证书号
// keyword
// 每页数量  pagesize
// 当前页号 page
//total
//?field=&keyword=
//Route::get('web/market', 'WebFront\Web\QualityControl\Market\MarketController@company');
//Route::get('web/market/index', 'WebFront\Web\QualityControl\Market\MarketController@company');
Route::get('web/market/company', 'WebFront\Web\QualityControl\Market\MarketController@company');
Route::get('web/market/company/{city_id}_{industry_id}_{pagesize}_{page}', 'WebFront\Web\QualityControl\Market\MarketController@company');


Route::get('web/market/company/info/{id}', 'WebFront\Web\QualityControl\Market\MarketController@info');// 机构信息详情

Route::get('web/market/link', 'WebFront\Web\QualityControl\Market\MarketController@link');// 相关链接

Route::get('web/market/down_file', 'WebFront\Web\QualityControl\Market\MarketController@down_file');// 下载文件


// 企业能力附表-最新
Route::get('web/market/company_new_schedule', 'WebFront\Web\QualityControl\Market\CompanyNewScheduleController@index');// 列表

// 监督检查信息管理
// Route::get('web/market/company_supervise', 'WebFront\Web\QualityControl\Market\CompanySuperviseController@index');// 列表

Route::get('web/market/company_supervise/info/{id}', 'WebFront\Web\QualityControl\Market\CompanySuperviseController@info');// 添加

// 机构自我声明
Route::get('web/market/company_statement', 'WebFront\Web\QualityControl\Market\CompanyStatementController@index');// 列表

// 机构处罚
Route::get('web/market/company_punish', 'WebFront\Web\QualityControl\Market\CompanyPunishController@index');// 列表

// 通知公告
Route::get('web/market/platform_notices', 'WebFront\Web\QualityControl\Market\PlatformNoticesController@index');// 列表

// 表格下载
Route::get('web/market/platform_down_files', 'WebFront\Web\QualityControl\Market\PlatformDownFilesController@index');// 列表

// 能力验证结果导入
Route::get('web/market/company_ability', 'WebFront\Web\QualityControl\Market\CompanyAbilityController@index');// 列表

// 监督检查
Route::get('web/market/company_inspect', 'WebFront\Web\QualityControl\Market\CompanyInspectController@index');// 列表

// 企业其它【新闻】
Route::get('web/market/company_news', 'WebFront\Web\QualityControl\Market\CompanyNewsController@index');// 列表
Route::get('web/market/company_news/info/{id}', 'WebFront\Web\QualityControl\Market\CompanyNewsController@info');// 查看-详情

// 首页
//Route::get('web/test', 'WebFront\Web\QualityControl\IndexController@test');// 测试
//Route::get('web/index', 'WebFront\Web\QualityControl\IndexController@index');// 首页--ok
//Route::get('web', 'WebFront\Web\QualityControl\IndexController@index');// --ok
//Route::get('web/login', 'WebFront\Web\QualityControl\IndexController@login');//login.html 登录--ok
//Route::get('web/logout', 'WebFront\Web\QualityControl\IndexController@logout');// 注销--ok
//Route::get('web/password', 'WebFront\Web\QualityControl\IndexController@password');//psdmodify.html 个人信息-修改密码--ok
//Route::get('web/info', 'WebFront\Web\QualityControl\IndexController@info');//myinfo.html 个人信息--显示--ok
//Route::get('web/down_drive', 'WebFront\Web\QualityControl\IndexController@down_drive');// 下载网页打印机驱动

// Auth::routes();
Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');
//注册用户点击验证邮箱
Route::get('email/verify/{token}', 'EmailController@verify')->name('email.verify');
//保护路由
// 路由中间件 可用于仅允许经过验证的用户访问指定路由。Laravel 附带了 verified 中间件，
// 它定义在 Illuminate\Auth\Middleware\EnsureEmailIsVerified。由于此中间件已在应用程序的 HTTP 内核中注册，
// 因此您需要做的就是将中间件附加到路由定义：
//Route::get('profile', function () {
//    // 只有验证过的用户可以进入
//})->middleware('verified');

//  将Passport集成到您的Laravel API https://justlaravel.com/integrate-passport-laravel-api/
// 以调用注册视图
//Route::get('/register', function(){
//    return view('auth.register');
//})->name('register');
////Route::get('/login', function(){
////    return view('auth.login');
////})->name('login');
//Route::post('/register', 'PassportController@register');

//Route::get('/testGetToken', function () {
//    $client = new GuzzleHttp\Client;
//    $response = $client->post('http://runbuy.admin.cunwo.net/oauth/token', [
//        'form_params' => [
//            'client_id' => 8,// the_client_id_obtained_when_registered_to_API,
//            'client_secret' => 'z4iTk0hTiEaz7amHC1FjdSTopfYG0sjJqlCmoLGd',// 'the_client_secret_obtained_when_registered_to_API',
//            'grant_type' => 'password',
//            'username' => 'dfsdfsd@qq.com',// 'dfsdfsd@qq.com',
//            'password' => '123456',
//            'scope' => '*',
//        ]
//    ]);
//
//    $auth = json_decode( (string) $response->getBody() );
//    $response = $client->get('http://runbuy.admin.cunwo.net/api/users', [
//        'headers' => [
//            'Authorization' => 'Bearer '.$auth->access_token,
//        ]
//    ]);
//    $details = json_decode( (string) $response->getBody() );
//    return view('testGetToken', ['details' => $details]);
//});

// https://learnku.com/docs/laravel/5.6/passport/1380
//请求令牌
//授权时的重定向
//Route::get('/redirect', function () {
//    $query = http_build_query([
//        'client_id' => 5,//'client-id',
//        'redirect_uri' => 'http://runbuy.admin.cunwo.net/api/auth/callback',// 'http://example.com/callback',
//        'response_type' => 'code',
//        'scope' => '',
//    ]);
//    $redirectUrl = 'http://runbuy.admin.cunwo.net/oauth/authorize?' . $query;// 'http://your-app.com/oauth/authorize?'.$query
//    return redirect($redirectUrl);
//});
// 密码授权令牌
// 请求令牌
/**
 *
 *  {
 *      "token_type": "Bearer",
 *      "expires_in": 1296000,
 *      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjJhNmVmMGZkN2U3NzJjYjk3ZGU4MjA0NGI2MzhiNjcyZTE3MTAzMTBlZmY3NWU3YWYyMzczNzg2NDRjOGNmNjE0MjVmZGQ2OGZlMzM2ODE4In0.eyJhdWQiOiIyIiwianRpIjoiMmE2ZWYwZmQ3ZTc3MmNiOTdkZTgyMDQ0YjYzOGI2NzJlMTcxMDMxMGVmZjc1ZTdhZjIzNzM3ODY0NGM4Y2Y2MTQyNWZkZDY4ZmUzMzY4MTgiLCJpYXQiOjE1NzE3MDA5MjAsIm5iZiI6MTU3MTcwMDkyMCwiZXhwIjoxNTcyOTk2OTIwLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.Wnwxpe7RzdSkr7N71GQ28BLweFyX3OA8FPgTcj5tbWYKSlE1cYbZ0E7V1UhmWabSzeff6VL6R16srXMwKEYQdE4_JyTcxhOIaCM633gLphYzgLjXDAIJmuuwiaMyMq0QL1_mtyNPF_eLXHiC8I9uD5kXaxqaIw393Ed98Bo6glSVO62Eg3R_yFXrjncw0FNiC0kx02hD71C5NAMFrpEyCP1ATlJqEZAJG7qGJoPq1uoYFSLeKrFwpbtPMKokD2a-gDd3re6i4faO5mxplaESDztHHA6lN0toU5GdgpggCAXriEA4GioncGOUpkdS9QCNxQKt-2rSd4An1iWnVUhhEaF-BI4kzHmCtyGQjP-gyuDhkDQvgP_b7cOjuqmnbTZRWsd6wuX75vfCeUxIMiu1eLKdAX_xEx3WPbq62hZnMkyKaZmUY-wXmWCaWzdaOt4ANqvsWoZs31HBGUinUhPvQrAb18iEN2vsNdESNowicpoXrCzxzyFk67MHMegB8GvY8-sDykcobWvx4Dqp5VGXU701Vhb2Eti5MeFcJ4OpQgEh4xLgPBgoF29oXJuqdBGnCH-dDZUmY-yW2h6M-moU2_VIXztw-GP4tXvl7FOlOfpRXGo-SUWetZ9LDiyh2PAGCbYuckQqCNbqSnY3J0oFZ9yI1pWZdfEU7U0T9MPM8Gc",
 *      "refresh_token": "def5020041714f3b344600b69e10314ca79298e2f8a289e933bcbd9d429eb9623d97077554f511a80a35541bdd2ff74307f92f29a5c5b5fa1bb8e0cae82a423d3e74714534bd0b29ca27956a6d332b2b2098b0bc1dae9c23b39e30053f2e1fd02085e86b501bcbf93164d3374bcfb8104408b62d936a877b98e3fa32b1d8a4f3c1dd7b03db901e679dc9d67549c99a03634c8621cef21036922595f8b28fdcf8dd0cec4b14311cb9786f152564eee9ef90f484985855080e8003b816ccb6ab1c085799a7703943a83dd6f40748de3d96bfa6c17226cc78cba5ea50c276864ac737c6e99038279fd0de904a9e4ae2c7bbcc063cc0a4656a57ef046c955fa9b9b1a8241c48e8bbe5a97718bbd41e0578d7a8eecb97e5a394dbc89b4238d35f86e930ebaa5a4c124058baf87752cf6ce5d9d41664539bf2098d708b2d8c22ab15c3cc50bad558cd0cbff1cec8242481289113ec8a4846bf65f75a07eb40a96c06a108"
 *  }
 *
 */
//Route::get('/redirect/password', function () {$http = new GuzzleHttp\Client;
//    $client = new GuzzleHttp\Client;
//    $response = $client->post('http://runbuy.admin.cunwo.net/oauth/token', [
//        'form_params' => [
//            'grant_type' => 'password',
//            'client_id' => 2,// 'client-id',
//            'client_secret' => '0YyuRCVmLUhvOy2BHgGAaXDHPSlopXdYv2ff7yMi',// 'client-secret',
//            'username' => 'dsfsdfsddasd@qq.com',// 'taylor@laravel.com',
//            'password' => '123456',// 'my-password',
//            // 可以通过请求 scope 参数 * 来授权应用程序支持的所有范围的令牌。如果你的请求中包含 scope 为 * 的参数，
//            // 令牌实例上的 can 方法会始终返回 true。这种作用域的授权只能分配给使用 password 授权时发出的令牌：
//            'scope' => '',
//        ],
//    ]);
//    return json_decode((string) $response->getBody(), true);
//});

// 隐式授权令牌
// 隐式授权类似于授权码授权，但是它只将令牌返回给客户端而不交换授权码。
//  这种授权最常用于无法安全存储客户端凭据的 JavaScript 或移动应用程序。
// 通过调用 AuthServiceProvider 中的 enableImplicitGrant 方法来启用这种授权：
/**
 * 有个问题：为什么返回的地址参数是#而不是?号
 *  http://runbuy.admin.cunwo.net/api/auth/callback/implicit#access_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjIyMmZiY2E4ZGNmOGQ3NzAwNTMxOGI2YjlkYjM3YWIzZmZlODg5YjFiZjQwZTYzYTU3ZTQ3ZjFkZjlmZGMyNWQwNzU2MjdlYzhmMDM5NWE3In0.eyJhdWQiOiI1IiwianRpIjoiMjIyZmJjYThkY2Y4ZDc3MDA1MzE4YjZiOWRiMzdhYjNmZmU4ODliMWJmNDBlNjNhNTdlNDdmMWRmOWZkYzI1ZDA3NTYyN2VjOGYwMzk1YTciLCJpYXQiOjE1NzE3MDUxMjcsIm5iZiI6MTU3MTcwNTEyNywiZXhwIjoxNTczMDAxMTI3LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.dNXslW8QHo7iQTOWTIQ3h0RXpL7PqUHdyE1QAzr-0osDCuoNQbHhWq2pcXJxCwJnO1YeNlsiBswkBkBb5QcR9UYJNL6ZmnbVBsJxxRBOY2TdPVd26bxDdHN0g3tBLEt4OB5uNT20fDBdBQPU9nAF3hEqBhEpN3kJiKmR4E0QsNKn65nobXKhhjTg4cuuopT2ZK7J1VNQQryIb4IOgDUNIGR-qb_gYqoi6J5son9wtAHmI72nz1zG7gitdt_yV1VYdkGx3fSsfL3qt0HDaflhBdi4BEL-KSZgmy3rgHO5TNx5idszDoHzpwzxuFEIhzUZoMpg5Nj-vjiqFYmZ6XUOPEhBq5V77n1h4Hvpj9xXNH3ckO7VsAy6wsHol0hjDWER-WeOmyakT2mADYgtixcinmW7ZYJEcHhRAwyBTA-rY8iVz013NHsVIJbocntdNdpvvuQc3Crqu1CnKorBYPZjsfI15vISE8UDRYC6z7MNYPV5XrJ9QvI_PFnOWL6jDnIieAjT_wB-BbBNpFQytOwjRQL5wIJXMHcon-SPqkPm41Dlt2nHjepPtsViHZXwyJpHL3ZvxwICYmpFE4Vtr5wRapQeeirnwYPOoBcbMTurSS9J3WNqhuEkkxCX90q29tI6R_u38eFz8LFmedZoI4LrZTo7mZCysZroonuy6LAcLic&token_type=Bearer&expires_in=1296000
 *
 */
//Route::get('/redirect/implicit', function () {
//    $query = http_build_query([
//        'client_id' => 5,// 'client-id',
//        'redirect_uri' => 'http://runbuy.admin.cunwo.net/api/auth/callback/implicit',//'http://example.com/callback',
//        'response_type' => 'token',
//        'scope' => '',
//    ]);
//
//    return redirect('http://runbuy.admin.cunwo.net/oauth/authorize?'.$query);
//});


// 客户端凭据授权令牌
//客户端凭据授权适用于机器到机器的认证。例如，你可以在通过 API 执行维护任务中使用此授权。
//要使用这种授权，你首先需要在 app/Http/Kernel.php 的 $routeMiddleware 变量中添加新的中间件：
// 客户端通过获得的下面的token来请求服务端的这个接口
/*
 *
 *   {
 *        "token_type": "Bearer",
 *        "expires_in": 1296000,
 *        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImZlMGMxMTkzMTNhZDk0NDc0NGM0NjQ4MjBlMzgyMjM3M2NkMGFjMTAxYjUzZjg3YjU0NDczMGM2Y2QyMDQyY2U4ZjkxYTM1ZTcyNGMyM2MwIn0.eyJhdWQiOiI1IiwianRpIjoiZmUwYzExOTMxM2FkOTQ0NzQ0YzQ2NDgyMGUzODIyMzczY2QwYWMxMDFiNTNmODdiNTQ0NzMwYzZjZDIwNDJjZThmOTFhMzVlNzI0YzIzYzAiLCJpYXQiOjE1NzE3MDcxMTMsIm5iZiI6MTU3MTcwNzExMywiZXhwIjoxNTczMDAzMTEzLCJzdWIiOiIiLCJzY29wZXMiOltdfQ.pz_dIe_gsgRYOsEpTKizIGI41rXAobm60_SHWgvy1SK28-0gypNV0PFyBjLM8sVJYez8cAZd2gAGrFmAaeB8Z9q64tiWj2I-FTKx5yggNohzMA0T9wu9P-m0YDX4NVCz1ZWAGrSAlPH4Qxtjrof6N-GibL-APXinXE-cGv6P-SW-yYeMlqw7EYkWBglJ28cTH4ZQ8fp7aBm7FvILdKetVpt2vBqLsl-UNckDqob3nie6skdHVcZUZoXrRN_fzYGP0sxrK_Y6AjnkcwidqHZWyjBLgqdIU_ErK_OVKGW4yDmmEo17mvxj2uF6nzbIQwDK78Mjq9rgwWeb3K53MrkRKYgFXRT7qBgHl3S4L8i4bNfupOMCQeAU3NrB1iE3Ko2kjX9ZAS93cO0mLihXBA0XhpIZnYexQAMzEPdMeeFBAyLH6VeoQVePRDXRwj0BqzLcwznDr0DeQUzD1qQ8AHCSUhlRZTGWjUJxPd8SEN4xUb40LfnEKbEd6PZJy5cwM7tLMs7xcFrfyJ1e-hpOcFK0wQdXD1xUU4IKopTZypMg8GE5fJ2-QW3HwLPM3hEJ_Hi1VMSfkqA9ksiudmNcYKXqIYrtTa58XFbIfxgMdafV7Z1zXNzWNZtiIGL3LPm-Ccy7-bUz3oisvX-LMFPWzF4SYqx1oD8MFjNPxQYg_bSVPS8"
 *    }
 *
 */
//Route::get('/user/client_credentials', function(Request $request) {
//   echo 'aaaa';
//})->middleware('client');


// Laravel+passport 实现API认证 --未验证
// https://blog.csdn.net/hhhzua/article/details/80170447
//Route::group([
//    'prefix'=>'/v1',
//    'middleware' => ['api'],
//], function () {
//    Route::post('user/login','Api\LoginController@login');
//    Route::post('user/register','Api\LoginController@register');
//});
//Route::group([
//    'prefix'=>'/v1',
//    'middleware' => ['auth:api'],
//    //指定需要传入有效访问令牌的 auth:api 中间件
//], function () {
//    Route::get('user/logout','Api\LoginController@logout');
//    Route::get('user/info','User\IndexController@getInfo');//显示个人信息
//});


