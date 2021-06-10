<?php

namespace App\Http\Controllers\WebFront\Web\QualityControl;

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

class HomeController extends BasicRegController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    public function test(Request $request){
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
     * 登录页
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

            // $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $this->setHostType($reDataArr);
            return view('web.QualityControl.home.login', $reDataArr);
        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 登录页 登录--为登录测试  补充资料用
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function login_company(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            // $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            return view('web.QualityControl.login_company', $reDataArr);
        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 登录页 登录--为登录测试  补充资料用
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function login_user(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            // $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            return view('web.QualityControl.login_user', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * ajax保存数据 登陆----为登录测试  补充资料用
     *
     * @param Request $request
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_login_company(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        return CTAPIStaffBusiness::loginCaptchaCode($request, $this,2, 1, 1);
    }

    /**
     * ajax保存数据 登陆----为登录测试  补充资料用
     *
     * @param Request $request
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_login_user(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        return CTAPIStaffBusiness::loginCaptchaCode($request, $this,4, 1, 1);
    }

    /**
     * 注册页 -- 注册服务协议
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function reg_agree(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            // $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $this->setHostType($reDataArr);
            return view('web.QualityControl.home.reg_agree', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 注册页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function reg(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            // $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $this->setHostType($reDataArr);
            return view('web.QualityControl.home.reg', $reDataArr);
        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 完善企业资料页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function perfect_company(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $info = $this->user_info;
            $user_type = $info['admin_type'] ?? 0;
            // 非企业用户不可进行此操作！
            if(empty($info) || $user_type != 2){
//            throws('您还没有注册！', $this->getNotLoginErrCode());
                return redirect('web/login');
            }
            $handleKeyArr = [];
            $handleKeyConfigArr = [];
            if($user_type == 2){
                array_push($handleKeyArr, 'siteResources');// array_merge($handleKeyArr, ['industry', 'siteResources']); ;//
                array_push($handleKeyConfigArr, 'certificate_info');
            }

            // if(!empty($handleKeyArr)) CTAPIStaffBusiness::handleData($request, $this, $info, $handleKeyArr);
            if(!empty($handleKeyConfigArr)){
                $relationFormatConfigs = CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []);
                CTAPIStaffBusiness::formatRelationList( $request, $this, $info, $relationFormatConfigs);
            }
            $reDataArr['info'] = $info;
            // 获得城市KV值
            $reDataArr['citys_kv'] = CTAPICitysBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'city_name']);
            $reDataArr['defaultCity'] = $info['city_id'] ?? -1;// 默认

            // 所属行业
            $reDataArr['industry_kv'] = CTAPIIndustryBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'industry_name']);
            $reDataArr['defaultIndustry'] = $info['company_industry_id'] ?? -1;// 默认

            // 企业--企业类型1检测机构、2生产企业
            $reDataArr['companyType'] =  Staff::$companyTypeArr;
            $reDataArr['defaultCompanyType'] = $info['company_type'] ?? -1;// 列表页默认状态

            // 企业--企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
            $reDataArr['companyProp'] =  Staff::$companyPropArr;
            $reDataArr['defaultCompanyProp'] = $info['company_prop'] ?? -1;// 列表页默认状态

            // 企业--单位人数1、1-20、2、20-100、3、100-500、4、500以上
            $reDataArr['companyPeoples'] =  Staff::$companyPeoplesNumArr;
            $reDataArr['defaultCompanyPeoples'] = $info['company_peoples_num'] ?? -1;// 列表页默认状态
            $this->setHostType($reDataArr);
            return view('web.QualityControl.home.perfect_company', $reDataArr);
        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 选择-弹窗 选择所属企业
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function select_company(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            // 获得城市KV值--企业和用户有城市
            $reDataArr['citys_kv'] = CTAPICitysBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'city_name']);
            $reDataArr['defaultCity'] = $info['city_id'] ?? -1;// 默认

            // 所属行业--只有企业有
            $reDataArr['industry_kv'] = CTAPIIndustryBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'industry_name']);
            $reDataArr['defaultIndustry'] = $info['company_industry_id'] ?? -1;// 默认

            // 拥有者类型1平台2企业4个人
            $reDataArr['adminType'] =  Staff::$adminTypeArr;
            $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

            // 是否完善资料1待完善2已完善
            $reDataArr['isPerfect'] =  Staff::$isPerfectArr;
            $reDataArr['defaultIsPerfect'] = -1;// 列表页默认状态

            // 是否超级帐户2否1是
            $reDataArr['issuper'] =  Staff::$issuperArr;
            $reDataArr['defaultIssuper'] = -1;// 列表页默认状态

            // 审核状态1待审核2审核通过4审核不通过
            $reDataArr['openStatus'] =  Staff::$openStatusArr;
            $reDataArr['defaultOpenStatus'] = -1;// 列表页默认状态

            // 状态 1正常 2冻结
            $reDataArr['accountStatus'] =  Staff::$accountStatusArr;
            $reDataArr['defaultAccountStatus'] = -1;// 列表页默认状态

            // 性别0未知1男2女
            $reDataArr['sex'] =  Staff::$sexArr;
            $reDataArr['defaultSex'] = -1;// 列表页默认状态

            // 企业--是否独立法人1独立法人 2非独立法人
            $reDataArr['companyIsLegalPersion'] =  Staff::$companyIsLegalPersionArr;
            $reDataArr['defaultCompanyIsLegalPersion'] = -1;// 列表页默认状态

            // 企业--企业类型1检测机构、2生产企业
            $reDataArr['companyType'] =  Staff::$companyTypeArr;
            $reDataArr['defaultCompanyType'] = -1;// 列表页默认状态

            // 企业--企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
            $reDataArr['companyProp'] =  Staff::$companyPropArr;
            $reDataArr['defaultCompanyProp'] = -1;// 列表页默认状态

            // 企业--单位人数1、1-20、2、20-100、3、100-500、4、500以上
            $reDataArr['companyPeoples'] =  Staff::$companyPeoplesNumArr;
            $reDataArr['defaultCompanyPeoples'] = -1;// 列表页默认状态

            // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
            $reDataArr['companyGrade'] =  Staff::$companyGradeArr;
            $reDataArr['defaultCompanyGrade'] = -1;// 列表页默认状态

            $this->setHostType($reDataArr);
            return view('web.QualityControl.home.select_company', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * ajax获得列表数据--获得企业列表信息
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function company_ajax_alist(Request $request){
//        $this->InitParams($request);
//        // $this->company_id = 1;
//        $mergeParams = [
//            'admin_type' => 2,// 类型1平台2企业4个人
//        ];
//        CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);
//
//        $relations = [];//  ['siteResources']
//        $handleKeyArr = [];
//        $handleKeyConfigArr = [];
//        array_push($handleKeyArr, 'industry');// array_merge($handleKeyArr, ['industry', 'siteResources']); ;//
//        array_push($handleKeyConfigArr, 'industry_info');
//        $handleKeyArr = array_merge($handleKeyArr, ['extend', 'city']);
//        $handleKeyConfigArr = array_merge($handleKeyConfigArr, ['extend_info', 'city_info']);
//        $extParams = [
//            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
//        ];
//
//        return  CTAPIStaffBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            // $this->company_id = 1;
            $mergeParams = [
                'admin_type' => 2,// 类型1平台2企业4个人
            ];
            CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);

            $relations = [];//  ['siteResources']
            $handleKeyArr = [];
            $handleKeyConfigArr = [];
            array_push($handleKeyArr, 'industry');// array_merge($handleKeyArr, ['industry', 'siteResources']); ;//
            array_push($handleKeyConfigArr, 'industry_info');
            $handleKeyArr = array_merge($handleKeyArr, ['extend', 'city']);
            $handleKeyConfigArr = array_merge($handleKeyConfigArr, ['extend_info', 'city_info']);
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];

            return  CTAPIStaffBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
        });
    }

    /**
     * 完善个人资料页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function perfect_user(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $info = $this->user_info;
            $user_type = $info['admin_type'] ?? 0;
            // 非用户不可进行此操作！
            if(empty($info) || $user_type != 4){
//            throws('您还没有注册！', $this->getNotLoginErrCode());
                return redirect('web/login');
            }
//            $handleKeyArr = [];
//            if($user_type == 4) array_push($handleKeyArr, 'company');// array_merge($handleKeyArr, ['industry', 'siteResources']); ;//
//            if(!empty($handleKeyArr)) CTAPIStaffBusiness::handleData($request, $this, $info, $handleKeyArr);

            $relationFormatConfigs = CTAPIStaffBusiness::getRelationConfigs($request, $this, ['company_info'], []);// , 'join_items'
            CTAPIStaffBusiness::formatRelationList( $request, $this, $info, $relationFormatConfigs);
            $reDataArr['info'] = $info;
            // 获得城市KV值
            $reDataArr['citys_kv'] = CTAPICitysBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'city_name']);
            $reDataArr['defaultCity'] = $info['city_id'] ?? -1;// 默认

            $this->setHostType($reDataArr);
            return view('web.QualityControl.home.perfect_user', $reDataArr);
        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * @OA\Post(
     *     path="/api/web/ajax_reg",
     *     tags={"前端-帐号注册登录"},
     *     summary="帐号密码登录",
     *     description="通过帐号、密码、图形验证码进行注册",
     *     operationId="webIndexAjax_reg",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_admin_username"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_admin_password"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_repass"),
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
    public function ajax_reg(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        $admin_type = CommonRequest::getInt($request, 'admin_type');
        if(!in_array($admin_type, [2, 4])) throws('帐户类型无效！');
        $regInitData = [
            'company_is_legal_persion' => 2,// 企业--是否独立法人1独立法人 2非独立法人
            'issuper' => 2,
            'company_type' => 0,// 企业类型1检测机构、2生产企业
            'company_prop' => 0,// 企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
            'company_peoples_num' => 0,// 单位人数1、1-20、2、20-100、3、100-500、4、500以上
            'open_status' => 2,// 审核状态1待审核2审核通过4审核不通过
            'account_status' => 1// 状态 1正常 2冻结
        ];
        // 企业和用户需要审核
        if(in_array($admin_type, [2, 4])) $regInitData['open_status'] = 1;
        $login_type = 1;
        $checkType = 1;
        if($admin_type == 4){// 用户
            $login_type = 2;
            $checkType = 2;
            $real_name = CommonRequest::get($request, 'real_name');
            $sex = CommonRequest::getInt($request, 'sex');
            $regInitData = array_merge($regInitData, [
                'real_name' => $real_name,
                'sex' => $sex,
            ]);
        }
        $userInfo = CTAPIStaffBusiness::loginCaptchaCode($request, $this,$admin_type, $login_type, $checkType, 2, $regInitData);
        // 保存注册信息
        $preKey = CommonRequest::get($request, 'preKey');// 0 小程序 1后台[默认]
        if(!is_numeric($preKey)) $preKey = 1;
        $redisKey = $this->setUserInfo($userInfo['id'], $preKey);
        $userInfo['redisKey'] = $redisKey;
        return ajaxDataArr(1, $userInfo, '');

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
         $this->InitParams($request);
        CTAPIStaffBusiness::loginOut($request, $this);
        $reDataArr = $this->reDataArr;
        return redirect('web/login');
    }

    /**
     * ajax保存数据--注册-补充企业资料
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_perfect_company(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        // 判断是否就是登录的用户
        if($id != $this->user_info['id']) throws('参数[id]有误！');
        if($this->user_info['admin_type'] != 2)  throws('非企业用户不可进行此操作！');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
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
        $laboratory_addr = CommonRequest::get($request, 'laboratory_addr');
        // 判断开始结束日期
        Tool::judgeBeginEndDate($ratify_date, $valid_date, 1 + 2 + 256 + 512, 1, date('Y-m-d'), '有效起止日期');

        $company_contact_name = CommonRequest::get($request, 'company_contact_name');
        $company_contact_mobile = CommonRequest::get($request, 'company_contact_mobile');
        $company_contact_tel = CommonRequest::get($request, 'company_contact_tel');
        // 可能会用的参数
        $admin_username = CommonRequest::get($request, 'admin_username');
        $admin_password = CommonRequest::get($request, 'admin_password');
        $sure_password = CommonRequest::get($request, 'sure_password');

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
            'admin_type' => $this->user_info['admin_type'],
            'is_perfect' => 2,
            'open_status' =>2,// 改为默认审核通过
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
            'laboratory_addr' => $laboratory_addr,
            'company_contact_name' => $company_contact_name,
            'company_contact_mobile' => $company_contact_mobile,
            'company_contact_tel' => $company_contact_tel,
            // 'resource_id' => $resource_id[0] ?? 0,// 第一个图片资源的id
            'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
            'resourceIds' => $resource_id,// 此下标为图片资源关系
        ];
        if($admin_username != '') $saveData['admin_username'] = $admin_username;
        if($admin_password != '' || $sure_password != ''){
            if ($admin_password != $sure_password){
                return ajaxDataArr(0, null, '密码和确定密码不一致！');
            }
            $saveData['admin_password'] = $admin_password;
        }

        // 如果是从 陕西省市场监督管理局 抓取的新用户，完善资料后，改为已完善资料
        $is_import = $this->user_info['is_import'] ?? 0;
        if($is_import == 1){
            $saveData['is_import'] = 2;
        }
        // 超级帐户 不可 冻结
//        if(isset($userInfo['issuper']) && $userInfo['issuper'] != 1){
//            $saveData['account_status'] = $account_status;
//        }

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * ajax保存数据--注册-补充用户资料
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_perfect_user(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        // 判断是否就是登录的用户
        if($id != $this->user_info['id']) throws('参数[id]有误！');
        if($this->user_info['admin_type'] != 4)  throws('非用户不可进行此操作！');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $company_id = CommonRequest::getInt($request, 'company_id');
//        $real_name = CommonRequest::get($request, 'real_name');
//        $sex = CommonRequest::getInt($request, 'sex');
        $email = CommonRequest::get($request, 'email');
//        $mobile = CommonRequest::get($request, 'mobile');
        $qq_number = CommonRequest::get($request, 'qq_number');
        $id_number = CommonRequest::get($request, 'id_number');
        $position_name = CommonRequest::get($request, 'position_name');
        $city_id = CommonRequest::getInt($request, 'city_id');
        $addr = CommonRequest::get($request, 'addr');
        // 可能会用的参数
        $admin_username = CommonRequest::get($request, 'admin_username');
        $admin_password = CommonRequest::get($request, 'admin_password');
        $sure_password = CommonRequest::get($request, 'sure_password');
        // 判断企业是否存在
        $companyInfo = static::getStaffInfo($company_id);
        if(empty($companyInfo) || $companyInfo['admin_type'] != 2) throws('所属企业不存在！');
        $saveData = [
            'admin_type' => $this->user_info['admin_type'],
            'is_perfect' => 2,
            'company_id' => $company_id,
//            'real_name' => $real_name,
//            'sex' => $sex,
//            'mobile' => $mobile,
            'email' => $email,
            'qq_number' => $qq_number,
            'id_number' => $id_number,
            'position_name' => $position_name,
            'city_id' => $city_id,
            'addr' => $addr,
            // 'force_company_num' => 1,
        ];
        // 如果改变了所属企业,需要重新统计员工数
        if(isset($saveData['company_id']) && $company_id != $this->user_info['company_id']) $saveData['force_company_num'] = 1;

        if($admin_username != '') $saveData['admin_username'] = $admin_username;
        if($admin_password != '' || $sure_password != ''){
            if ($admin_password != $sure_password){
                return ajaxDataArr(0, null, '密码和确定密码不一致！');
            }
            $saveData['admin_password'] = $admin_password;
        }
        // 超级帐户 不可 冻结
//        if(isset($userInfo['issuper']) && $userInfo['issuper'] != 1){
//            $saveData['account_status'] = $account_status;
//        }

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }
}
