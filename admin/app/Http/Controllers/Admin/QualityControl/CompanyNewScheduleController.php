<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICompanyScheduleBusiness;
use App\Business\Controller\API\QualityControl\CTAPIResourceBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\CompanySchedule;
use App\Services\File\DownFile;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class CompanyNewScheduleController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    /**
     * 测试上传图片
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function test(Request $request,$id = 0)
    {
        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'admin.QualityControl.CompanyNewSchedule.test', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

            });
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
//            return view('admin.QualityControl.CompanyNewSchedule.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

        return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.CompanyNewSchedule.index', true
            , 'doListPage', [], function (&$reDataArr) use ($request){

            });
    }

    /**
     * 列表页--按企业id降序
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function list(Request $request)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('admin.QualityControl.CompanyNewSchedule.list', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.CompanyNewSchedule.list', true
            , 'doListPage', [], function (&$reDataArr) use ($request){

            });
    }

    /**
     * 首页--单个企业显示
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function show(Request $request,$company_id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$company_id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            $info = CTAPIStaffBusiness::getInfoData($request, $this, $company_id, [], '', []);
//            if(empty($info)) throws('记录不存在！');
//            $reDataArr['company_id'] = $company_id;
//            $reDataArr['user_company_name'] = $info['company_name'];
//            return view('admin.QualityControl.CompanyNewSchedule.show', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 0, 8, 'admin.QualityControl.CompanyNewSchedule.show', true
            , '', [], function (&$reDataArr) use ($request, &$company_id){

                $info = CTAPIStaffBusiness::getInfoData($request, $this, $company_id, [], '', []);
                if(empty($info)) throws('记录不存在！');
                $reDataArr['company_id'] = $company_id;
                $reDataArr['user_company_name'] = $info['company_name'];

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
//            $reDataArr['province_kv'] = CTAPICompanyScheduleBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPICompanyScheduleBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('admin.QualityControl.CompanyNewSchedule.select', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'admin.QualityControl.CompanyNewSchedule.select', true
//            , 'doListPage', [], function (&$reDataArr) use ($request){
//
//            });
//    }



    /**
     * 添加 excel
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function add_excel(Request $request,$id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            $info = [
//                'id'=>$id,
//                //   'department_id' => 0,
//            ];
//            $operate = "添加";
//
//            if ($id > 0) { // 获得详情数据
//                $operate = "修改";
//                $info = CTAPICompanyScheduleBusiness::getInfoData($request, $this, $id, [], '', []);
//                if(empty($info)) throws('记录不存在！');
//                // $user_info = $this->user_info;
//                if( $info['company_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');
//
//            }
//            // $reDataArr = array_merge($reDataArr, $resultDatas);
//            $reDataArr['info'] = $info;
//            $reDataArr['operate'] = $operate;
//            return view('admin.QualityControl.CompanyNewSchedule.add_excel', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'admin.QualityControl.CompanyNewSchedule.add_excel', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

            });
    }

    /**
     * 添加
     *
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
//            $info = [
//                'id'=>$id,
//                //   'department_id' => 0,
//            ];
//            $operate = "添加";
//
//            if ($id > 0) { // 获得详情数据
//                $operate = "修改";
//                $info = CTAPICompanyScheduleBusiness::getInfoData($request, $this, $id, [], '', []);
//            }
//            // $reDataArr = array_merge($reDataArr, $resultDatas);
//            $reDataArr['info'] = $info;
//            $reDataArr['operate'] = $operate;
//
//            $reDataArr['type_ids'] = CompanySchedule::$typeIdArr;
//            $reDataArr['defaultTypeId'] = $info['type_id'] ?? -1;// 默认
//
//            return view('admin.QualityControl.CompanyNewSchedule.add', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'admin.QualityControl.CompanyNewSchedule.add', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

            });
    }

    /**
     * @OA\Get(
     *     path="/api/admin/quality_control/company_new_schedule/ajax_info",
     *     tags={"大后台-能力验证-能力附表"},
     *     summary="能力附表--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="adminQualityControlCompanyNewScheduleAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_schedule_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_company_schedule"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_schedule"}
     */
    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_info(Request $request){
//        $this->InitParams($request);
//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        $info = CTAPICompanyScheduleBusiness::getInfoData($request, $this, $id, [], '', []);
//        $resultDatas = ['info' => $info];
//        return ajaxDataArr(1, $resultDatas, '');

        $id = CommonRequest::getInt($request, 'id');
        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

        });
    }

    /**
     * @OA\Post(
     *     path="/api/admin/quality_control/company_new_schedule/ajax_save",
     *     tags={"大后台-能力验证-能力附表"},
     *     summary="能力附表--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="adminQualityControlCompanyNewScheduleAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_schedule_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_schedule"}
     */

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request)
    {
//        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        $pageNum = ($id > 0) ? 256 : 32;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request, $id){
            // CommonRequest::judgeEmptyParams($request, 'id', $id);
            // 企业 的 个人--只能读自己的人员信息
            $organize_id = CommonRequest::getInt($request, 'company_id');
            if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');

            $userInfo = $this->getStaffInfo($organize_id);
            if(empty($userInfo)) throws('企业记录不存在！');

            // word资源
            $resource_id = [];
    //        $resource_id = CommonRequest::get($request, 'resource_id');
    //        // 如果是字符，则转为数组
    //        if(is_string($resource_id) || is_numeric($resource_id)){
    //            if(strlen(trim($resource_id)) > 0){
    //                $resource_id = explode(',' ,$resource_id);
    //            }
    //        }
    //        if(!is_array($resource_id)) $resource_id = [];
    //
    //        // 再转为字符串
    //        $resource_ids = implode(',', $resource_id);
    //        if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';

            // pdf资源
            $resource_id_pdf = CommonRequest::get($request, 'resource_id_pdf');
            // 如果是字符，则转为数组
            if(is_string($resource_id_pdf) || is_numeric($resource_id_pdf)){
                if(strlen(trim($resource_id_pdf)) > 0){
                    $resource_id_pdf = explode(',' ,$resource_id_pdf);
                }
            }
            if(!is_array($resource_id_pdf)) $resource_id_pdf = [];

            // 再转为字符串
            $resource_ids_pdf = implode(',', $resource_id_pdf);
            if(!empty($resource_ids_pdf)) $resource_ids_pdf = ',' . $resource_ids_pdf . ',';

            $type_id = CommonRequest::getInt($request, 'type_id');


            $saveData = [
                'company_id' => $organize_id,
                'type_id' => $type_id,
    //             'resource_id' => $resource_id[0] ?? 0,// word资源的id
    //            'resource_ids' => $resource_ids,// word资源id串(逗号分隔-未尾逗号结束)
                'resource_id_pdf' => $resource_id_pdf[0] ?? 0,// pdf资源的id
                'resource_ids_pdf' => $resource_ids_pdf,// pdf资源id串(逗号分隔-未尾逗号结束)
                'resourceIds' => array_merge($resource_id, $resource_id_pdf),// 此下标为图片资源关系
            ];

    //        if($id <= 0) {// 新加;要加入的特别字段
    //            $addNewData = [
    //                // 'account_password' => $account_password,
    //            ];
    //            $saveData = array_merge($saveData, $addNewData);
    //        }
            $extParams = [
                'judgeDataKey' => 'replace',// 数据验证的下标
                'methodName' =>  'replaceByIdNew',
            ];
            $resultDatas = CTAPICompanyScheduleBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
            return ajaxDataArr(1, $resultDatas, '');
        });
    }


    /**
     * ajax保存数据--excel保存
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_excel_save(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');
        $pageNum = ($id > 0) ? 256 : 32;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request, $id){
                // CommonRequest::judgeEmptyParams($request, 'id', $id);
                // 企业 的 个人--只能读自己的人员信息
                $organize_id = CommonRequest::getInt($request, 'company_id');
                if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');

                $userInfo = $this->getStaffInfo($organize_id);
                if(empty($userInfo)) throws('企业记录不存在！');

                // word资源
                //        $resource_id = [];
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

                // pdf资源
                $resource_id_pdf = [];
                //        $resource_id_pdf = CommonRequest::get($request, 'resource_id_pdf');
                //        // 如果是字符，则转为数组
                //        if(is_string($resource_id_pdf) || is_numeric($resource_id_pdf)){
                //            if(strlen(trim($resource_id_pdf)) > 0){
                //                $resource_id_pdf = explode(',' ,$resource_id_pdf);
                //            }
                //        }
                //        if(!is_array($resource_id_pdf)) $resource_id_pdf = [];
                //
                //        // 再转为字符串
                //        $resource_ids_pdf = implode(',', $resource_id_pdf);
                //        if(!empty($resource_ids_pdf)) $resource_ids_pdf = ',' . $resource_ids_pdf . ',';

                $type_id = 0;// CommonRequest::getInt($request, 'type_id');


                $saveData = [
                    'company_id' => $organize_id,
                    'type_id' => $type_id,
                    'resource_id' => $resource_id[0] ?? 0,// word资源的id
                    'resource_ids' => $resource_ids,// word资源id串(逗号分隔-未尾逗号结束)
                    //            'resource_id_pdf' => $resource_id_pdf[0] ?? 0,// pdf资源的id
                    //            'resource_ids_pdf' => $resource_ids_pdf,// pdf资源id串(逗号分隔-未尾逗号结束)
                    'resourceIds' => array_merge($resource_id, $resource_id_pdf),// 此下标为图片资源关系
                ];

                //        if($id <= 0) {// 新加;要加入的特别字段
                //            $addNewData = [
                //                // 'account_password' => $account_password,
                //            ];
                //            $saveData = array_merge($saveData, $addNewData);
                //        }
                $extParams = [
                    'judgeDataKey' => 'replace',// 数据验证的下标
                    'methodName' =>  'replaceByIdNew',
                ];
                $resultDatas = CTAPICompanyScheduleBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
                return ajaxDataArr(1, $resultDatas, '');
            });
    }

    /**
     * @OA\Get(
     *     path="/api/admin/quality_control/company_new_schedule/ajax_alist",
     *     tags={"大后台-能力验证-能力附表"},
     *     summary="能力附表--列表",
     *     description="能力附表--列表......",
     *     operationId="adminQualityControlCompanyNewScheduleAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_schedule_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_company_schedule"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_schedule"}
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
//        $relations = [];//  ['siteResources']
//        $extParams = [
//            // 'handleKeyArr' => ['company', 'siteResources'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> CTAPICompanyScheduleBusiness::getRelationConfigs($request, $this, ['company_info' => '', 'resource_list' => '', 'resource_pdf_list' => ''], []),
//        ];
//        return CTAPICompanyScheduleBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);

        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            $relations = [];//  ['siteResources']
            $extParams = [
                'infoHandleKeyArr' => ['resource_union'],
                // 'handleKeyArr' => ['company', 'siteResources'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPICompanyScheduleBusiness::getRelationConfigs($request, $this, ['company_info' => '', 'resource_list' => '', 'resource_pdf_list' => ''], []),
            ];
            return CTAPICompanyScheduleBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
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
     * ajax获得列表数据--按企业id降序
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_alist_company(Request $request){
//        $this->InitParams($request);
//        $relations = [];//  ['siteResources']
//        $extParams = [
//            // 'handleKeyArr' => ['company', 'siteResources'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> CTAPICompanyScheduleBusiness::getRelationConfigs($request, $this, ['company_info' => '', 'resource_list' => '', 'resource_pdf_list' => ''], []),
//        ];
//        $queryParams = [
//            'where' => [
////                ['company_id', $company_id],
////                //['mobile', $keyword],
//            ],
////            'select' => [
////                'id','company_id','position_name','sort_num'
////                //,'operate_staff_id','operate_staff_id_history'
////                ,'created_at'
////            ],
//            'orderBy' => ['company_id'=>'desc', 'id'=>'desc'],//
//        ];
//        return CTAPICompanyScheduleBusiness::getList($request, $this, 2 + 4, $queryParams, $relations, $extParams);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            $relations = [];//  ['siteResources']
            $extParams = [
                // 'handleKeyArr' => ['company', 'siteResources'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPICompanyScheduleBusiness::getRelationConfigs($request, $this, ['company_info' => '', 'resource_list' => '', 'resource_pdf_list' => ''], []),
            ];
            $queryParams = [
                'where' => [
//                ['company_id', $company_id],
//                //['mobile', $keyword],
                ],
//            'select' => [
//                'id','company_id','position_name','sort_num'
//                //,'operate_staff_id','operate_staff_id_history'
//                ,'created_at'
//            ],
                'orderBy' => ['company_id'=>'desc', 'id'=>'desc'],//
            ];
            return CTAPICompanyScheduleBusiness::getList($request, $this, 2 + 4, $queryParams, $relations, $extParams);
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
//        $result = CTAPICompanyScheduleBusiness::getList($request, $this, 1 + 0);
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
//        CTAPICompanyScheduleBusiness::getList($request, $this, 1 + 0);
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
//        CTAPICompanyScheduleBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @OA\Post(
     *     path="/api/admin/quality_control/company_new_schedule/ajax_del",
     *     tags={"大后台-能力验证-能力附表"},
     *     summary="能力附表--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="adminQualityControlCompanyNewScheduleAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_company_schedule_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_company_schedule"}
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
//        return CTAPICompanyScheduleBusiness::delAjax($request, $this);

        $tem_id = CommonRequest::get($request, 'id');
        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
            return CTAPICompanyScheduleBusiness::delAjax($request, $this);
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
//        $childKV = CTAPICompanyScheduleBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPICompanyScheduleBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
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
//        $resultDatas = CTAPICompanyScheduleBusiness::importByFile($request, $this, $fileName);
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
//        $organize_id = CommonRequest::getInt($request, 'company_id');
//        if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
//
//        $userInfo = $this->getStaffInfo($organize_id);
//        if(empty($userInfo)) throws('企业记录不存在！');
//
//        // 上传并保存文件
//        $result = CTAPIResourceBusiness::fileSingleUpload($request, $this, 4);
//        if($result['apistatus'] == 0) return $result;
//        // 文件上传成功
//        // /srv/www/dogtools/admin/public/resource/company/5/excel/2020/06/21/2020062115463441018048779bab4a.xlsx
//        $fileName = Tool::getPath('public') . $result['result']['filePath'];
//
//        // 图片资源
//        $resource_id = $result['result']['id'];// CommonRequest::get($request, 'resource_id');
//        // 如果是字符，则转为数组
//        if(is_string($resource_id) || is_numeric($resource_id)){
//            if(strlen(trim($resource_id)) > 0){
//                $resource_id = explode(',' ,$resource_id);
//            }
//        }
//        if(!is_array($resource_id)) $resource_id = [];
//
//        // 再转为字符串
//        $resource_ids = implode(',', $resource_id);
//        if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';
//
//        $saveData = [
//            'company_id' => $organize_id,
//            // 'resource_id' => $resource_id[0] ?? 0,// 第一个图片资源的id
//            'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
//            'resourceIds' => $resource_id,// 此下标为图片资源关系
//        ];
//        $extParams = [
//            'judgeDataKey' => 'replace',// 数据验证的下标
//        ];
//        $id = 0;
//        $resultDatas = CTAPICompanyScheduleBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
//
//        return ajaxDataArr(1, $resultDatas, '');

        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){

            // $this->company_id = 1;
            // 企业 的 个人--只能读自己的人员信息
            $organize_id = CommonRequest::getInt($request, 'company_id');
            if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');

            $userInfo = $this->getStaffInfo($organize_id);
            if(empty($userInfo)) throws('企业记录不存在！');

            // 上传并保存文件
            $result = CTAPIResourceBusiness::fileSingleUpload($request, $this, 4);
            if($result['apistatus'] == 0) return $result;
            // 文件上传成功
            // /srv/www/dogtools/admin/public/resource/company/5/excel/2020/06/21/2020062115463441018048779bab4a.xlsx
            $fileName = Tool::getPath('public') . $result['result']['filePath'];

            // 图片资源
            $resource_id = $result['result']['id'];// CommonRequest::get($request, 'resource_id');
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
                'company_id' => $organize_id,
                // 'resource_id' => $resource_id[0] ?? 0,// 第一个图片资源的id
                'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
                'resourceIds' => $resource_id,// 此下标为图片资源关系
            ];
            $extParams = [
                'judgeDataKey' => 'replace',// 数据验证的下标
            ];
            $id = 0;
            $resultDatas = CTAPICompanyScheduleBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);

        });
    }


    /**
     * 单文件上传-上传pdf
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function up_pdf(Request $request)
    {
//        $this->InitParams($request);
//        // $this->company_id = 1;
//        // 企业 的 个人--只能读自己的人员信息
//        $organize_id = CommonRequest::getInt($request, 'company_id');
//        if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
//
//        $userInfo = $this->getStaffInfo($organize_id);
//        if(empty($userInfo)) throws('企业记录不存在！');
//
//        // 上传并保存文件
//        return CTAPIResourceBusiness::fileSingleUpload($request, $this, 8);

        return $this->exeDoPublicFun($request, 0, 4,'', true, '', [], function (&$reDataArr) use ($request){

//            // $this->company_id = 1;
//            // 企业 的 个人--只能读自己的人员信息
//            $organize_id = CommonRequest::getInt($request, 'company_id');
//            if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
//
//            $userInfo = $this->getStaffInfo($organize_id);
//            if(empty($userInfo)) throws('企业记录不存在！');

            // 上传并保存文件
            return CTAPIResourceBusiness::filePlupload($request, $this, 8);
        });
    }

    /**
     * 单文件上传-上传word
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function up_word(Request $request)
    {
//        $this->InitParams($request);
//        // $this->company_id = 1;
//        // 企业 的 个人--只能读自己的人员信息
//        $organize_id = CommonRequest::getInt($request, 'company_id');
//        if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
//
//        $userInfo = $this->getStaffInfo($organize_id);
//        if(empty($userInfo)) throws('企业记录不存在！');
//
//        // 上传并保存文件
//        return CTAPIResourceBusiness::fileSingleUpload($request, $this, 16);

        return $this->exeDoPublicFun($request, 0, 4,'', true, '', [], function (&$reDataArr) use ($request){

            // $this->company_id = 1;
            // 企业 的 个人--只能读自己的人员信息
            $organize_id = CommonRequest::getInt($request, 'company_id');
            if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');

            $userInfo = $this->getStaffInfo($organize_id);
            if(empty($userInfo)) throws('企业记录不存在！');

            // 上传并保存文件
            return CTAPIResourceBusiness::filePlupload($request, $this, 16);
        });
    }


    /**
     * 单文件上传-上传excel
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function up_excel(Request $request)
    {
//        $this->InitParams($request);
//        // $this->company_id = 1;
//        // 企业 的 个人--只能读自己的人员信息
//        $organize_id = CommonRequest::getInt($request, 'company_id');
//        if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
//
//        $userInfo = $this->getStaffInfo($organize_id);
//        if(empty($userInfo)) throws('企业记录不存在！');
//
//        // 上传并保存文件
//        return CTAPIResourceBusiness::filePlupload($request, $this, 2);

        return $this->exeDoPublicFun($request, 0, 4,'', true, '', [], function (&$reDataArr) use ($request){

//            // $this->company_id = 1;
//            // 企业 的 个人--只能读自己的人员信息
//            $organize_id = CommonRequest::getInt($request, 'company_id');
//            if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
//
//            $userInfo = $this->getStaffInfo($organize_id);
//            if(empty($userInfo)) throws('企业记录不存在！');

            // 上传并保存文件
            return CTAPIResourceBusiness::filePlupload($request, $this, 2);
        });
    }
    /**
     * 单文件上传-上传图片
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function up_img(Request $request)
    {
//        $this->InitParams($request);
//        // $this->company_id = 1;
//        // 企业 的 个人--只能读自己的人员信息
//        $organize_id = CommonRequest::getInt($request, 'company_id');
//        if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
//
//        $userInfo = $this->getStaffInfo($organize_id);
//        if(empty($userInfo)) throws('企业记录不存在！');
//
//        // 上传并保存文件
//        return CTAPIResourceBusiness::filePlupload($request, $this, 1);

        return $this->exeDoPublicFun($request, 0, 4,'', true, '', [], function (&$reDataArr) use ($request){

            // $this->company_id = 1;
            // 企业 的 个人--只能读自己的人员信息
            $organize_id = CommonRequest::getInt($request, 'company_id');
            if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');

            $userInfo = $this->getStaffInfo($organize_id);
            if(empty($userInfo)) throws('企业记录不存在！');

            // 上传并保存文件
            return CTAPIResourceBusiness::filePlupload($request, $this, 1);
        });
    }

    /**
     * 下载文件
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function down_moban(Request $request)
    {
////        $this->InitParams($request);
//        // $this->source = 2;
////        $reDataArr = $this->reDataArr;
//        // 下载二维码文件
//        $publicPath = Tool::getPath('public');
//        $fileName = '/download/moban.xlsx';
//        $save_file_name = CommonRequest::get($request, 'save_file_name');// 下载时保存的文件名 [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
//        $res = DownFile::downFilePath(2, $publicPath . $fileName, 1024, $save_file_name);
//        if(is_string($res)) echo $res;

        return $this->exeDoPublicFun($request, 0, 4,'', true, '', [], function (&$reDataArr) use ($request){

            // 下载二维码文件
            $publicPath = Tool::getPath('public');
            $fileName = '/download/moban.xlsx';
            $save_file_name = CommonRequest::get($request, 'save_file_name');// 下载时保存的文件名 [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
            $res = DownFile::downFilePath(2, $publicPath . $fileName, 1024, $save_file_name);
            if(is_string($res)) echo $res;
        });
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


        $company_id = CommonRequest::getInt($request, 'company_id');
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
        // pr($info);
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


        $info = [
            'id'=>$id,
            //   'department_id' => 0,
        ];
        $operate = "添加";
        // 如果是企业列表点《企业简介》
        $company_id = CommonRequest::getInt($request, 'company_id');
        if($id <= 0 && $company_id > 0){
            $companyInfo = CTAPIStaffBusiness::getInfoData($request, $this, $company_id);
            if(empty($companyInfo)) throws('企业信息不存在！');
            $info['company_id'] = $company_id;
            $info['user_company_name'] = $companyInfo['company_name'] ?? '';
        }

        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $info = CTAPICompanyScheduleBusiness::getInfoData($request, $this, $id, [], '', []);
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

        $reDataArr['type_ids'] = CompanySchedule::$typeIdArr;
        $reDataArr['defaultTypeId'] = $info['type_id'] ?? -1;// 默认

        $company_hidden = CommonRequest::getInt($request, 'company_hidden');
        $reDataArr['company_hidden'] = $company_hidden;// =1 : 隐藏企业选择

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
