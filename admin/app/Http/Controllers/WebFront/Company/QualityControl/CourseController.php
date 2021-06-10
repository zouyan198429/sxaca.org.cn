<?php

namespace App\Http\Controllers\WebFront\Company\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICourseBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseOrderBusiness;
use App\Business\Controller\API\QualityControl\CTAPICourseOrderStaffBusiness;
use App\Business\Controller\API\QualityControl\CTAPIInvoiceBuyerBusiness;
use App\Business\Controller\API\QualityControl\CTAPIOrderPayConfigBusiness;
use App\Business\Controller\API\QualityControl\CTAPIOrderPayMethodBusiness;
use App\Business\Controller\API\QualityControl\CTAPIResourceBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\Course;
use App\Models\QualityControl\OrderPayConfig;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class CourseController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com) 下载
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
//            return view('company.QualityControl.Course.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'company.QualityControl.Course.index', true
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
//            $reDataArr['province_kv'] = CTAPICourseBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPICourseBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('company.QualityControl.Course.select', $reDataArr);
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
////            return view('company.QualityControl.Course.add', $reDataArr);
////
////        }, $this->errMethod, $reDataArr, $this->errorView);
//
//        $pageNum = ($id > 0) ? 64 : 16;
//        return $this->exeDoPublicFun($request, $pageNum, 1,'company.QualityControl.Course.add', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
//    }

    /**
     * 添加-报名
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function join(Request $request,$id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('company.QualityControl.Course.join', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'company.QualityControl.Course.join', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request, &$id){
            });
    }

    /**
     * ajax获得添加用户
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_add_user(Request $request){
        return $this->exeDoPublicFun($request, 0, 4,'', true, '', [], function (&$reDataArr) use ($request){
            $courseId = CommonRequest::getInt($request, 'course_id');
            $userIds = CommonRequest::get($request, 'user_ids');
            CTAPICourseOrderBusiness::getCourseStaff( $request, $this, $reDataArr, $courseId, $company_id = $this->own_organize_id, $userIds);
            $dataList = $reDataArr['staff_list'] ?? [];
            return ajaxDataArr(1, $dataList, '');
        });
    }

    /**
     * @OA\Get(
     *     path="/api/company/course/ajax_info",
     *     tags={"企业后台-面授培训-课程管理"},
     *     summary="课程管理--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="companyQualityControlCourseAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_course_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_course"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_course"}
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
        $info = CTAPICourseBusiness::getInfoData($request, $this, $id, [], '', []);
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
     *     path="/api/company/course/ajax_save",
     *     tags={"企业后台-面授培训-课程管理"},
     *     summary="课程管理--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="companyQualityControlCourseAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_course_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_course"}
     */
//
//    /**
//     * ajax保存数据
//     *
//     * @param int $id
//     * @return Response
//     * @author zouyan(305463219@qq.com)
//     */
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
//                $course_name = CommonRequest::get($request, 'course_name');
//                $explain_remarks = CommonRequest::get($request, 'explain_remarks');
//                $course_content = CommonRequest::get($request, 'course_content');
//                $course_content = stripslashes($course_content);
//                $price_member = CommonRequest::get($request, 'price_member');
//                $price_general = CommonRequest::get($request, 'price_general');
//                $status_online = CommonRequest::getInt($request, 'status_online');
//                $pay_config_id = CommonRequest::getInt($request, 'pay_config_id');
//                // 开通的付款方式
//                $pay_method = CommonRequest::get($request, 'pay_method');
//                if(!is_array($pay_method) && is_string($pay_method)){// 转为数组
//                    $pay_method = explode(',',$pay_method);
//                }
//
////                $pay_method_ids = implode(',', $pay_method);
////                if(!empty($pay_method_ids)) $pay_method_ids = ',' . $pay_method_ids . ',';
//                $sel_pay_method = 0;
//                Tool::arrClsEmpty($pay_method);
//                foreach($pay_method as $tem_pay_method){
//                    $sel_pay_method = $sel_pay_method | $tem_pay_method;
//                }
//
//                // 图片资源
//                $resource_id = CommonRequest::get($request, 'resource_id');
//                // 如果是字符，则转为数组
//                if(is_string($resource_id) || is_numeric($resource_id)){
//                    if(strlen(trim($resource_id)) > 0){
//                        $resource_id = explode(',' ,$resource_id);
//                    }
//                }
//                if(!is_array($resource_id)) $resource_id = [];
//
//                // 再转为字符串
//                $resource_ids = implode(',', $resource_id);
//                if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';
//
//
//                $saveData = [
//                    'course_name' => $course_name,
//                    'pay_config_id' => $pay_config_id,
//                    'pay_method' => $sel_pay_method,
//                    'explain_remarks' => replace_enter_char($explain_remarks, 1),
//                    'price_member' => $price_member,
//                    'price_general' => $price_general,
//                    'status_online' => $status_online,
//                    'course_content' => $course_content,// 详细说明
//                    'resource_id' => $resource_id[0] ?? 0,// 第一个图片资源的id
//                    'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
//                    'resourceIds' => $resource_id,// 此下标为图片资源关系
//                ];
//                 // 价格转为整型
//                Tool::bathPriceCutFloatInt($saveData, Course::$IntPriceFields, Course::$IntPriceIndex, 1);

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
//                $resultDatas = CTAPICourseBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
//                return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_join_save(Request $request)
    {
//        $this->InitParams($request);

        $id = CommonRequest::getInt($request, 'id');
        $pageNum = ($id > 0) ? 256 : 32;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request){
                $id = CommonRequest::getInt($request, 'id');
                // CommonRequest::judgeEmptyParams($request, 'id', $id);
                $contacts = CommonRequest::get($request, 'contacts');
                $tel = CommonRequest::get($request, 'tel');
                $invoice_buyer_id = CommonRequest::getInt($request, 'invoice_buyer_id');
                // 学员信息
                $staff_ids = CommonRequest::get($request, 'staff_ids');
                // 如果是字符，则转为数组
                if(is_string($staff_ids) || is_numeric($staff_ids)){
                    //if(strlen(trim($staff_ids)) > 0){
                        $staff_ids = explode(',' ,$staff_ids);
                    //}
                }
                if(!is_array($staff_ids)) $staff_ids = [];
                // 证书所属单位信息
                $certificate_company = CommonRequest::get($request, 'certificate_company');
                // 如果是字符，则转为数组
                if(is_string($certificate_company) || is_numeric($certificate_company)){
                    //if(strlen(trim($certificate_company)) > 0){
                        $certificate_company = explode(',' ,$certificate_company);
                   // }
                }
                if(!is_array($certificate_company)) $certificate_company = [];

                $resultDatas = CTAPICourseOrderBusiness::courseJoin($request, $this, $id, $staff_ids, [
                    'contacts' => $contacts,
                    'tel' => $tel,
                    'certificate_company' => $certificate_company,
                ]);
                return ajaxDataArr(1, $resultDatas, '');
            });
    }

    /**
     * @OA\Get(
     *     path="/api/company/course/ajax_alist",
     *     tags={"企业后台-面授培训-课程管理"},
     *     summary="课程管理--列表",
     *     description="课程管理--列表......",
     *     operationId="companyQualityControlCourseAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_course_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_course"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_course"}
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
//        return  CTAPICourseBusiness::getList($request, $this, 2 + 4);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            $extendParams = [
                'course_order_company' => [
//                    'fieldValParams' => [
//                        'admin_type' => $this->user_info['admin_type'],
//                        'company_id'=> $this->user_info['id']
//                    ],
                    'sqlParams' => [
                        'where' => [
                            ['admin_type', $this->user_info['admin_type']],
                            ['company_id', $this->user_info['id']],
                        ]
                    ]
                ]
            ];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPICourseBusiness::getRelationConfigs($request, $this, ['resource_list' => '', 'course_order_company' => ''], $extendParams),
                // 'infoHandleKeyArr' => ['resetPayMethod'],
                'listHandleKeyArr' => ['initPayMethodText', 'priceIntToFloat'],
                'sqlParams' => ['where' => [['status_online', 1]]]
            ];
            return  CTAPICourseBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
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
//        $result = CTAPICourseBusiness::getList($request, $this, 1 + 0);
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
//    public function export(Request $request){
//        $this->InitParams($request);
//        CTAPICourseBusiness::getList($request, $this, 1 + 0);
//        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0);
//        });
//    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import_template(Request $request){
//        $this->InitParams($request);
//        CTAPICourseBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @OA\Post(
     *     path="/api/company/course/ajax_del",
     *     tags={"企业后台-面授培训-课程管理"},
     *     summary="课程管理--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="companyQualityControlCourseAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_course_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_course"}
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
////        return CTAPICourseBusiness::delAjax($request, $this);
//
//        $tem_id = CommonRequest::get($request, 'id');
//        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
//        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // return CTAPICourseBusiness::delAjax($request, $this);
//            $organize_id = 0;//CommonRequest::getInt($request, 'company_id');// 可有此参数
//            return CTAPICourseBusiness::delCustomizeAjax($request,  $this, $organize_id, [], '');
//
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
//        $childKV = CTAPICourseBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPICourseBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
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
//        $resultDatas = CTAPICourseBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPICourseBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPICourseBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * 单文件上传-上传文件
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function up_file(Request $request)
//    {
//        $this->InitParams($request);
//        // $this->company_id = 1;
//        // 企业 的 个人--只能读自己的人员信息
////        $organize_id = $this->user_id;// CommonRequest::getInt($request, 'company_id');
////        if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
////
////        $userInfo = $this->getStaffInfo($organize_id);
////        if(empty($userInfo)) throws('企业记录不存在！');
//
//        // 上传并保存文件
//        return CTAPIResourceBusiness::filePlupload($request, $this, 1);//  | 2 | 8 | 16
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

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

        // 状态(1正常(报名中)  2下架)
        $reDataArr['statusOnline'] =  Course::$statusOnlineArr;
        $reDataArr['defaultStatusOnline'] = -1;// 列表页默认状态

        // 获得收款帐号KV值
        $reDataArr['pay_config_kv'] = CTAPIOrderPayConfigBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'pay_company_name'], [
            'sqlParams' => ['where' => [['open_status', 1]]]
        ]);
        $reDataArr['defaultPayConfig'] = -1;// 默认

        // 收款开通类型(1现金、2微信支付、4支付宝)
        $reDataArr['payMethod'] =  CTAPIOrderPayMethodBusiness::getListKV($request, $this, ['key' => 'pay_method', 'val' => 'pay_name']);
        $reDataArr['defaultPayMethod'] = -1;// 列表页默认状态
        // $reDataArr['payMethodDisable'] = OrderPayConfig::$payMethodDisable;// 不可用的--禁用
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
            $extendParams = [
                'course_order_company' => [
//                    'fieldValParams' => [
//                        'admin_type' => $this->user_info['admin_type'],
//                        'company_id'=> $this->user_info['id']
//                    ],
                    'sqlParams' => [
                        'where' => [
                            ['admin_type', $this->user_info['admin_type']],
                            ['company_id', $this->user_info['id']],
                        ]
                    ]
                ]
            ];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPICourseBusiness::getRelationConfigs($request, $this, ['resource_list' => '', 'course_content' => '', 'course_order_company' => ''], $extendParams),
                // 'infoHandleKeyArr' => ['resetPayMethod'],
                'listHandleKeyArr' => ['initPayMethodText', 'priceIntToFloat'],
                'sqlParams' => ['where' => [['status_online', 1]]]
            ];
            $info = CTAPICourseBusiness::getInfoData($request, $this, $id, [], '', $extParams);

        }

        // $reDataArr = array_merge($reDataArr, $resultDatas);

        // 状态(1正常(报名中)  2下架)
        $reDataArr['statusOnline'] =  Course::$statusOnlineArr;
        $reDataArr['defaultStatusOnline'] = $info['status_online'] ?? -1;// 列表页默认状态

        // 获得收款帐号KV值
        $reDataArr['pay_config_kv'] = CTAPIOrderPayConfigBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'pay_company_name'], [
            'sqlParams' => ['where' => [['open_status', 1]]]
        ]);
        $reDataArr['defaultPayConfig'] = $info['pay_config_id'] ?? -1;// 默认

        // 获得发票抬头KV值
//        $reDataArr['invoice_buyer_kv'] = CTAPIInvoiceBuyerBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'gmf_mc'], [
//            'sqlParams' => ['where' => [['open_status', 1], ['company_id', $this->user_id]]]
//        ]);
//        $reDataArr['defaultInvoiceBuyer'] = $info['invoice_buyer_id'] ?? -1;// 默认

        // 收款开通类型(1现金、2微信支付、4支付宝)
        $reDataArr['payMethod'] =  CTAPIOrderPayMethodBusiness::getListKV($request, $this, ['key' => 'pay_method', 'val' => 'pay_name']);
        $reDataArr['defaultPayMethod'] = $info['pay_method'] ?? -1;// 列表页默认状态
        // $reDataArr['payMethodDisable'] = OrderPayConfig::$payMethodDisable;// 不可用的--禁用

        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

        $reDataArr['hidden_option'] = $hiddenOption;
        // pr($reDataArr);
    }
    // **************公用方法********************结束*********************************

}
