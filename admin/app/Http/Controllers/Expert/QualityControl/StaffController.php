<?php

namespace App\Http\Controllers\Expert\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPIIndustryBusiness;
use App\Business\Controller\API\QualityControl\CTAPIResourceBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\Staff;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class StaffController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    public static $ADMIN_TYPE = 1;// 类型1平台2企业4个人
    public static $VIEW_NAME = 'Staff';// 视图文件夹名称

    // 下面的只能判断操作的数据是这个栏目的数据

    // 判断操作权限--根据用户id
    public function judgePower(Request $request, $staff_id = 0){
        $userInfo = $this->getStaffInfo($staff_id);
        $this->judgeUserPower($request, $userInfo);
        return $userInfo;
    }

    // 判断操作权限--根据用户信息【一维数组】
    public function judgeUserPower(Request $request, $userInfo = []){
        if(empty($userInfo) || count($userInfo) <= 0){
            throws('用户名信息不存在！');
        }
        // 判断类型是否正确 1平台2老师4学生
        if($userInfo['admin_type'] != static::$ADMIN_TYPE){
            throws('用户类型不一致！');
        }
        return true;
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
//            return view('expert.QualityControl.' . static::$VIEW_NAME . '.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'expert.QualityControl.' . static::$VIEW_NAME . '.index', true
            , 'doListPage', [], function (&$reDataArr) use ($request){

            });
    }

    /**
     * 同事选择-弹窗
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function select(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            // 获得城市KV值--企业和用户有城市
            if(in_array(static::$ADMIN_TYPE , [2, 4])) {
                $reDataArr['citys_kv'] = CTAPICitysBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'city_name']);
                $reDataArr['defaultCity'] = $info['city_id'] ?? -1;// 默认
            }

            // 所属行业--只有企业有
            if(static::$ADMIN_TYPE == 2){
                $reDataArr['industry_kv'] = CTAPIIndustryBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'industry_name']);
                $reDataArr['defaultIndustry'] = $info['company_industry_id'] ?? -1;// 默认

            }
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
            $open_status = CommonRequest::get($request, 'open_status');
            if(strlen($open_status) <= 0 ) $open_status = -1;
            $reDataArr['openStatus'] =  Staff::$openStatusArr;
            $reDataArr['defaultOpenStatus'] = $open_status;// -1;// 列表页默认状态

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

            // 授权人审核状态 1待审核 2 审核通过  4 审核未通过
            $reDataArr['signStatus'] =  Staff::$signStatusArr;
            $reDataArr['defaultSignStatus'] = -1;// 列表页默认状态

            // 角色1法人  2最高管理者  4技术负责人  8授权签字人
            $reDataArr['roleNum'] =  Staff::$roleNumArr;
            $reDataArr['defaultRoleNum'] = -1;// 列表页默认状态

            // 是否食品1食品  2非食品
            $reDataArr['signIsFood'] =  Staff::$signIsFoodArr;
            $reDataArr['defaultSignIsFood'] = -1;// 列表页默认状态

            // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
            $reDataArr['companyGrade'] =  Staff::$companyGradeArr;
            $company_grade = CommonRequest::get($request, 'company_grade');
            if(strlen($company_grade) <= 0 ) $company_grade = -1;
            $reDataArr['defaultCompanyGrade'] = $company_grade;// 列表页默认状态
            $reDataArr['company_grade'] = $company_grade;
            return view('expert.QualityControl.' . static::$VIEW_NAME . '.select', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'expert.QualityControl.RrrDddd.select', true
//            , 'doListPage', [], function (&$reDataArr) use ($request){
//
//            });
    }

    /**
     * 添加
     * 大后台: 管理员、企业 、 个人 修改
     * 企业后台： 企业 、 个人 修改
     * 个人后台：个人 修改
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function add(Request $request,$id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('expert.QualityControl.' . static::$VIEW_NAME . '.add', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'expert.QualityControl.' . static::$VIEW_NAME . '.add', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

        });
    }

    /**
     * @OA\Get(
     *     path="/api/expert/staff/ajax_info",
     *     tags={"大后台-系统管理-帐号管理"},
     *     summary="帐号管理--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="expertQualityControlStaffAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_staff_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_staff"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_staff"}
     */
    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_info(Request $request){
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');

        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
        $info = CTAPIStaffBusiness::getInfoData($request, $this, $id, [], '', []);
        $this->judgeUserPower($request, $info);
        // 判断是否有操作权限
        // 根据具体功能 ，加上或去掉要判断的下标
        $powerFields = [];// ['organize_id' => 'company_id', 'personal_id' => 'id'];
        if(!$this->batchJudgeRecordOperateAuth($info, $powerFields, 0, 0, 0, true)){
            return ajaxDataArr(0, null, '您没有操作权限！');
        }
        $resultDatas = ['info' => $info];
        return ajaxDataArr(1, $resultDatas, '');

//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
    }

    /**
     * @OA\Post(
     *     path="/api/expert/staff/ajax_save",
     *     tags={"大后台-系统管理-帐号管理"},
     *     summary="帐号管理--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="expertQualityControlStaffAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_staff_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_staff"}
     */

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request)
    {
//        $this->InitParams($request);

        $id = CommonRequest::getInt($request, 'id');
        $pageNum = ($id > 0) ? 256 : 32;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request){
                $id = CommonRequest::getInt($request, 'id');
                // CommonRequest::judgeEmptyParams($request, 'id', $id);
                $real_name = CommonRequest::get($request, 'real_name');
                $sex = CommonRequest::getInt($request, 'sex');
//        $account_status = CommonRequest::getInt($request, 'account_status');
                $mobile = CommonRequest::get($request, 'mobile');
                $tel = CommonRequest::get($request, 'tel');
                $qq_number = CommonRequest::get($request, 'qq_number');
                $admin_username = CommonRequest::get($request, 'admin_username');
                $admin_password = CommonRequest::get($request, 'admin_password');
                $sure_password = CommonRequest::get($request, 'sure_password');
                $userInfo = [];
                if($id > 0){
                    $userInfo = $this->judgePower($request, $id);
                    // 判断是否有操作权限
                    // 根据具体功能 ，加上或去掉要判断的下标
                    $powerFields = [];// ['organize_id' => 'company_id', 'personal_id' => 'id'];
                    if(!$this->batchJudgeRecordOperateAuth($userInfo, $powerFields, 0, 0, 0, true)){
                        return ajaxDataArr(0, null, '您没有操作权限！');
                    }
                }

                $saveData = [
                    'admin_type' => static::$ADMIN_TYPE,
                    'real_name' => $real_name,
                    'sex' => $sex,
//            'gender' => $sex,
//            'account_status' => $account_status,
                    'mobile' => $mobile,
                    'tel' => $tel,
                    'qq_number' => $qq_number,
                    'admin_username' => $admin_username,
                ];
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
        });
    }

    /**
     * @OA\Get(
     *     path="/api/expert/staff/ajax_alist",
     *     tags={"大后台-系统管理-帐号管理"},
     *     summary="帐号管理--列表",
     *     description="帐号管理--列表......",
     *     operationId="expertQualityControlStaffAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_staff_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_staff"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_staff"}
     */
    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_alist(Request $request){
//        $this->InitParams($request);
//        // $this->company_id = 1;
//        $mergeParams = [
//            'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
//        ];
//        // 企业 的 个人--只能读自己的人员信息
//        if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
//            $mergeParams['company_id'] = $this->own_organize_id;
//        }
//        CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);
//
//        $relations = [];//  ['siteResources']
//        $handleKeyArr = [];
//        $handleKeyConfigArr = [];
//        if(static::$ADMIN_TYPE == 2){
//            array_push($handleKeyArr, 'industry');// array_merge($handleKeyArr, ['industry', 'siteResources']); ;//
//            array_push($handleKeyConfigArr, 'industry_info');
//        }
//        if(in_array(static::$ADMIN_TYPE, [2, 4])){
//            $handleKeyArr = array_merge($handleKeyArr, ['extend', 'city']);
//            $handleKeyConfigArr = array_merge($handleKeyConfigArr, ['extend_info', 'city_info']);
//        }
//        if(static::$ADMIN_TYPE == 4){
//            array_push($handleKeyArr, 'company');
//            array_push($handleKeyConfigArr, 'company_info');
//        }
//
//        $extParams = [
//            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
//        ];
//
//        return  CTAPIStaffBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){
            // $this->company_id = 1;
            $mergeParams = [
                'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
            ];
            // 企业 的 个人--只能读自己的人员信息
            if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
                $mergeParams['company_id'] = $this->own_organize_id;
            }
            CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);

            $relations = [];//  ['siteResources']
            $handleKeyArr = [];
            $handleKeyConfigArr = [];
            if(static::$ADMIN_TYPE == 2){
                array_push($handleKeyArr, 'industry');// array_merge($handleKeyArr, ['industry', 'siteResources']); ;//
                array_push($handleKeyConfigArr, 'industry_info');
            }
            if(in_array(static::$ADMIN_TYPE, [2, 4])){
                $handleKeyArr = array_merge($handleKeyArr, ['extend', 'city']);
                $handleKeyConfigArr = array_merge($handleKeyConfigArr, ['extend_info', 'city_info']);
            }
            if(static::$ADMIN_TYPE == 4){
                array_push($handleKeyArr, 'company');
                array_push($handleKeyConfigArr, 'company_info');
            }

            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];

            return  CTAPIStaffBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
        });
    }

    /**
     * 选择短信模板页面
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function sms_send(Request $request)
    {
        return $this->exeDoPublicFun($request, 34359738368, 8, 'admin.QualityControl.SmsTemplate.sms_send', true
            , '', [], function (&$reDataArr) use ($request){
                $sms_operate_type = 1;// 操作类型 1 发送短信  ; 2测试发送短信
                $reDataArr['sms_operate_type'] = $sms_operate_type;
                // 设置参数
                $mergeParams = [// template_id 与 module_id 二选一
                    // 'sms_template_id' => 1,// 短信模板id;--可为0 ；
                    'sms_module_id' => 1,// 短信模块id
                ];
                CTAPISmsTemplateBusiness::mergeRequest($request, $this, $mergeParams);

                $smsMobileFieldKV = ['mobile' => '手机号'];// 可以发送短信的手机号字段
                $smsMobileField = 'mobile';// 默认的发送短信的手机号字段
                $reDataArr['smsMobileFieldKV'] = $smsMobileFieldKV;
                $reDataArr['defaultSmsMobileField'] = $smsMobileField;
                CTAPISmsTemplateBusiness::smsSend($request,  $this, $reDataArr);
            });
    }

    /**
     * ajax发送手机短信
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_sms_send(Request $request){
        return $this->exeDoPublicFun($request, 68719476736, 4,'', true, '', [], function (&$reDataArr) use ($request){

            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                'relationFormatConfigs'=> CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $this,
//                    ['company_name' => '', 'course_name' =>'', 'class_name' =>'', 'staff_info' => ['resource_list' => ''], 'course_order_info' => ''], []),
//                'listHandleKeyArr' => ['priceIntToFloat'],
            ];
            return CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
        });
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_ids(Request $request){
//        $this->InitParams($request);
//        $result = CTAPIStaffBusiness::getList($request, $this, 1 + 0);
//        $data_list = $result['result']['data_list'] ?? [];
//        $ids = implode(',', array_column($data_list, 'id'));
//        return ajaxDataArr(1, $ids, '');
//        return $this->exeDoPublicFun($request, 4294967296, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $result = CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0);
//            $data_list = $result['result']['data_list'] ?? [];
//            $ids = implode(',', array_column($data_list, 'id'));
//            return ajaxDataArr(1, $ids, '');
//        });
//    }


    /**
     * 导出
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function export(Request $request){
//        $this->InitParams($request);
//        // $this->company_id = 1;
//        $mergeParams = [
//            'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
//        ];
//        // 企业 的 个人--只能读自己的人员信息
//        if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
//            $mergeParams['company_id'] = $this->own_organize_id;
//        }
//        CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);
//        $relations = [];//  ['siteResources']
//        $handleKeyArr = [];
//        $handleKeyConfigArr = [];
//        if(static::$ADMIN_TYPE == 2){
//            array_push($handleKeyArr, 'industry');// array_merge($handleKeyArr, ['industry', 'siteResources']); ;//
//            array_push($handleKeyConfigArr, 'industry_info');
//        }
//        if(in_array(static::$ADMIN_TYPE, [2, 4])){
//            $handleKeyArr = array_merge($handleKeyArr, ['extend', 'city']);
//            $handleKeyConfigArr = array_merge($handleKeyConfigArr, ['extend_info', 'city_info']);
//        }
//        if(static::$ADMIN_TYPE == 4){
//            array_push($handleKeyArr, 'company');
//            array_push($handleKeyConfigArr, 'company_info');
//        }
//
//        $extParams = [
//            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
//        ];
//
//
//        CTAPIStaffBusiness::getList($request, $this, 1 + 0, [], $relations, $extParams);
        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){

            // $this->company_id = 1;
            $mergeParams = [
                'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
            ];
            // 企业 的 个人--只能读自己的人员信息
            if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
                $mergeParams['company_id'] = $this->own_organize_id;
            }
            CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);
            $relations = [];//  ['siteResources']
            $handleKeyArr = [];
            $handleKeyConfigArr = [];
            if(static::$ADMIN_TYPE == 2){
                array_push($handleKeyArr, 'industry');// array_merge($handleKeyArr, ['industry', 'siteResources']); ;//
                array_push($handleKeyConfigArr, 'industry_info');
            }
            if(in_array(static::$ADMIN_TYPE, [2, 4])){
                $handleKeyArr = array_merge($handleKeyArr, ['extend', 'city']);
                $handleKeyConfigArr = array_merge($handleKeyConfigArr, ['extend_info', 'city_info']);
            }
            if(static::$ADMIN_TYPE == 4){
                array_push($handleKeyArr, 'company');
                array_push($handleKeyConfigArr, 'company_info');
            }

            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];


            CTAPIStaffBusiness::getList($request, $this, 1 + 0, [], $relations, $extParams);
        });
    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function import_template(Request $request){
//        $this->InitParams($request);
//        // $this->company_id = 1;
//        $mergeParams = [
//            'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
//        ];
//        // 企业 的 个人--只能读自己的人员信息
//        if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
//            $mergeParams['company_id'] = $this->own_organize_id;
//        }
//        CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);
//
//        CTAPIStaffBusiness::importTemplate($request, $this);
        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){

            // $this->company_id = 1;
            $mergeParams = [
                'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
            ];
            // 企业 的 个人--只能读自己的人员信息
            if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
                $mergeParams['company_id'] = $this->own_organize_id;
            }
            CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);

            CTAPIStaffBusiness::importTemplate($request, $this);
        });
    }


    /**
     * @OA\Post(
     *     path="/api/expert/staff/ajax_del",
     *     tags={"大后台-系统管理-帐号管理"},
     *     summary="帐号管理--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="expertQualityControlStaffAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_staff_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_staff"}
     */
    /**
     * 子帐号管理-删除
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_del(Request $request)
    {
//        $this->InitParams($request);
//        $id = CommonRequest::get($request, 'id');
//        $company_id = 0;
//        // 删除的是个人， 是企业后台--操作的-- 企业只能删除自己的员工
//        if(static::$ADMIN_TYPE == 4 && $this->user_type == 2){
//            $company_id = $this->own_organize_id;
//        }
//        // 删除员工--还需要重新统计企业的员工数
//        // 企业删除 ---有员工的企业不能删除，需要先删除/解绑员工
//        if(in_array(static::$ADMIN_TYPE, [2, 4])){
//            $organize_id = $this->organize_id;
//            // 大后台--可以删除所有的员工；删除企业【无员工】
//            // 企业后台 -- 删除员工，只能删除自己的员工；无删除企业
//            // 个人后台--不可进行删除操作
//            if($this->user_type == 2 && static::$ADMIN_TYPE == 4) $organize_id = $this->own_organize_id;
//            return CTAPIStaffBusiness::delDatasAjax($request, $this, static::$ADMIN_TYPE, $organize_id);
//        }else{// 管理员 直接删除
//            $delResult = CTAPIStaffBusiness::delByIds($request, $this, static::$ADMIN_TYPE, $id, $company_id, 0);
//            return  ajaxDataArr(1, $delResult, '');
//        }

        $tem_id = CommonRequest::get($request, 'id');
        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){

            $id = CommonRequest::get($request, 'id');
            $company_id = 0;
            // 删除的是个人， 是企业后台--操作的-- 企业只能删除自己的员工
            if(static::$ADMIN_TYPE == 4 && $this->user_type == 2){
                $company_id = $this->own_organize_id;
            }
            // 删除员工--还需要重新统计企业的员工数
            // 企业删除 ---有员工的企业不能删除，需要先删除/解绑员工
            if(in_array(static::$ADMIN_TYPE, [2, 4])){
                $organize_id = $this->organize_id;
                // 大后台--可以删除所有的员工；删除企业【无员工】
                // 企业后台 -- 删除员工，只能删除自己的员工；无删除企业
                // 个人后台--不可进行删除操作
                if($this->user_type == 2 && static::$ADMIN_TYPE == 4) $organize_id = $this->own_organize_id;
                return CTAPIStaffBusiness::delDatasAjax($request, $this, static::$ADMIN_TYPE, $organize_id);
            }else{// 管理员 直接删除
                $delResult = CTAPIStaffBusiness::delByIds($request, $this, static::$ADMIN_TYPE, $id, $company_id, 0);
                return  ajaxDataArr(1, $delResult, '');
            }
        });
    }

    /**
     * ajax根据部门id,小组id获得所属部门小组下的员工数组[kv一维数组]
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_child(Request $request){
//        $this->InitParams($request);
//        $parent_id = CommonRequest::getInt($request, 'parent_id');
//        // 获得一级城市信息一维数组[$k=>$v]
//        $childKV = CTAPIStaffBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIStaffBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//        return $this->exeDoPublicFun($request, 8589934592, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $parent_id = CommonRequest::getInt($request, 'parent_id');
//            // 获得一级城市信息一维数组[$k=>$v]
//            $childKV = CTAPIRrrDdddBusiness::getCityByPid($request, $this, $parent_id);
//            // $childKV = CTAPIRrrDdddBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//            return  ajaxDataArr(1, $childKV, '');
//        });
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIStaffBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
///
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $fileName = 'staffs.xlsx';
//            $resultDatas = CTAPIRrrDdddBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * 单文件上传-导入excel
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function import(Request $request)
    {
//        $this->InitParams($request);
//        // $this->company_id = 1;
//        // 企业 的 个人--只能读自己的人员信息
//        $organize_id = 0;
//        if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
//            $organize_id = $this->own_organize_id;
//        }else if(static::$ADMIN_TYPE == 4){
//            $organize_id = CommonRequest::getInt($request, 'company_id');
//            if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
//        }
//
//        $mergeParams = [
//            'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
//            'company_id' => $organize_id,
//        ];
//        CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);
//
//        // 上传并保存文件
//        $result = CTAPIResourceBusiness::fileSingleUpload($request, $this, 2);
//        if($result['apistatus'] == 0) return $result;
//        // 文件上传成功
//        // /srv/www/dogtools/admin/public/resource/company/5/excel/2020/06/21/2020062115463441018048779bab4a.xlsx
//        $fileName = Tool::getPath('public') . $result['result']['filePath'];
//        $resultDatas = [];
//        try{
//            $resultDatas = CTAPIStaffBusiness::importByFile($request, $this, $fileName);
//        } catch ( \Exception $e) {
//            throws($e->getMessage());
//        } finally {
//            $resourceId = $result['result']['id'] ?? 0;
//            if ($resourceId > 0) {
//                CTAPIStaffBusiness::mergeRequest($request, $this, [
//                    'id' => $resourceId,
//                ]);
//                CTAPIResourceBusiness::delAjax($request, $this);
//            }
//            // 删除上传的文件
//            Tool::resourceDelFile(['resource_url' => $result['result']['filePath']]);
//        }
//        return ajaxDataArr(1, $resultDatas, '');
        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){

            // $this->company_id = 1;
            // 企业 的 个人--只能读自己的人员信息
            $organize_id = 0;
            if($this->user_type == 2 && static::$ADMIN_TYPE == 4){
                $organize_id = $this->own_organize_id;
            }else if(static::$ADMIN_TYPE == 4){
                $organize_id = CommonRequest::getInt($request, 'company_id');
                if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
            }

            $mergeParams = [
                'admin_type' => static::$ADMIN_TYPE,// 类型1平台2企业4个人
                'company_id' => $organize_id,
            ];
            CTAPIStaffBusiness::mergeRequest($request, $this, $mergeParams);

            // 上传并保存文件
            $result = CTAPIResourceBusiness::fileSingleUpload($request, $this, 2);
            if($result['apistatus'] == 0) return $result;
            // 文件上传成功
            // /srv/www/dogtools/admin/public/resource/company/5/excel/2020/06/21/2020062115463441018048779bab4a.xlsx
            $fileName = Tool::getPath('public') . $result['result']['filePath'];
            $resultDatas = [];
            try{
                $resultDatas = CTAPIStaffBusiness::importByFile($request, $this, $fileName);
            } catch ( \Exception $e) {
                throws($e->getMessage());
            } finally {
                $resourceId = $result['result']['id'] ?? 0;
                if ($resourceId > 0) {
                    CTAPIStaffBusiness::mergeRequest($request, $this, [
                        'id' => $resourceId,
                    ]);
                    CTAPIResourceBusiness::delAjax($request, $this);
                }
                // 删除上传的文件
                Tool::resourceDelFile(['resource_url' => $result['result']['filePath']]);
            }
            return ajaxDataArr(1, $resultDatas, '');
        });
    }

    /**
     * 子帐号管理-审核操作(通过/不通过)
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_open(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::get($request, 'id');// 单个id 或 逗号分隔的多个，或 多个的一维数组
        if(is_array($id)) $id = implode(',', $id);
        $open_status = CommonRequest::getInt($request, 'open_status');// 操作类型 2审核通过     4审核不通过

        $organize_id = $this->organize_id;
        // 大后台--可以操作所有的员工；操作企业【无员工】
        // 企业后台 -- 操作员工，只能操作自己的员工；无操作企业
        // 个人后台--不可进行操作
        if($this->user_type == 2 && static::$ADMIN_TYPE == 4) $organize_id = $this->own_organize_id;
        $modifyNum = CTAPIStaffBusiness::openAjax($request, $this, static::$ADMIN_TYPE, $organize_id, $id, $open_status);
        return ajaxDataArr(1, ['modify_num' => $modifyNum], '');
    }

    /**
     * 子帐号管理-授权人审核操作(通过/不通过)
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_sign(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::get($request, 'id');// 单个id 或 逗号分隔的多个，或 多个的一维数组
        if(is_array($id)) $id = implode(',', $id);
        $sign_status = CommonRequest::getInt($request, 'sign_status');// 操作类型 2审核通过     4审核不通过

        $organize_id = $this->organize_id;
        // 大后台--可以操作所有的员工；操作企业【无员工】
        // 企业后台 -- 操作员工，只能操作自己的员工；无操作企业
        // 个人后台--不可进行操作
        if($this->user_type == 2 && static::$ADMIN_TYPE == 4) $organize_id = $this->own_organize_id;
        $modifyNum = CTAPIStaffBusiness::signAjax($request, $this, static::$ADMIN_TYPE, $organize_id, $id, $sign_status);
        return ajaxDataArr(1, ['modify_num' => $modifyNum], '');
    }

    /**
     * 子帐号管理-角色审核操作(通过/不通过)
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_role(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::get($request, 'id');// 单个id 或 逗号分隔的多个，或 多个的一维数组
        if(is_array($id)) $id = implode(',', $id);
        $role_status = CommonRequest::getInt($request, 'role_status');// 操作类型 2审核通过     4审核不通过

        $organize_id = $this->organize_id;
        // 大后台--可以操作所有的员工；操作企业【无员工】
        // 企业后台 -- 操作员工，只能操作自己的员工；无操作企业
        // 个人后台--不可进行操作
        if($this->user_type == 2 && static::$ADMIN_TYPE == 4) $organize_id = $this->own_organize_id;
        $modifyNum = CTAPIStaffBusiness::roleAjax($request, $this, static::$ADMIN_TYPE, $organize_id, $id, $role_status);
        return ajaxDataArr(1, ['modify_num' => $modifyNum], '');
    }

    /**
     * 子帐号管理-(冻结/解冻)
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_frozen(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::get($request, 'id');// 单个id 或 逗号分隔的多个，或 多个的一维数组
        if(is_array($id)) $id = implode(',', $id);
        $account_status = CommonRequest::getInt($request, 'account_status');// 操作类型 状态 1正常--解冻操作； 2冻结--冻结操作
        $organize_id = $this->organize_id;
        // 大后台--可以操作所有的员工；操作企业【无员工】
        // 企业后台 -- 操作员工，只能操作自己的员工；无操作企业
        // 个人后台--不可进行操作
        if($this->user_type == 2 && static::$ADMIN_TYPE == 4) $organize_id = $this->own_organize_id;
        $modifyNum = CTAPIStaffBusiness::accountStatusAjax($request, $this, static::$ADMIN_TYPE, $organize_id, $id, $account_status);
        return ajaxDataArr(1, ['modify_num' => $modifyNum], '');
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


        // 获得城市KV值--企业和用户有城市
        if(in_array(static::$ADMIN_TYPE , [2, 4])) {
            $reDataArr['citys_kv'] = CTAPICitysBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'city_name']);
            $reDataArr['defaultCity'] = $info['city_id'] ?? -1;// 默认
        }

        // 所属行业--只有企业有
        if(static::$ADMIN_TYPE == 2){
            // 数据类型
            $record_type = CommonRequest::getInt($request, 'record_type');
            if(strlen($record_type) <= 0 ) $record_type = -1;
            $reDataArr['recordType'] =  Staff::$recordTypeArr;
            $reDataArr['defaultRecordType'] = $record_type;// -1;// 列表页默认状态

            // 企业--会员等级是否有续期1没有2有
            $company_grade_continue = CommonRequest::getInt($request, 'company_grade_continue');
            if(strlen($company_grade_continue) <= 0 ) $company_grade_continue = -1;
            $reDataArr['companyGradeContinue'] =  Staff::$companyGradeContinueArr;
            $reDataArr['defaultCompanyGradeContinue'] = $company_grade_continue;// -1;// 列表页默认状态


            $reDataArr['industry_kv'] = CTAPIIndustryBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'industry_name']);
            $reDataArr['defaultIndustry'] = $info['company_industry_id'] ?? -1;// 默认

        }

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
        $open_status = CommonRequest::get($request, 'open_status');
        if(strlen($open_status) <= 0 ) $open_status = -1;
        $reDataArr['openStatus'] =  Staff::$openStatusArr;
        $reDataArr['defaultOpenStatus'] = $open_status;// -1;// 列表页默认状态

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

        // 授权人审核状态 1待审核 2 审核通过  4 审核未通过
        $sign_status = CommonRequest::get($request, 'sign_status');
        if(strlen($sign_status) <= 0 ) $sign_status = -1;
        $reDataArr['signStatus'] =  Staff::$signStatusArr;
        $reDataArr['defaultSignStatus'] = $sign_status;// -1;// 列表页默认状态

        // 角色审核状态 1待审核 2 审核通过  4 审核未通过
        $role_status = CommonRequest::get($request, 'role_status');
        if(strlen($role_status) <= 0 ) $role_status = -1;
        $reDataArr['roleStatus'] =  Staff::$roleStatusArr;
        $reDataArr['defaultRoleStatus'] = $role_status;// -1;// 列表页默认状态


        // 角色1法人  2最高管理者  4技术负责人  8授权签字人
        $role_num = CommonRequest::get($request, 'role_num');
        if(strlen($role_num) <= 0 ) $role_num = -1;
        $reDataArr['roleNum'] =  Staff::$roleNumArr;
        $reDataArr['defaultRoleNum'] = $role_num;// -1;// 列表页默认状态

        // 是否食品1食品  2非食品
        $reDataArr['signIsFood'] =  Staff::$signIsFoodArr;
        $reDataArr['defaultSignIsFood'] = -1;// 列表页默认状态

        // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
        $reDataArr['companyGrade'] =  Staff::$companyGradeArr;
        $company_grade = CommonRequest::get($request, 'company_grade');
        if(strlen($company_grade) <= 0 ) $company_grade = -1;
        $reDataArr['defaultCompanyGrade'] = $company_grade;// 列表页默认状态
        $reDataArr['company_grade'] = $company_grade;

        $company_id = CommonRequest::getInt($request, 'company_id');
        $info = [];
        if(is_numeric($company_id) && $company_id > 0){
            // 获得企业信息
            $companyInfo = CTAPIStaffBusiness::getInfoData($request, $this, $company_id);
            if(empty($companyInfo)) throws('企业信息不存在！');
            $info['company_id'] = $company_id;
            $info['user_company_name'] = $companyInfo['company_name'] ?? '';
        }
        $reDataArr['info'] = $info;

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
        // $user_info = $this->user_info;
        $id = $extendParams['params']['id'] ?? 0;

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态
            $info = [
                'id'=>$id,
                //   'department_id' => 0,
                'role_num' => 0,
            ];
            $operate = "添加";
            if ($id > 0) { // 获得详情数据
                $operate = "修改";
                $handleKeyArr = [];
                $handleKeyConfigArr = [];
                if(static::$ADMIN_TYPE == 2){
                    array_push($handleKeyArr, 'siteResources');// array_merge($handleKeyArr, ['industry', 'siteResources']); ;//
                    array_push($handleKeyConfigArr, 'certificate_info');
                }
                if(static::$ADMIN_TYPE == 4){
                    array_push($handleKeyArr, 'company');
                    array_push($handleKeyConfigArr, 'company_info');
                }
                $extParams = [
                    // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                     'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),

                ];
                $info = CTAPIStaffBusiness::getInfoData($request, $this, $id, [], '', $extParams);
                $this->judgeUserPower($request, $info);
                // 判断是否有操作权限
                // 根据具体功能 ，加上或去掉要判断的下标
                $powerFields = [];// ['organize_id' => 'company_id', 'personal_id' => 'id'];
                if(!$this->batchJudgeRecordOperateAuth($info, $powerFields, 0, 0, 0, true)){
                    throws('您没有操作权限！');
                }
            }else{
                $company_id = CommonRequest::getInt($request, 'company_id');
                if(is_numeric($company_id) && $company_id > 0){
                    // 获得企业信息
                    $companyInfo = CTAPIStaffBusiness::getInfoData($request, $this, $company_id);
                    if(empty($companyInfo)) throws('企业信息不存在！');
                    $info['company_id'] = $company_id;
                    $info['user_company_name'] = $companyInfo['company_name'] ?? '';
                }
            }

            // $reDataArr = array_merge($reDataArr, $resultDatas);
            $reDataArr['info'] = $info;
            $reDataArr['operate'] = $operate;
            // 获得城市KV值--企业和用户有城市
            if(in_array(static::$ADMIN_TYPE , [2, 4])) {
                // 获得城市KV值
                $reDataArr['citys_kv'] = CTAPICitysBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'city_name']);
                $reDataArr['defaultCity'] = $info['city_id'] ?? -1;// 默认

                // 是否完善资料1待完善2已完善
                $reDataArr['isPerfect'] =  Staff::$isPerfectArr;
                $reDataArr['defaultIsPerfect'] = $info['is_perfect'] ?? -1;// 列表页默认状态
            }

            // 只有企业有
            if(static::$ADMIN_TYPE == 2) {

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
            }
            // 只有用户有
            if(static::$ADMIN_TYPE == 4) {
                // 授权人审核状态 1待审核 2 审核通过  4 审核未通过
                $reDataArr['signStatus'] =  Staff::$signStatusArr;
                $reDataArr['defaultSignStatus'] = $info['sign_status'] ?? -1;// 列表页默认状态

                // 角色1法人  2最高管理者  4技术负责人  8授权签字人
                $reDataArr['roleNum'] =  Staff::$roleNumArr;
                $reDataArr['defaultRoleNum'] = $info['role_num'] ?? -1;// 列表页默认状态

                // 角色审核状态 1待审核 2 审核通过  4 审核未通过
                $reDataArr['roleStatus'] =  Staff::$roleStatusArr;
                $reDataArr['defaultRoleStatus'] = $info['role_status'] ?? -1;// 列表页默认状态

                // 是否食品1食品  2非食品
                $reDataArr['signIsFood'] =  Staff::$signIsFoodArr;
                $reDataArr['defaultSignIsFood'] = $info['sign_is_food'] ?? -1;// 列表页默认状态

            }

            $company_hidden = CommonRequest::getInt($request, 'company_hidden');
            $reDataArr['company_hidden'] = $company_hidden;// =1 : 隐藏企业选择
        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
