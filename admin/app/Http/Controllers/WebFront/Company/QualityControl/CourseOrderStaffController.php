<?php

namespace App\Http\Controllers\WebFront\Company\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICourseBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseClassBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseOrderStaffBusiness;
use App\Business\Controller\API\QualityControl\CTAPIOrderPayMethodBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\CourseOrderStaff;
use App\Models\QualityControl\OrderPayConfig;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class CourseOrderStaffController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

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
//            return view('company.QualityControl.CourseOrderStaff.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'company.QualityControl.CourseOrderStaff.index', true
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
//    public function select(Request $request)
//    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            $reDataArr['province_kv'] = CTAPICourseOrderStaffBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPICourseOrderStaffBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('company.QualityControl.CourseOrderStaff.select', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'company.QualityControl.RrrDddd.select', true
//            , 'doListPage', [], function (&$reDataArr) use ($request){
//
//            });
//    }

    /**
     * 添加
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function add(Request $request,$id = 0)
//    {
////        $reDataArr = [];// 可以传给视图的全局变量数组
////        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
////            // 正常流程的代码
////
////            $this->InitParams($request);
////            // $reDataArr = $this->reDataArr;
////            $reDataArr = array_merge($reDataArr, $this->reDataArr);
////            return view('company.QualityControl.CourseOrderStaff.add', $reDataArr);
////
////        }, $this->errMethod, $reDataArr, $this->errorView);
//
//        $pageNum = ($id > 0) ? 64 : 16;
//        return $this->exeDoPublicFun($request, $pageNum, 1,'company.QualityControl.CourseOrderStaff.add', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
//    }

    /**
     * 分班
     * 参数 id 需要参与分班的人员id, 多个用逗号,分隔或 一维id数组
     * 参数  course_id 分配班级的班级所属课程类型
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function join_class(Request $request)
//    {
//        return $this->exeDoPublicFun($request, 0, 8, 'company.QualityControl.CourseOrderStaff.join_class', true
//            , '', [], function (&$reDataArr) use ($request){
//                $id = CommonRequest::get($request, 'id');
//                if(is_string($id)) $id = explode(',', $id);
//                if(!is_array($id)) $id = [];
//                if(empty($id)) throws('请选择要分班的学员');
//                $info = [
//                    'id'=> implode(',', $id),
//                    //   'department_id' => 0,
//                ];
//                // $course_id = CommonRequest::getInt($request, 'course_id');
//                $dataList = CTAPICourseOrderStaffBusiness::getClassStaffAndJudge($request, $this, $id, 1);
//                $courseIds = Tool::getArrFields($dataList, 'course_id');
//                if(count($courseIds) > 1) throws('只能对同一课程下的学员，不可进行分班操作！');
//                $course_id = $courseIds[0] ?? 0;
//                if(!is_numeric($course_id) || $course_id <= 0) throws('课程信息有误，不可进行分班操作！');
//                $reDataArr['info'] = $info;
//                $reDataArr['course_order_staff'] = $dataList;
//                // 获得可用班级信息KV
//                $extParamsQuery = [
//                    'sqlParams' => ['whereIn' => ['class_status' => [1, 2]]]
//                ];
//                if(is_numeric($course_id) && $course_id > 0){
//                        Tool::appendParamQuery($extParamsQuery['sqlParams'], $course_id, 'course_id', [0, '0', ''], ',', false);
//                }
//                $reDataArr['course_class_kv'] = CTAPICourseClassBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'class_name'], $extParamsQuery);
//                $reDataArr['defaultCourseClassId'] = $info['class_id'] ?? -1;// 默认
//            });
//    }

    /**
     * ajax保存数据--分班保存
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_join_class_save(Request $request)
//    {
////        $this->InitParams($request);
//        $pageNum = 0;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
//            , '', [], function (&$reDataArr) use ($request){
//                $id = CommonRequest::get($request, 'id');
//                // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                if(is_string($id)) $id = explode(',', $id);
//                if(!is_array($id)) $id = [];
//                if(empty($id)) throws('请选择要分班的学员');
//                $dataList = CTAPICourseOrderStaffBusiness::getClassStaffAndJudge($request, $this, $id, 1);
//                if(empty($dataList)) throws('请选择要分班的学员');
//
//                $class_id = CommonRequest::getInt($request, 'class_id');
//                // 获得班级信息
//                $classInfo = CTAPICourseClassBusiness::getFVFormatList( $request,  $this, 4, 1
//                    , ['id' => $class_id], false, [], []);
//                if(empty($classInfo)) throws('班级信息不存在！');
//                if(!in_array($classInfo['class_status'], [1, 2])) throws('班级非待开班或开班中状态，不能分配学员！');
//
//                $organize_id = $this->organize_id;
//                // 企业后台--可以操作所有的员工；操作企业【无员工】
//                // 企业后台 -- 操作员工，只能操作自己的员工；无操作企业
//                // 个人后台--不可进行操作
//                if($this->user_type == 2) $organize_id = $this->own_organize_id;
//                $resultDatas = CTAPICourseOrderStaffBusiness::joinedClassAjax($request, $this, $organize_id, $id, $class_id, true);
//                return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * ajax保存数据--取消分班保存
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_cancel_class(Request $request)
//    {
////        $this->InitParams($request);
//        $pageNum = 0;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
//            , '', [], function (&$reDataArr) use ($request){
//                $id = CommonRequest::get($request, 'id');
//                // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                if(is_string($id)) $id = explode(',', $id);
//                if(!is_array($id)) $id = [];
//                if(empty($id)) throws('请选择要取消分班的学员');
//                $dataList = CTAPICourseOrderStaffBusiness::getClassStaffAndJudge($request, $this, $id, 2);
//                if(empty($dataList)) throws('请选择要取消分班的学员');
//
////                $class_id = CommonRequest::getInt($request, 'class_id');
////                // 获得班级信息
////                $classInfo = CTAPICourseClassBusiness::getFVFormatList( $request,  $this, 4, 1
////                    , ['id' => $class_id], false, [], []);
////                if(empty($classInfo)) throws('班级信息不存在！');
////                if(!in_array($classInfo['class_status'], [1, 2])) throws('班级非待开班或开班中状态，不能分配学员！');
//
//                $organize_id = $this->organize_id;
//                // 企业后台--可以操作所有的员工；操作企业【无员工】
//                // 企业后台 -- 操作员工，只能操作自己的员工；无操作企业
//                // 个人后台--不可进行操作
//                if($this->user_type == 2) $organize_id = $this->own_organize_id;
//                $resultDatas = CTAPICourseOrderStaffBusiness::cancelClassAjax($request, $this, $organize_id, $id, true);
//                return ajaxDataArr(1, $resultDatas, '');
//            });
//    }

    /**
     * 缴费
     * 参数 id 需要参与缴费的人员id, 多个用逗号,分隔或 一维id数组
     * 参数  course_id 分配班级的班级所属课程类型--可为空
     * 参数  class_id 所属的班级id--可为空
     * 参数  company_id 报名用户所属的企业id-可为空
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function pay(Request $request)
    {
        return $this->exeDoPublicFun($request, 0, 8, 'company.QualityControl.CourseOrderStaff.pay', true
            , '', [], function (&$reDataArr) use ($request){
                $id = CommonRequest::get($request, 'id');
                if(is_string($id)) $id = explode(',', $id);
                if(!is_array($id)) $id = [];
                if(empty($id)) throws('请选择要缴费的学员');
                $info = [
                    'id'=> implode(',', $id),
                    //   'department_id' => 0,
                ];

//                $course_id = CommonRequest::getInt($request, 'course_id');
//                $class_id = CommonRequest::getInt($request, 'class_id');
                $company_id = $this->user_id;// CommonRequest::getInt($request, 'company_id');// 报名用户所属的企业id

                // 根据报名用户id,获得报名用户及支付信息
                list($dataList, $pay_configs_format, $companyKV) = CTAPICourseOrderStaffBusiness::getPayStaffByIds($request, $this, $id, $company_id);
                $dataPanyConfigList = Tool::arrUnderReset($dataList, 'pay_config_id', 2, '_');
                // 再按企业分
                foreach($dataPanyConfigList as $k => &$v){
                    $v = Tool::arrUnderReset($v, 'company_id', 2, '_');
                }
                // $reDataArr['course_order_staff'] = $dataList;

                $reDataArr['pay_config_format'] = $pay_configs_format;
                $reDataArr['company_kv'] = $companyKV;
                $reDataArr['config_staff_list'] = $dataPanyConfigList;

                $reDataArr['info'] = $info;

                // 收款开通类型(1现金、2微信支付、4支付宝)
                $reDataArr['payMethod'] =  CTAPIOrderPayMethodBusiness::getListKV($request, $this, ['key' => 'pay_method', 'val' => 'pay_name']);
                $reDataArr['defaultPayMethod'] = $info['pay_method'] ?? -1;// 列表页默认状态
                // pr($reDataArr);
            });
    }


    /**
     * 缴费--生成订单保存页面
     * 参数 id 需要参与缴费的人员id, 多个用逗号,分隔或 一维id数组
     * 参数  course_id 分配班级的班级所属课程类型--可为空
     * 参数  class_id 所属的班级id--可为空
     * 参数  pay_config_id 支付配置id-可为空
     * 参数  company_id 报名用户所属的企业id-可为空
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function pay_save(Request $request)
    {
        return $this->exeDoPublicFun($request, 0, 8, 'company.QualityControl.CourseOrderStaff.pay_save', true
            , '', [], function (&$reDataArr) use ($request){
                $id = CommonRequest::get($request, 'id');
                if(is_string($id)) $id = explode(',', $id);
                if(!is_array($id)) $id = [];
                if(empty($id)) throws('请选择要缴费的学员');
                $info = [
                    'id'=> implode(',', $id),
                    //   'department_id' => 0,
                ];

//                $course_id = CommonRequest::getInt($request, 'course_id');
//                $class_id = CommonRequest::getInt($request, 'class_id');
                $company_id = $this->user_id;// CommonRequest::getInt($request, 'company_id');// 报名用户所属的企业id
                $pay_config_id = CommonRequest::getInt($request, 'pay_config_id');// 支付配置id
                $pay_method = CommonRequest::getInt($request, 'pay_method');// 选择的缴费方式
                $reDataArr['company_id'] = $company_id;
                $reDataArr['pay_config_id'] = $pay_config_id;
                $reDataArr['pay_method'] = $pay_method;

                // 根据报名用户id,及收款账号和收款方式 获得报名用户及支付信息
                list($payMethodInfo, $dataPanyConfigList, $pay_configs_format, $companyKV) = CTAPICourseOrderStaffBusiness::getMethodInfoAndStaffList($request, $this, $id, $company_id, $pay_config_id, $pay_method);
                $reDataArr['method_info'] = $payMethodInfo;
                $reDataArr['config_staff_list'] = $dataPanyConfigList;
                $reDataArr['company_kv'] = $companyKV;
                $reDataArr['pay_config_format'] = $pay_configs_format;

                $reDataArr['info'] = $info;
                // 收款开通类型(1现金、2微信支付、4支付宝)
                // $reDataArr['payMethod'] =  CTAPIOrderPayMethodBusiness::getListKV($request, $this, ['key' => 'pay_method', 'val' => 'pay_name']);
                // $reDataArr['defaultPayMethod'] = $info['pay_method'] ?? -1;// 列表页默认状态
                // pr($reDataArr);
            });
    }

    /**
     * ajax保存数据--缴费生成订单
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_create_order(Request $request)
    {
//        $this->InitParams($request);
        $pageNum = 0;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request){
                $id = CommonRequest::get($request, 'id');
                if(is_string($id)) $id = explode(',', $id);
                if(!is_array($id)) $id = [];
                if(empty($id)) throws('请选择要缴费的学员');
                $info = [
                    'id'=> implode(',', $id),
                    //   'department_id' => 0,
                ];
//                $course_id = CommonRequest::getInt($request, 'course_id');
//                $class_id = CommonRequest::getInt($request, 'class_id');
                $company_id = $this->user_id;//CommonRequest::getInt($request, 'company_id');// 报名用户所属的企业id
                $pay_config_id = CommonRequest::getInt($request, 'pay_config_id');// 支付配置id
                $pay_method = CommonRequest::getInt($request, 'pay_method');// 选择的缴费方式
                $auth_code = CommonRequest::get($request, 'auth_code');// 扫码枪扫的付款码
                $total_price_discount = 0;// CommonRequest::get($request, 'total_price_discount');// 商品下单时优惠金额
                $reDataArr['pay_config_id'] = $pay_config_id;
                $reDataArr['pay_method'] = $pay_method;
                // $reDataArr['auth_code'] = $auth_code;

                // 根据报名用户id,及收款账号和收款方式 获得报名用户及支付信息
                list($payMethodInfo, $dataPanyConfigList, $pay_configs_format, $companyKV) = CTAPICourseOrderStaffBusiness::getMethodInfoAndStaffList($request, $this, $id, $company_id, $pay_config_id, $pay_method);
                $reDataArr['method_info'] = $payMethodInfo;
                $reDataArr['config_staff_list'] = $dataPanyConfigList;
                $reDataArr['pay_config_format'] = $pay_configs_format;

                $reDataArr['info'] = $info;

                if(!is_numeric($total_price_discount) || $total_price_discount < 0) $total_price_discount = 0;
                $otherParams = [
                   'total_price_discount' => $total_price_discount,// 商品下单时优惠金额
                   'payment_amount' => CommonRequest::get($request, 'payment_amount'),// 总支付金额
                   'change_amount' => CommonRequest::get($request, 'change_amount'),// 找零金额
                    'remarks' => CommonRequest::get($request, 'remarks'),// 订单备注
                    'auth_code' => CommonRequest::get($request, 'auth_code'),// 扫码枪扫的付款码
                ];
                $operate_type = 2;//  操作类型1用户操作2平台操作
                $organize_id = $this->organize_id;
                // 企业后台--可以操作所有的员工；操作企业【无员工】
                // 企业后台 -- 操作员工，只能操作自己的员工；无操作企业
                // 个人后台--不可进行操作
                if($this->user_type == 2) $organize_id = $this->own_organize_id;
                $resultDatas = CTAPICourseOrderStaffBusiness::createOrderAjax($request, $this, $organize_id, $company_id, $id, $pay_config_id, $pay_method, $otherParams, $operate_type, true);
                return ajaxDataArr(1, $resultDatas, '');
            });
    }

    /**
     * @OA\Get(
     *     path="/api/company/course_order_staff/ajax_info",
     *     tags={"企业后台-面授培训-报名学员"},
     *     summary="报名学员--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="companyQualityControlCourseOrderStaffAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_course_order_staff_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_course_order_staff"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_course_order_staff"}
     */
    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_info(Request $request){
////        $this->InitParams($request);
////        $id = CommonRequest::getInt($request, 'id');
////        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
////        $info = CTAPICourseOrderStaffBusiness::getInfoData($request, $this, $id, [], '', []);
////        $resultDatas = ['info' => $info];
////        return ajaxDataArr(1, $resultDatas, '');
//
//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id],
//            function (&$reDataArr, &$id) use ($request){
//            $extParams = [
//                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                'relationFormatConfigs'=> CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $this,
//                    ['company_name' => '', 'course_name' =>'', 'class_name' =>'', 'staff_info' =>'', 'course_order_info' => ''], []),
//                'listHandleKeyArr' => ['priceIntToFloat'],
//            ];
//            $info = CTAPICourseOrderStaffBusiness::getFVFormatList( $request,  $this, 4, 1
//                , ['id' => $id, 'company_id' => $this->user_id], false, [], $extParams);
//            $reDataArr['info'] = $info;
//            // pr($reDataArr);
//        });
//    }

    /**
     * @OA\Post(
     *     path="/api/company/course_order_staff/ajax_save",
     *     tags={"企业后台-面授培训-报名学员"},
     *     summary="报名学员--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="companyQualityControlCourseOrderStaffAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_course_order_staff_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_course_order_staff"}
     */

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_save(Request $request)
//    {
////        $this->InitParams($request);
//
//        $id = CommonRequest::getInt($request, 'id');
//        $pageNum = ($id > 0) ? 256 : 32;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
//            , '', [], function (&$reDataArr) use ($request){
//                $id = CommonRequest::getInt($request, 'id');
//                // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                $industry_name = CommonRequest::get($request, 'industry_name');
//                $simple_name = CommonRequest::get($request, 'simple_name');
//                $sort_num = CommonRequest::getInt($request, 'sort_num');
//
//                $saveData = [
//                    'industry_name' => $industry_name,
//                    'simple_name' => $simple_name,
//                    'sort_num' => $sort_num,
//                ];
//                 // 价格转为整型
//                Tool::bathPriceCutFloatInt($saveData, CourseOrderStaff::$IntPriceFields, CourseOrderStaff::$IntPriceIndex, 1);
//
////        if($id <= 0) {// 新加;要加入的特别字段
////            $addNewData = [
////                // 'account_password' => $account_password,
////            ];
////            $saveData = array_merge($saveData, $addNewData);
////        }
//                $extParams = [
//                    'judgeDataKey' => 'replace',// 数据验证的下标
//                ];
//                $resultDatas = CTAPICourseOrderStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
//                return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * @OA\Get(
     *     path="/api/company/course_order_staff/ajax_alist",
     *     tags={"企业后台-面授培训-报名学员"},
     *     summary="报名学员--列表",
     *     description="报名学员--列表......",
     *     operationId="companyQualityControlCourseOrderStaffAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_course_order_staff_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_course_order_staff"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_course_order_staff"}
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
//        return  CTAPICourseOrderStaffBusiness::getList($request, $this, 2 + 4);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            // 根据条件获得项目列表数据
            $mergeParams = [
                'company_id' => $this->user_id,
            ];
            CTAPICourseOrderStaffBusiness::mergeRequest($request, $this, $mergeParams);
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $this,
                    ['company_name' => '', 'course_name' =>'', 'class_name' =>'', 'staff_info' => ['resource_list' => ''], 'course_order_info' => ''] , []),
                'listHandleKeyArr' => ['priceIntToFloat'],

            ];
            return  CTAPICourseOrderStaffBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
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
//        $result = CTAPICourseOrderStaffBusiness::getList($request, $this, 1 + 0);
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
//        CTAPICourseOrderStaffBusiness::getList($request, $this, 1 + 0);
        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){

            // 根据条件获得项目列表数据
            $mergeParams = [
                'company_id' => $this->user_id,
            ];
            CTAPICourseOrderStaffBusiness::mergeRequest($request, $this, $mergeParams);
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $this,
                    ['company_name' => '', 'course_name' =>'', 'class_name' =>'', 'staff_info' => ['resource_list' => ''], 'course_order_info' => ''], []),
                'listHandleKeyArr' => ['priceIntToFloat'],
            ];
            CTAPICourseOrderStaffBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
        });
    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import_template(Request $request){
//        $this->InitParams($request);
//        CTAPICourseOrderStaffBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @OA\Post(
     *     path="/api/company/course_order_staff/ajax_del",
     *     tags={"企业后台-面授培训-报名学员"},
     *     summary="报名学员--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="companyQualityControlCourseOrderStaffAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_course_order_staff_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_course_order_staff"}
     */
    /**
     * 子帐号管理-删除
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_del(Request $request)
//    {
////        $this->InitParams($request);
////        return CTAPICourseOrderStaffBusiness::delAjax($request, $this);
//
//        $tem_id = CommonRequest::get($request, 'id');
//        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
//        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
//
//            $id = CommonRequest::getInt($request, 'id');
//            $info = CTAPICourseOrderStaffBusiness::getInfoDataBase($request, $this, '', $id, [], '', 1);
//            if(empty($info)) throws('记录不存在！');
//            // $user_info = $this->user_info;
//            if( $info['company_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');
//            return CTAPICourseOrderStaffBusiness::delAjax($request, $this);
//        });
//    }

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
//        $childKV = CTAPICourseOrderStaffBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPICourseOrderStaffBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
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
//        $resultDatas = CTAPICourseOrderStaffBusiness::importByFile($request, $this, $fileName);
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
//    public function import(Request $request)
//    {
//        $this->InitParams($request);
//        // 上传并保存文件
//        $result = Resource::fileSingleUpload($request, $this, 1);
//        if($result['apistatus'] == 0) return $result;
//        // 文件上传成功
//        $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//        $resultDatas = CTAPICourseOrderStaffBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPICourseOrderStaffBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * 报名人员管理-(作废/取消作废)
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_frozen(Request $request)
//    {
//        $this->InitParams($request);
//        $id = CommonRequest::get($request, 'id');// 单个id 或 逗号分隔的多个，或 多个的一维数组
//        if(is_array($id)) $id = implode(',', $id);
//        $staff_status = CommonRequest::getInt($request, 'staff_status');// 操作类型 状态 1正常--取消作废操作； 4已作废--作废操作
//        if(in_array($staff_status, [1]))throws('不可以进行取消作废操作！');
//        $organize_id = $this->organize_id;
//        // 企业后台--可以操作所有的员工；操作企业【无员工】
//        // 企业后台 -- 操作员工，只能操作自己的员工；无操作企业
//        // 个人后台--不可进行操作
//        if($this->user_type == 2) $organize_id = $this->own_organize_id;
//        $modifyNum = CTAPICourseOrderStaffBusiness::staffStatusAjax($request, $this, $organize_id, $id, $staff_status);
//        return ajaxDataArr(1, ['modify_num' => $modifyNum], '');
//    }
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

        $reDataArr['class_id'] = CommonRequest::getInt($request, 'class_id');

        // 获得课程键值
        $course_id = CommonRequest::getInt($request, 'course_id');// $hiddenOption = 2
        $reDataArr['course_kv'] = CTAPICourseBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'course_name'], []);
        $reDataArr['defaultCourseId'] = (!is_numeric($course_id) || $course_id <= 0 ) ? -1 : $course_id;// 默认

        // 缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
        $reDataArr['payStatus'] =  CourseOrderStaff::$payStatusArr;
        $reDataArr['defaultPayStatus'] = -1;// 列表页默认状态

        // 分班状态(1待分班、4已分班)
        $join_class_status = CommonRequest::getInt($request, 'join_class_status');// $hiddenOption = 4
        $reDataArr['joinClassStatus'] =  CourseOrderStaff::$joinClassStatusArr;
        $reDataArr['defaultJoinClassStatus'] = (!is_numeric($join_class_status) || $join_class_status <= 0 ) ? -1 : $join_class_status;// 列表页默认状态

        // 人员状态1正常4已作废8已结业
        $reDataArr['staffStatus'] =  CourseOrderStaff::$staffStatusArr;
        $reDataArr['defaultStaffStatus'] = -1;// 列表页默认状态

        $company_id = $this->user_id;//CommonRequest::getInt($request, 'company_id');
        $info = [];

        $company_hidden = 0;
        if(is_numeric($company_id) && $company_id > 0){
            // 获得企业信息
            $companyInfo = CTAPIStaffBusiness::getInfoData($request, $this, $company_id);
            if(empty($companyInfo)) throws('企业信息不存在！');
            $info['company_id'] = $company_id;
            $info['user_company_name'] = $companyInfo['company_name'] ?? '';
            $company_hidden = 1;
        }
        $reDataArr['info'] = $info;
        $reDataArr['company_hidden'] = $company_hidden;// =1 : 隐藏企业选择

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
        ];
        $operate = "添加";

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPICourseOrderStaffBusiness::getRelationConfigs($request, $this,
                    ['company_name' => '', 'course_name' =>'', 'class_name' =>'', 'staff_info' => ['resource_list' => ''], 'course_order_info' => ''], []),
                'listHandleKeyArr' => ['priceIntToFloat'],

            ];
            $info = CTAPICourseOrderStaffBusiness::getInfoData($request, $this, $id, [], '', $extParams);
            // $user_info = $this->user_info;
            if( $info['company_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

        // 获得课程键值
        $course_id = CommonRequest::getInt($request, 'course_id');// $hiddenOption = 2
        $reDataArr['course_kv'] = CTAPICourseBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'course_name'], []);
        $reDataArr['defaultCourseId'] = $info['course_id'] ?? ((!is_numeric($course_id) || $course_id <= 0 ) ? -1 : $course_id);// 默认

        // 缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
        $reDataArr['payStatus'] =  CourseOrderStaff::$payStatusArr;
        $reDataArr['defaultPayStatus'] = $info['pay_status'] ?? -1;// 列表页默认状态

        // 分班状态(1待分班、4已分班)
        $join_class_status = CommonRequest::getInt($request, 'join_class_status');// $hiddenOption = 4
        $reDataArr['joinClassStatus'] =  CourseOrderStaff::$joinClassStatusArr;
        $reDataArr['defaultJoinClassStatus'] = $info['join_class_status'] ?? ((!is_numeric($join_class_status) || $join_class_status <= 0 ) ? -1 : $join_class_status);// 列表页默认状态

        // 人员状态1正常4已作废8已结业
        $reDataArr['staffStatus'] =  CourseOrderStaff::$staffStatusArr;
        $reDataArr['defaultStaffStatus'] = $info['staff_status'] ?? -1;// 列表页默认状态

        $company_hidden = CommonRequest::getInt($request, 'company_hidden');
        $reDataArr['company_hidden'] = $company_hidden;// =1 : 隐藏企业选择

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
