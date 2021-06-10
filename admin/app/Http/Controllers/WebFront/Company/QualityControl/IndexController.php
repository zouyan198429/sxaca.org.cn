<?php

namespace App\Http\Controllers\WebFront\Company\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPIIndustryBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
// use App\Business\Controller\API\RunBuy\CTAPITablesBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\Staff;
use App\Services\Captcha\CaptchaCode;
use App\Services\File\DownFile;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IndexController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    public function test(Request $request){
//        $extParams['sqlParams']['whereIn']['id'] = 123;
//        pr($extParams);
//            $this->InitParams($request);
//        $reDataArr = $this->reDataArr;
//        //pr($this->getUserInfo($request));
//        //die;
//        pr($this->user_id);
//        echo '1111';
        return view('company.QualityControl.test', []);
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
//
//            // 如果用户没有完善信息，则跳转到完善信息页
////        $userInfo = $this->user_info;
////        if($userInfo['is_perfect'] == 1 ){
//            // 保存注册信息
////            $preKey = CommonRequest::get($request, 'preKey');// 0 小程序 1后台[默认]
////            if(!is_numeric($preKey)) $preKey = 1;
////            $redisKey = $this->setUserInfo($userInfo['id'], $preKey, 'reg');
////            $userInfo['redisKey'] = $redisKey;
////            $this->delUserInfo();
////            return redirect(config('public.webSite.reg.domain') .'web/perfect_company');
////        }
//            return view('company.QualityControl.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'company.QualityControl.index', true
            , 'doListPage', [], function (&$reDataArr) use ($request){

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
            return view('company.QualityControl.login', $reDataArr);

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
            return view('company.QualityControl.admin.password', $reDataArr);

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
            return view('company.QualityControl.admin.info', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 17179869184, 1,'web.QualityControl.admin.info', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//            });
    }

    /**
     * 修改企业基本信息
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function basic(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $id = $this->user_id;
            $info = $this->user_info;
//            $info = [
//                'id'=>$id,
//                //   'department_id' => 0,
//            ];
            $operate = "添加";

//            if ($id > 0) { // 获得详情数据
                $operate = "修改";
                $handleKeyArr = [];
                $handleKeyConfigArr = [];
               array_push($handleKeyArr, 'siteResources');// array_merge($handleKeyArr, ['industry', 'siteResources']); ;//
               array_push($handleKeyConfigArr, 'certificate_info');

//                $extParams = [
//                    // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                    'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
//                ];
//                $info = CTAPIStaffBusiness::getInfoData($request, $this, $id, [], '', $extParams);
                // if(!empty($handleKeyArr)) CTAPIStaffBusiness::handleData($request, $this, $info, $handleKeyArr);
                if(!empty($handleKeyConfigArr)){
                    $relationFormatConfigs = CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []);
                    CTAPIStaffBusiness::formatRelationList( $request, $this, $info, $relationFormatConfigs);
                }

//            }
            // $reDataArr = array_merge($reDataArr, $resultDatas);
            $reDataArr['info'] = $info;
            $reDataArr['operate'] = $operate;
            // 获得城市KV值--企业和用户有城市
            // 获得城市KV值
            $reDataArr['citys_kv'] = CTAPICitysBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'city_name']);
            $reDataArr['defaultCity'] = $info['city_id'] ?? -1;// 默认

            // 是否完善资料1待完善2已完善
            $reDataArr['isPerfect'] =  Staff::$isPerfectArr;
            $reDataArr['defaultIsPerfect'] = $info['is_perfect'] ?? -1;// 列表页默认状态

            // 只有企业有
            // 所属行业
            $reDataArr['industry_kv'] = CTAPIIndustryBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'industry_name']);
            $reDataArr['defaultIndustry'] = $info['company_industry_id'] ?? -1;// 默认

            // 企业--企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
            $reDataArr['companyProp'] = Staff::$companyPropArr;
            $reDataArr['defaultCompanyProp'] = $info['company_prop'] ?? -1;// 列表页默认状态

            // 企业--单位人数1、1-20、2、20-100、3、100-500、4、500以上
            $reDataArr['companyPeoples'] = Staff::$companyPeoplesNumArr;
            $reDataArr['defaultCompanyPeoples'] = $info['company_peoples_num'] ?? -1;// 列表页默认状态

            // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
            $reDataArr['companyGrade'] =  Staff::$companyGradeArr;
            $company_grade = ($id > 0) ? $info['company_grade'] : CommonRequest::get($request, 'company_grade');
            if(strlen($company_grade) <= 0 ) $company_grade = -1;
            $reDataArr['defaultCompanyGrade'] = $company_grade;// $info['company_grade'] ?? -1;// 列表页默认状态


            return view('company.QualityControl.admin.basic', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }


    /**
     * ajax保存数据
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_basic_save(Request $request)
    {
        $this->InitParams($request);
        $user_info = $this->user_info;
        $id = $this->user_id;// CommonRequest::getInt($request, 'id');
        $company_name = CommonRequest::get($request, 'company_name');
        $company_credit_code = CommonRequest::get($request, 'company_credit_code');
        $company_is_legal_persion = CommonRequest::getInt($request, 'company_is_legal_persion');
        if($company_is_legal_persion != 1) $company_is_legal_persion = 2;
        $company_legal_credit_code = CommonRequest::get($request, 'company_legal_credit_code');
        $company_legal_name = CommonRequest::get($request, 'company_legal_name');
        $city_id = CommonRequest::getInt($request, 'city_id');
        $company_type = CommonRequest::getInt($request, 'company_type');
        $company_prop = CommonRequest::get($request, 'company_prop');
        $addr = CommonRequest::get($request, 'addr');
        $zip_code = CommonRequest::get($request, 'zip_code');
        $fax = CommonRequest::get($request, 'fax');
        $email = CommonRequest::get($request, 'email');
        $company_legal = CommonRequest::get($request, 'company_legal');
        $company_peoples_num = CommonRequest::getInt($request, 'company_peoples_num');
        $company_industry_id = CommonRequest::getInt($request, 'company_industry_id');
        $company_certificate_no = CommonRequest::get($request, 'company_certificate_no');
        $ratify_date = CommonRequest::get($request, 'ratify_date');
        $valid_date = CommonRequest::get($request, 'valid_date');
        // $laboratory_addr = CommonRequest::get($request, 'laboratory_addr');
        // 判断开始结束日期
        Tool::judgeBeginEndDate($ratify_date, $valid_date, 1 + 2 + 256 + 512, 1, date('Y-m-d'), '有效起止日期');

        $company_contact_name = CommonRequest::get($request, 'company_contact_name');
        $company_contact_mobile = CommonRequest::get($request, 'company_contact_mobile');
        $company_contact_tel = CommonRequest::get($request, 'company_contact_tel');
//        $is_perfect = CommonRequest::getInt($request, 'is_perfect');
        // 可能会用的参数
        $admin_username = CommonRequest::get($request, 'admin_username');
//        $admin_password = CommonRequest::get($request, 'admin_password');
//        $sure_password = CommonRequest::get($request, 'sure_password');
        $userInfo = [];

        // 图片资源
        $resource_id = CommonRequest::get($request, 'resource_id');
        // 如果是字符，则转为数组
        if(is_string($resource_id) || is_numeric($resource_id)){
            if(strlen(trim($resource_id)) > 0){
                $resource_id = explode(',' ,$resource_id);
            }
        }
        if(!is_array($resource_id)) $resource_id = [];

        // 再转为字符串
        $resource_ids = implode(',', $resource_id);
        if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';

        $saveData = [
            'admin_type' => $user_info['admin_type'],// static::$ADMIN_TYPE,
//            'is_perfect' => $is_perfect,
            'company_name' => $company_name,
            'company_credit_code' => $company_credit_code,
            'company_is_legal_persion' => $company_is_legal_persion,
            'company_legal_credit_code' => $company_legal_credit_code,
            'company_legal_name' => $company_legal_name,
            'city_id' => $city_id,
            'company_type' => $company_type,
            'company_prop' => $company_prop,
            'addr' => $addr,
            'zip_code' => $zip_code,
            'fax' => $fax,
            'email' => $email,
            'company_legal' => $company_legal,
            'company_peoples_num' => $company_peoples_num,
            'company_industry_id' => $company_industry_id,
            'company_certificate_no' => $company_certificate_no,
            'ratify_date' => $ratify_date,
            'valid_date' => $valid_date,
           // 'laboratory_addr' => $laboratory_addr,
            'company_contact_name' => $company_contact_name,
            'company_contact_mobile' => $company_contact_mobile,
            'company_contact_tel' => $company_contact_tel,
            'admin_username' => $admin_username,
            // 'resource_id' => $resource_id[0] ?? 0,// 第一个图片资源的id
            'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
            'resourceIds' => $resource_id,// 此下标为图片资源关系
        ];
//        if($admin_password != '' || $sure_password != ''){
//            if ($admin_password != $sure_password){
//                return ajaxDataArr(0, null, '密码和确定密码不一致！');
//            }
//            $saveData['admin_password'] = $admin_password;
//        }
        // 超级帐户 不可 冻结
//        if(isset($userInfo['issuper']) && $userInfo['issuper'] != 1){
//            $saveData['account_status'] = $account_status;
//        }

        if($id <= 0) {// 新加;要加入的特别字段
            $addNewData = [
                // 'account_password' => $account_password,
//                'is_perfect' => 1,
                'company_grade' => 1,// 新加的会员默认等级为非会员单位
                'issuper' => 2,
//                'company_type' => 0,// 企业类型1检测机构、2生产企业
//                'company_prop' => 0,// 企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
//                'company_peoples_num' => 0,// 单位人数1、1-20、2、20-100、3、100-500、4、500以上
                'open_status' => 2,// 审核状态1待审核2审核通过4审核不通过
                'account_status' => 1// 状态 1正常 2冻结
            ];
            $saveData = array_merge($saveData, $addNewData);
        }
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
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
     *     path="/api/company/ajax_login",
     *     tags={"企业后台-帐号注册登录"},
     *     summary="帐号密码登录",
     *     description="通过帐号、密码、图形验证码进行登录",
     *     operationId="companyIndexAjax_login",
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
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_login(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        return CTAPIStaffBusiness::loginCaptchaCode($request, $this,2, 1, 1);
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
        return CTAPIStaffBusiness::loginCaptchaCode($request, $this,2, 2, 2);
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
        // return redirect('company/login');
        return redirect('web/login');
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
//        $qrcode_url = $info['qrcode_url'] ?? '';//  http://runbuy.admin.cunwo.net/resource/company/1/images/qrcode/tables/1.png
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
//    public function down_drive(Request $request)
//    {
////        $this->InitParams($request);
//        // $this->source = 2;
////        $reDataArr = $this->reDataArr;
//        // 下载二维码文件
//        $publicPath = Tool::getPath('public');
//        $fileName = '/CLodopPrint_Setup_for_Win32NT.exe';
//        $res = DownFile::downFilePath(2, $publicPath . '/' . $fileName);
//        $save_file_name = CommonRequest::get($request, 'save_file_name');// 下载时保存的文件名 [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
//        if(is_string($res)) echo $res;
//    }

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
