<?php

namespace App\Http\Controllers\WebFront\Web\QualityControl\Certificate;

use App\Business\Controller\API\QualityControl\CTAPICertificateScheduleBusiness;
use App\Business\Controller\API\QualityControl\CTAPICitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPIIndustryBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class CertificateScheduleController extends BasicController
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
//            return view('web.QualityControl.CertificateSchedule.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'web.QualityControl.CertificateSchedule.index', false
            , 'doListPage', [], function (&$reDataArr) use ($request){

            });
    }

    /**
     *  列表页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function list(Request $request)
//    {
//        return $this->exeDoPublicFun($request, 1, 1, 'web.QualityControl.CertificateSchedule.list', false
//            , 'doListPage', [], function (&$reDataArr) use ($request){
//
//            });
//    }

    /**
     *  列表页--企业的
     * ?field=&keyword=
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function company(Request $request, $city_id = 0, $industry_id = 0, $pagesize = 20, $page = 1)
    {
        return $this->exeDoPublicFun($request, 0, 8, 'web.QualityControl.CertificateSchedule.company', false
            , '', [], function (&$reDataArr) use ($request, &$city_id, &$industry_id, &$pagesize, &$page){

            $keyArr = CTAPIStaffBusiness::getCompanyList($request, $this, $reDataArr, 'jigou/list/', $city_id, $industry_id, $pagesize, $page, 1);
            $reDataArr['key_str'] = implode(',', $keyArr);

            // 获得最新更新企业
            $company_update_list = CTAPIStaffBusiness::getFVFormatList( $request,  $this, 2, 20
                ,  ['admin_type' => 2], false,[]
                , [
                    'sqlParams' => [
                        'where' => [['is_perfect', 2], ['open_status', 2], ['account_status', 1]],
                        'orderBy' => ['updated_at' => 'desc', 'id' => 'desc']
                    ]
                ]);
            $reDataArr['company_update_list'] = $company_update_list;

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
////        $reDataArr = [];// 可以传给视图的全局变量数组
////        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
////            // 正常流程的代码
////
////            $this->InitParams($request);
////            // $reDataArr = $this->reDataArr;
////            $reDataArr = array_merge($reDataArr, $this->reDataArr);
////            $reDataArr['province_kv'] = CTAPICertificateScheduleBusiness::getCityByPid($request, $this,  0);
////            $reDataArr['province_kv'] = CTAPICertificateScheduleBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
////            $reDataArr['province_id'] = 0;
////            return view('web.QualityControl.CertificateSchedule.select', $reDataArr);
////
////        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'web.QualityControl.CertificateSchedule.select', true
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
////            return view('web.QualityControl.CertificateSchedule.add', $reDataArr);
////
////        }, $this->errMethod, $reDataArr, $this->errorView);
//
//        $pageNum = ($id > 0) ? 64 : 16;
//        return $this->exeDoPublicFun($request, $pageNum, 1,'web.QualityControl.CertificateSchedule.add', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//            });
//    }

    /**
     * 详情页
     *
     * @param Request $request
     * @param int $company_id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function info(Request $request,$id = 0)
    {
	Tool::phpInitSet();
        return $this->exeDoPublicFun($request, 17179869184, 1,'web.QualityControl.CertificateSchedule.info', false
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

            });
    }

    /**
     * @OA\Get(
     *     path="/api/web/quality_control/certificate_schedule/ajax_info",
     *     tags={"质量认证-资质认定"},
     *     summary="资质认定--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="adminQualityControlCertificateScheduleAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_certificate_schedule_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_certificate_schedule"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_certificate_schedule"}
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
////        $info = CTAPICertificateScheduleBusiness::getInfoData($request, $this, $id, [], '', []);
////        $resultDatas = ['info' => $info];
////        return ajaxDataArr(1, $resultDatas, '');
//
//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
//    }

    /**
     * @OA\Post(
     *     path="/api/web/quality_control/certificate_schedule/ajax_save",
     *     tags={"质量认证-资质认定"},
     *     summary="资质认定--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="adminQualityControlCertificateScheduleAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_certificate_schedule_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_certificate_schedule"}
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
////        $id = CommonRequest::getInt($request, 'id');
////        // CommonRequest::judgeEmptyParams($request, 'id', $id);
////        $type_name = CommonRequest::get($request, 'type_name');
////        $sort_num = CommonRequest::getInt($request, 'sort_num');
////
////        $saveData = [
////            'type_name' => $type_name,
////            'sort_num' => $sort_num,
////        ];
////
//////        if($id <= 0) {// 新加;要加入的特别字段
//////            $addNewData = [
//////                // 'account_password' => $account_password,
//////            ];
//////            $saveData = array_merge($saveData, $addNewData);
//////        }
////        $extParams = [
////            'judgeDataKey' => 'replace',// 数据验证的下标
////        ];
////        $resultDatas = CTAPICertificateScheduleBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
////        return ajaxDataArr(1, $resultDatas, '');
//
//        $id = CommonRequest::getInt($request, 'id');
//        $pageNum = ($id > 0) ? 256 : 32;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
//            , '', [], function (&$reDataArr) use ($request){
//                $id = CommonRequest::getInt($request, 'id');
//                // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                $company_id = CommonRequest::getInt($request, 'company_id');
//                $certificate_no = CommonRequest::get($request, 'certificate_no');
//                $addr = CommonRequest::get($request, 'addr');
//                $ratify_date = CommonRequest::get($request, 'ratify_date');
//                $valid_date = CommonRequest::get($request, 'valid_date');
//                $category_name = CommonRequest::get($request, 'category_name');
//                $project_name = CommonRequest::get($request, 'project_name');
//                $param_name = CommonRequest::get($request, 'param_name');
//                $method_name = CommonRequest::get($request, 'method_name');
//                $limit_range = CommonRequest::get($request, 'limit_range');
//                $explain_text = CommonRequest::get($request, 'explain_text');
//                // 判断开始结束日期
//                Tool::judgeBeginEndDate($ratify_date, $valid_date, 1 + 2 + 256 + 512, 1, date('Y-m-d'), '有效起止日期');
//
//                $saveData = [
//                    'company_id' => $company_id,
//                    'certificate_no' => $certificate_no,
//                    'ratify_date' => $ratify_date,
//                    'valid_date' => $valid_date,
//                    'addr' => $addr,
//                    'category_name' => $category_name,
//                    'project_name' => $project_name,
//                    'param_name' => $param_name,
//                    'method_name' => replace_enter_char($method_name, 1),
//                    'limit_range' => replace_enter_char($limit_range, 1),
//                    'explain_text' => replace_enter_char($explain_text, 1),
//                ];
//
//                if($id <= 0) {// 新加;要加入的特别字段
//                    //            $addNewData = [
//                    //                // 'account_password' => $account_password,
//                    //            ];
//                    //            $saveData = array_merge($saveData, $addNewData);
//                }else{
//                    $info = CTAPICertificateScheduleBusiness::getInfoData($request, $this, $id, [], '', []);
//                    // 如果改变了所属企业,需要重新统计数
//                    if(isset($saveData['company_id']) && $company_id != $info['company_id']) $saveData['force_company_num'] = 1;
//                }
//                $extParams = [
//                    'judgeDataKey' => 'replace',// 数据验证的下标
//                ];
//                $resultDatas = CTAPICertificateScheduleBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
//                return ajaxDataArr(1, $resultDatas, '');
//            });
//    }

    /**
     * @OA\Get(
     *     path="/api/web/quality_control/certificate_schedule/ajax_alist",
     *     tags={"质量认证-资质认定"},
     *     summary="资质认定--列表",
     *     description="资质认定--列表......",
     *     operationId="adminQualityControlCertificateScheduleAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_certificate_schedule_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_certificate_schedule"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_certificate_schedule"}
     */
    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_alist(Request $request){
////        $this->InitParams($request);
////        return  CTAPICertificateScheduleBusiness::getList($request, $this, 2 + 4);
//        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $handleKeyConfigArr = ['company_info' => '','certificate_info' => ''];
//            $extParams = [
//                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                'relationFormatConfigs'=> CTAPICertificateScheduleBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
//            ];
//            return  CTAPICertificateScheduleBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
//        });
//    }

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
////        $this->InitParams($request);
////        $result = CTAPICertificateScheduleBusiness::getList($request, $this, 1 + 0);
////        $data_list = $result['result']['data_list'] ?? [];
////        $ids = implode(',', array_column($data_list, 'id'));
////        return ajaxDataArr(1, $ids, '');
//        return $this->exeDoPublicFun($request, 4294967296, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $result = CTAPICertificateScheduleBusiness::getList($request, $this, 1 + 0);
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
////        $this->InitParams($request);
////        CTAPICertificateScheduleBusiness::getList($request, $this, 1 + 0);
//        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            $handleKeyConfigArr = ['company_info' => '','certificate_info' => ''];
//            $extParams = [
//                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                'relationFormatConfigs'=> CTAPICertificateScheduleBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
//            ];
//            CTAPICertificateScheduleBusiness::getList($request, $this, 1 + 0);
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
////        $this->InitParams($request);
////        CTAPICertificateScheduleBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPICertificateScheduleBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @OA\Post(
     *     path="/api/web/quality_control/certificate_schedule/ajax_del",
     *     tags={"质量认证-资质认定"},
     *     summary="资质认定--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="adminQualityControlCertificateScheduleAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_certificate_schedule_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_certificate_schedule"}
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
////        return CTAPICertificateScheduleBusiness::delAjax($request, $this);
//        $tem_id = CommonRequest::get($request, 'id');
//        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
//        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $organize_id = CommonRequest::getInt($request, 'company_id');// 可有此参数
//            return CTAPICertificateScheduleBusiness::delDatasAjax($request, $this, $organize_id);
//            // return CTAPICertificateScheduleBusiness::delAjax($request, $this);
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
////        $this->InitParams($request);
////        $parent_id = CommonRequest::getInt($request, 'parent_id');
////        // 获得一级城市信息一维数组[$k=>$v]
////        $childKV = CTAPICertificateScheduleBusiness::getCityByPid($request, $this, $parent_id);
////        // $childKV = CTAPICertificateScheduleBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
////
////        return  ajaxDataArr(1, $childKV, '');
//        return $this->exeDoPublicFun($request, 8589934592, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $parent_id = CommonRequest::getInt($request, 'parent_id');
//            // 获得一级城市信息一维数组[$k=>$v]
//            $childKV = CTAPICertificateScheduleBusiness::getCityByPid($request, $this, $parent_id);
//            // $childKV = CTAPICertificateScheduleBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//            return  ajaxDataArr(1, $childKV, '');
//        });
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
////        $this->InitParams($request);
////        $fileName = 'staffs.xlsx';
////        $resultDatas = CTAPICertificateScheduleBusiness::importByFile($request, $this, $fileName);
////        return ajaxDataArr(1, $resultDatas, '');
///
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $fileName = 'staffs.xlsx';
//            $resultDatas = CTAPICertificateScheduleBusiness::importByFile($request, $this, $fileName);
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
////        $this->InitParams($request);
////        // 上传并保存文件
////        $result = Resource::fileSingleUpload($request, $this, 1);
////        if($result['apistatus'] == 0) return $result;
////        // 文件上传成功
////        $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
////        $resultDatas = CTAPICertificateScheduleBusiness::importByFile($request, $this, $fileName);
////        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPICertificateScheduleBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    // **************公用重写方法********************开始*********************************
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
        // $user_info = $this->user_info;
        // $id = $extendParams['params']['id'];

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

//        $company_id = CommonRequest::getInt($request, 'company_id');
//        $info = [];
//        $company_hidden = 0;
//        if(is_numeric($company_id) && $company_id > 0){
//            // 获得企业信息
//            $companyInfo = CTAPIStaffBusiness::getInfoData($request, $this, $company_id);
//            if(empty($companyInfo)) throws('企业信息不存在！');
//            $info['company_id'] = $company_id;
//            $info['user_company_name'] = $companyInfo['company_name'] ?? '';
//            $company_hidden = 1;
//        }
//        $reDataArr['info'] = $info;
//        $reDataArr['company_hidden'] = $company_hidden;// =1 : 隐藏企业选择
        // 获得企业总数
        $company_count = CTAPIStaffBusiness::getFVFormatList( $request,  $this, 8, 1
            ,  ['admin_type' => 2], false,[], ['sqlParams' => ['where' => [['is_perfect', 2], ['open_status', 2], ['account_status', 1]]]]);

        $reDataArr['company_count'] = $company_count;
        // 获得最新注册企业
        $company_new_list = CTAPIStaffBusiness::getFVFormatList( $request,  $this, 2, 9
            ,  ['admin_type' => 2], false,[]
            , [
                'sqlParams' => [
                    'where' => [['is_perfect', 2], ['open_status', 2], ['account_status', 1]],
                    'orderBy' => CTAPIStaffBusiness::$orderBy
                ]
            ]);
        foreach($company_new_list as $k => $v){
            $company_new_list[$k]['created_at_fmt'] = judgeDate($v['created_at'],'Y-m-d');
        }
        $reDataArr['company_new_list'] = $company_new_list;
        // 获得最新更新企业
        $company_update_list = CTAPIStaffBusiness::getFVFormatList( $request,  $this, 2, 9
            ,  ['admin_type' => 2], false,[]
            , [
                'sqlParams' => [
                    'where' => [['is_perfect', 2], ['open_status', 2], ['account_status', 1]],
                    'orderBy' => ['updated_at' => 'desc', 'id' => 'desc']
                ]
            ]);
        foreach($company_update_list as $k => $v){
            $company_update_list[$k]['updated_at_fmt'] = judgeDate($v['updated_at'],'Y-m-d');
        }
        $reDataArr['company_update_list'] = $company_update_list;
        // 行业及企业数量

        $industry_list = CTAPIIndustryBusiness::exeDBBusinessMethodCT($request, $this, '',  'getCompanyNumGroup', [], 1, 1);
        $reDataArr['industry_list'] = $industry_list;
        // 地区分布及企业数量
        $city_list = CTAPICitysBusiness::exeDBBusinessMethodCT($request, $this, '',  'getCompanyNumGroup', [], 1, 1);
        $reDataArr['city_list'] = $city_list;

        $reDataArr['rang_f_type'] = 0;
        $reDataArr['field'] = '';
        $reDataArr['qkey'] = 0;

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

        // id是企业id
        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $handleKeyConfigArr = ['certificate_detail', 'user_auth_list', 'certificate_list','industry_info', 'city_info'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];
            $info = CTAPIStaffBusiness::getInfoData($request, $this, $id, [], '', $extParams);
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用重写方法********************结束*********************************
}
