<?php

namespace App\Http\Controllers\Expert\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICertificateScheduleBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
// use App\Business\Controller\API\RunBuy\CTAPITablesBusiness;
use App\Business\DB\QualityControl\AbilityCodeDBBusiness;
use App\Business\DB\QualityControl\AbilityJoinItemsDBBusiness;
use App\Business\DB\QualityControl\AbilityJoinItemsResultsDBBusiness;
use App\Business\DB\QualityControl\CertificateScheduleDBBusiness;
use App\Business\DB\QualityControl\StaffDBBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\Staff;
use App\Services\Captcha\CaptchaCode;
use App\Services\DB\CommonDB;
use App\Services\File\DownFile;
use App\Services\Request\CommonRequest;
use App\Services\SessionCustom\SessionCustom;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IndexController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    public function test(Request $request){

        $dateTime =  date('Y-m-d H:i:s');
        $aaa = Tool::addMinusDate($dateTime, ['+30 day'], 'Y-m-d H:i:s', 1, '时间');
        pr($aaa);

        $aaa = SessionCustom::set('test', '1112', 0);
        pr($aaa);
        $bbb = SessionCustom::get('loginKeyadmin',true);

        pr($bbb);
        $redisKey = 'PHPREDIS_SESSION:' . session_id();
        pr($redisKey);
        $currentTime = date('Y-m-d H:i:s');
        $endTime = '2020-06-02 15:48:49';
        $endCarbon = carbon::parse ($endTime); // 格式化一个时间日期字符串为 carbon 对象
        // 减当前时间 ; > 0 没有过期 = 0 马上过期  < 0 过期
        $diffSeconds = (new Carbon)->diffInSeconds ($endCarbon, false); // $int 为正负数
        // $diffSeconds = strtotime($currentTime) - strtotime($endTime);
        dd($diffSeconds);
//        CTAPICertificateScheduleBusiness::mergeRequest($request, $this, [
//            'field' => 'method_name',
//            'keyword' => '标',
//        ]);
//        $queryParams = [
//            'select' => [
//                'company_id'
//                //,'position_name','sort_num'
//                //,'operate_staff_id','operate_staff_id_history'
//                // ,'created_at'
//            ],
//            'distinct'=> 'company_id',
//        ];
//        $aa = CTAPICertificateScheduleBusiness::getList($request, $this, 2 + 4, $queryParams, [], []);
//        pr($aa);
//        CertificateScheduleDBBusiness::test();
//        pr(123);
        // $ability_code = AbilityCodeDBBusiness::getAbilityCode();// 单号 生成  2020NLYZ0001
        //pr($ability_code);
        $currentNow = Carbon::now()->toDateTimeString();
        $aa = date('Y-m-d 23:59:59');
        $duration_minute = 13;
        $submit_off_time = Tool::addMinusDate(date('Y-m-d 23:59:59'), ['+' . $duration_minute . ' day'], 'Y-m-d H:i:s', 1, '时间');;
        echo $submit_off_time;
//        AbilityJoinItemsDBBusiness::initReslut();
//        die();
//        $bbb = '555';
//        $aaa = CommonDB::doTransactionFun(function () use(&$bbb){
//            $bbb .= '666';
//            return 'bcd';
//        });
//        echo $bbb . '<br/>';
//
//        pr($aaa);

//        $operate_type = 8;
//        $page_size = 2;
//        $fieldValParams = ['issuper' => 1];
//        $fieldEmptyQuery = false;
//        $relations = '';
//        $extParams = [];
//        $data = StaffDBBusiness::getDBFVFormatList($operate_type, $page_size, $fieldValParams, $fieldEmptyQuery, $relations, $extParams);
//
//        if(is_object($data)) vd($data);
//        if(is_array($data)) pr($data);
         phpinfo();
        die;
        $extParams['sqlParams']['whereIn']['id'] = 123;
        pr($extParams);
            $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        //pr($this->getUserInfo($request));
        //die;
        pr($this->user_id);
        echo '1111';
    }

    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('expert.QualityControl.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'expert.QualityControl.index', true
            , 'doListPage', [], function (&$reDataArr) use ($request){

            });
    }

    /**
     * ajax获得模型表的缓存时间；没有缓存时间-则返回当前时间
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_getTableUpdateTime(Request $request){
        return $this->exeDoPublicFun($request, 0, 4,'', true, '', [], function (&$reDataArr) use ($request){
            $module_name = CommonRequest::get($request, 'module_name');// QualityControl\CTAPIStaff
            if(empty($module_name)) throws('参数【module_name】不能为空！');

            $objClass = 'App\\Business\\Controller\API\\' . $module_name  . 'Business';// 'App\Business\Controller\API\QualityControl\CTAPIStaffBusiness';
            if (! class_exists($objClass )) {
                throws('参数[module_name]类不存在！');
            }
            // 空或 string(29) "2020-09-04 15:00:03!!!9840900"  [true, 4]
            $tableUpdateTime = $objClass::exeMethodCT($request, $this, '', 'getTableUpdateTimeCache', [], 1, 1);
            if(!empty($tableUpdateTime)) list($tableUpdateTime, $cacheMsecint) = array_values(Tool::getTimeMsec($tableUpdateTime));
            if(empty($tableUpdateTime)) $tableUpdateTime = date('Y-m-d H:i:s');
            return ajaxDataArr(1, $tableUpdateTime, '');
        });
    }

    /**
     * api生成验证码图片信息
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_captcha(Request $request)
//    {
//        $captchaParams = CaptchaCode::createCodeAPI(__CLASS__ . $request->ip(),'default');// app('captcha')->create('default', true);
//
//        return ajaxDataArr(1, $captchaParams, '');
//    }

    /**
     * api验证验证码信息是否正确
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_captcha_verify(Request $request)
//    {
//        $captcha_code = CommonRequest::get($request, 'captcha_code');
//        $captcha_key = CommonRequest::get($request, 'captcha_key');
////        if(!captcha_api_check($captcha_code, $captcha_key)) {
////            Cache::forget($captcha_key);
////            return ajaxDataArr(0, null, '验证码错误');
////        }
////        Cache::forget($captcha_key);
//        CaptchaCode::captchaCheckAPI($captcha_code, $captcha_key, false, 1);
//        return ajaxDataArr(1, ['data' => 1], '验证码正确');
//    }

    /**
     * 登陆
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function login(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            return view('expert.QualityControl.login', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 修改密码
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function password(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $user_info = $this->user_info;
            $reDataArr = array_merge($reDataArr, $user_info);
            return view('expert.QualityControl.admin.password', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 显示
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function info(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $user_info = $this->user_info;

            $reDataArr['adminType'] =  Staff::$adminTypeArr;
            $reDataArr['defaultAdminType'] = $user_info['admin_type'] ?? 0;// 列表页默认状态
            $reDataArr = array_merge($reDataArr, $user_info);
            return view('expert.QualityControl.admin.info', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 17179869184, 1,'web.QualityControl.admin.info', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//            });
    }

    /**
     * err404
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function err404(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            return view('404', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * @OA\Post(
     *     path="/api/expert/ajax_login",
     *     tags={"大后台-帐号注册登录"},
     *     summary="帐号密码登录",
     *     description="通过帐号、密码、图形验证码进行登录",
     *     operationId="expertIndexAjax_login",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_admin_username"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_admin_password"),
     *     @OA\Parameter(ref="#/components/parameters/Parameter_Object_captcha_captcha_key"),
     *     @OA\Parameter(ref="#/components/parameters/Parameter_Object_captcha_captcha_code"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_staff_login"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_multi_brands"}
     */
    /**
     * ajax保存数据
     *
     * @param Request $request
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_login(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        return CTAPIStaffBusiness::loginCaptchaCode($request, $this,8, 1, 1);
    }

    /**
     * ajax保存数据--手机验证码登录
     *
     * @param Request $request
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_login_sms(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        return CTAPIStaffBusiness::loginCaptchaCode($request, $this,8, 2, 2);
    }

    /**
     * 注销
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function logout(Request $request)
    {
        // $this->InitParams($request);
        CTAPIStaffBusiness::loginOut($request, $this);
        $reDataArr = $this->reDataArr;
        return redirect('expert/login');
    }

    /**
     * ajax修改密码
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_password_save(Request $request)
    {
        $this->InitParams($request);
        return CTAPIStaffBusiness::modifyPassWord($request, $this);
    }

    /**
     * ajax 修改设置
     *
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_info_save(Request $request)
    {
        $this->InitParams($request);
        $user_info = $this->user_info;

        $id = $this->user_id;
        $company_id = $this->company_id;
        $admin_username = CommonRequest::get($request, 'admin_username');
        $mobile = CommonRequest::get($request, 'mobile');
        $real_name = CommonRequest::get($request, 'real_name');
        $sex = CommonRequest::getInt($request, 'sex');
        $tel = CommonRequest::get($request, 'tel');
        $qq_number = CommonRequest::get($request, 'qq_number');

        $saveData = [
            'admin_type' => $user_info['admin_type'],
            'admin_username' => $admin_username,
            'mobile' => $mobile,
            'real_name' => $real_name,
            'sex' => $sex,
           // 'gender' => $sex,
            'tel' => $tel,
            'qq_number' => $qq_number,
        ];
        $extParams = [
            // 'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * 下载二维码
     *
     * @param Request $request
     * @param int $id id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function down(Request $request,$id = 0)
//    {
//        $this->InitParams($request);
//        // $this->source = 2;
//        $reDataArr = $this->reDataArr;
//        $relations = '';//  CTAPITablesBusiness::getExtendParamsConfig($request, $this, 'list_page_admin', 'relationsArr');
//
//        $info = CTAPITablesBusiness::getInfoData($request, $this, $id, ['id','table_name','has_qrcode','qrcode_url'], $relations, []);
//        $has_qrcode = $info['has_qrcode'] ?? 1;
//        $qrcode_url = $info['qrcode_url'] ?? '';//  http://runbuy.expert.cunwo.net/resource/company/1/images/qrcode/tables/1.png
//        $qrcode_url_old = $info['qrcode_url_old'] ?? '';// /resource/company/1/images/qrcode/tables/1.png
//        if($has_qrcode != 2 ) die('记录不存在或未生成二维码');
//        // 下载二维码文件
//        $publicPath = Tool::getPath('public');
//        $save_file_name = CommonRequest::get($request, 'save_file_name');// 下载时保存的文件名 [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
//        $res = DownFile::downFilePath(2, $publicPath . $qrcode_url_old, 1024, $save_file_name);
//        if(is_string($res)) echo $res;
//    }

    /**
     * 下载网页打印机驱动
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function down_drive(Request $request)
    {
//        $this->InitParams($request);
        // $this->source = 2;
//        $reDataArr = $this->reDataArr;
        // 下载二维码文件
        $publicPath = Tool::getPath('public');
        $fileName = '/CLodopPrint_Setup_for_Win32NT.exe';
        $save_file_name = CommonRequest::get($request, 'save_file_name');// 下载时保存的文件名 [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
        $res = DownFile::downFilePath(2, $publicPath . '/' . $fileName, 1024, $save_file_name);
        if(is_string($res)) echo $res;
    }

    // **************公用方法**********************开始*******************************

    /**
     * 公用列表页 --- 可以重写此方法--需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doListPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 1->1 首页；2->2 列表页； 12->2048 弹窗选择页面；
        // $user_info = $this->user_info;
        // $id = $extendParams['params']['id'];

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

        $reDataArr['hidden_option'] = $hiddenOption;
    }

    /**
     * 公用详情页 --- 可以重写此方法-需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doInfoPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 5->16 添加页； 7->64 编辑页；8->128 ajax详情； 35-> 17179869184 详情页
//        // $user_info = $this->user_info;
//        $id = $extendParams['params']['id'] ?? 0;
//
////        // 拥有者类型1平台2企业4个人
////        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
////        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态
//        $info = [
//            'id'=>$id,
//            //   'department_id' => 0,
//        ];
//        $operate = "添加";
//
//        if ($id > 0) { // 获得详情数据
//            $operate = "修改";
//            $info = CTAPIRrrDdddBusiness::getInfoData($request, $this, $id, [], '', []);
//        }
//        // $reDataArr = array_merge($reDataArr, $resultDatas);
//        $reDataArr['info'] = $info;
//        $reDataArr['operate'] = $operate;

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

    /**
     * 下载文件
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function down_file(Request $request)
    {
//        $this->InitParams($request);
        // $this->source = 2;
//        $reDataArr = $this->reDataArr;
        // 下载二维码文件
        $publicPath = Tool::getPath('public');
        $fileName = CommonRequest::get($request, 'resource_url');// '/CLodopPrint_Setup_for_Win32NT.exe';
        $save_file_name = CommonRequest::get($request, 'save_file_name');// 下载时保存的文件名 [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
        $res = DownFile::downFilePath(2, $publicPath . $fileName, 1024, $save_file_name);
        if(is_string($res)) echo $res;
    }
}
