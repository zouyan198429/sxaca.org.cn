<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIAbilityJoinItemsBusiness;
use App\Business\Controller\API\QualityControl\CTAPIAbilitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPIResourceBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\Abilitys;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;

class AbilitysController extends BasicController
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
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//             $reDataArr = array_merge($reDataArr, $this->reDataArr);
//
//            return view('admin.QualityControl.Abilitys.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.Abilitys.index', true
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
        return $this->exeDoPublicFun($request, 2048, 1, 'admin.QualityControl.Abilitys.select', true
            , 'doListPage', [], function (&$reDataArr) use ($request){

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
//            return view('admin.QualityControl.Abilitys.add', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'admin.QualityControl.Abilitys.add', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

        });
    }

    /**
     * 公布结果
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function publish(Request $request,$id = 0)
    {
        return $this->exeDoPublicFun($request, 0, 8,'admin.QualityControl.Abilitys.publish', true
            , '', [], function (&$reDataArr) use ($request, &$id){
                $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
                if(empty($info)) throws('记录不存在！');
                $status = $info['status'];
                $is_publish = $info['is_publish'];
                if($status != 4 || $is_publish != 2) throws('非进行中状态或非待公布状态，不可进行此操作！');
                $reDataArr['info'] = $info;

                // 公布结果时间类型 1待指定  2立即公布 4  定时公布
                $publishType = Abilitys::$publishTypeArr;
                if(isset($publishType[1])) unset($publishType[1]);
                $reDataArr['publishType'] = $publishType;
                $reDataArr['defaultPublishType'] = -1;// 列表页默认状态
            });
    }

    /**
     * 添加--导入
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function add_excel(Request $request,$id = 0)
    {
        $pageNum = 0;
        return $this->exeDoPublicFun($request, $pageNum, 1,'admin.QualityControl.Abilitys.add_excel', true
            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){

            });
    }

    /**
     * 查看
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function info(Request $request,$id = 0)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            if(!is_numeric($id) || $id <= 0){
                throws('参数[id]有误！');
            }
            $operate = "详情";
            // $handleKeyArr = ['projectStandards', 'projectSubmitItems'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list' => '', 'project_submit_items_list' => ''], []),
            ];
            $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', $extParams);
            // $reDataArr = array_merge($reDataArr, $resultDatas);
            if(empty($info)) {
                throws('记录不存在！');
            }
            $reDataArr['info'] = $info;
            $reDataArr['operate'] = $operate;
            return view('admin.QualityControl.Abilitys.info', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 17179869184, 1,'web.QualityControl.admin.info', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//            });
    }

    /**
     * @OA\Get(
     *     path="/api/admin/abilitys/ajax_info",
     *     tags={"大后台-能力验证-能力验证"},
     *     summary="能力验证--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="adminQualityControlAbilitysAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_abilitys_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_abilitys"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_abilitys"}
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
        $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
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
     *     path="/api/admin/abilitys/ajax_save",
     *     tags={"大后台-能力验证-能力验证"},
     *     summary="能力验证--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="adminQualityControlAbilitysAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_abilitys_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_abilitys"}
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
            , '', [], function (&$reDataArr) use ($request){
                $id = CommonRequest::getInt($request, 'id');
                // CommonRequest::judgeEmptyParams($request, 'id', $id);
                $ability_name = CommonRequest::get($request, 'ability_name');
                $estimate_add_num = CommonRequest::getInt($request, 'estimate_add_num');
                $join_begin_date = CommonRequest::get($request, 'join_begin_date');
                $join_end_date = CommonRequest::get($request, 'join_end_date');
                $duration_minute = CommonRequest::getInt($request, 'duration_minute');
                // 判断开始结束日期
                Tool::judgeBeginEndDate($join_begin_date, $join_end_date, 1 + 2 + 16 + 128 + 256 + 512, 1, date('Y-m-d H:i:s'), '报名时间');
                if(!is_numeric($duration_minute) || $duration_minute <= 0 ) throws('数据提交时限必须是数值且大于0！');


                // 方法标准
                $project_standard_ids = CommonRequest::get($request, 'project_standard_ids');// 值id数组
                if(is_string($project_standard_ids) || !is_array($project_standard_ids)) $project_standard_ids = explode(',', $project_standard_ids);

                $project_standard_names = CommonRequest::get($request, 'project_standard_names');// 值数组
                if(is_string($project_standard_names) || !is_array($project_standard_names)) $project_standard_names = explode(',', $project_standard_names);

                $project_standards = [];// 数组
                foreach ($project_standard_ids as $k => $temId){
                    array_push($project_standards,[
                        'id' => $temId,
                        'name' => $project_standard_names[$k],
                    ]);
                }

                // 验证数据项
                $submit_item_ids = CommonRequest::get($request, 'submit_item_ids');// 值id数组
                if(is_string($submit_item_ids) || !is_array($submit_item_ids)) $submit_item_ids = explode(',', $submit_item_ids);

                $submit_item_names = CommonRequest::get($request, 'submit_item_names');// 值数组
                if(is_string($submit_item_names) || !is_array($submit_item_names)) $submit_item_names = explode(',', $submit_item_names);

                $submit_items = [];// 数组
                foreach ($submit_item_ids as $k => $temId){
                    array_push($submit_items,[
                        'id' => $temId,
                        'name' => $submit_item_names[$k],
                    ]);
                }

                $saveData = [
                    'ability_name' => $ability_name,
                    'estimate_add_num' => $estimate_add_num,
                    'duration_minute' => $duration_minute,
                    'join_begin_date' => $join_begin_date,
                    'join_end_date' => $join_end_date,
                    'project_standards' => $project_standards,// 方法标准 - 数组
                    'submit_items' => $submit_items,// 验证数据项  - 数组
                ];
                // 开始报名前，可以增删改，后面就不可以修改、删除
                if($id > 0){
                    $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
                    if(empty($info)) throws('记录不存在！');
                    if($info['status'] != 1) throws('当前记录非【待开始】状态，不可修改！');
                }

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
                $extParams = [
                    'judgeDataKey' => 'replace',// 数据验证的下标
                ];
                $resultDatas = CTAPIAbilitysBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
                return ajaxDataArr(1, $resultDatas, '');
        });
    }


    /**
     * ajax保存数据 修改公布时间类型
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save_publish(Request $request)
    {
//        $this->InitParams($request);

        return $this->exeDoPublicFun($request, 0, 4,'', true
            , '', [], function (&$reDataArr) use ($request){
                $id = CommonRequest::getInt($request, 'id');
                $publish_type = CommonRequest::getInt($request, 'publish_type');
                $publish_time = CommonRequest::get($request, 'publish_time');

                // 公布结果时间类型 1待指定  2立即公布 4  定时公布
                $publishType = Abilitys::$publishTypeArr;
                if(!in_array($publish_type, array_keys($publishType))) throws('请选择正确的公布类型！');
                $begin_time = date('Y-m-d H:i:s');
                if($publish_type != 4) $publish_time = $begin_time;
                if($publish_type == 4){
                    // 判断指定公布时间
                    Tool::judgeBeginEndDate($begin_time, $publish_time, 1 + 2 + 64 + 128 + 256 + 512, 1, date('Y-m-d H:i:s'), '指定公布时间');

                }

                $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
                if(empty($info)) throws('记录不存在！');
                $status = $info['status'];
                $is_publish = $info['is_publish'];
                if($status != 4 || $is_publish != 2) throws('非进行中状态或非待公布状态，不可进行此操作！');

                $saveData = [
                    'publish_type' => $publish_type,
                    'publish_time' => $publish_time,
                    // 'judge_complete' => 1,// 有此下标，会去判断状态应该是不是可以到完成状态
                ];
                if($publish_type == 2){// 2立即公布
                    $saveData['is_publish'] = 4;
                    $saveData['judge_complete'] = 1;// 有此下标，会去判断状态应该是不是可以到完成状态
                }
                $extParams = [
                    'judgeDataKey' => 'replace',// 数据验证的下标
                ];
                $resultDatas = CTAPIAbilitysBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
                return ajaxDataArr(1, $resultDatas, '');
            });
    }



    /**
     * ajax保存数据--导入excel数据
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_excel_save(Request $request)
    {
//        $this->InitParams($request);

        $id = CommonRequest::getInt($request, 'id');
        $pageNum = 0;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request){

                $id = CommonRequest::getInt($request, 'id');
                // CommonRequest::judgeEmptyParams($request, 'id', $id);
                // $company_id = CommonRequest::getInt($request, 'company_id');
                $test_year = CommonRequest::getInt($request, 'test_year');
//
                if(!is_numeric($test_year) || $test_year <= 0) throws('请填入正确的年信息');

                // 资源
//        $resource_id = [];
                $resource_id = CommonRequest::get($request, 'resource_id');
                // 如果是字符，则转为数组
                if(is_string($resource_id) || is_numeric($resource_id)){
                    if(strlen(trim($resource_id)) > 0){
                        $resource_id = explode(',' ,$resource_id);
                    }
                }
                if(!is_array($resource_id)) $resource_id = [];
                if(empty($resource_id)) throws('请选择要导入的文件');

                $resourceId = $resource_id[0] ?? 0;
                if(!is_numeric($resourceId) || $resourceId <= 0)  throws('请选择要导入的文件');
                // 获得资源数据
                $resourceInfo = CTAPIResourceBusiness::getInfoData($request, $this, $resourceId, [], '', []);
                if(empty($resourceInfo))  throws('文件记录不存在');
                $resource_url = $resourceInfo['resource_url'] ?? '';



                $mergeParams = [
                     'test_year' => $test_year,
                    // 'company_id' => $company_id,
                    // 'certificate_no' => $certificate_no,
                    // 'ratify_date' => $ratify_date,
                    // 'valid_date' => $valid_date,
                    // 'addr' => $addr,
                ];
                CTAPIAbilitysBusiness::mergeRequest($request, $this, $mergeParams);


                // 文件上传成功
                // /srv/www/dogtools/admin/public/resource/company/5/excel/2020/06/21/2020062115463441018048779bab4a.xlsx
                $fileName = Tool::getPath('public') . $resource_url;// $result['result']['filePath'];
                $resultDatas = [];
                try{
                    $resultDatas = CTAPIAbilitysBusiness::importByFile($request, $this, $fileName);
                } catch ( \Exception $e) {
                    throws($e->getMessage());
                } finally {
                    // $resourceId = $result['result']['id'] ?? 0;
                    if ($resourceId > 0) {
                        CTAPIAbilitysBusiness::mergeRequest($request, $this, [
                            'id' => $resourceId,
                        ]);
                        CTAPIResourceBusiness::delAjax($request, $this);
                    }
                    // 删除上传的文件
                    // Tool::resourceDelFile(['resource_url' => $result['result']['filePath']]);
                    Tool::resourceDelFile(['resource_url' => $resource_url]);
                }
                return ajaxDataArr(1, $resultDatas, '');
            });
    }
    /**
     * @OA\Get(
     *     path="/api/admin/abilitys/ajax_alist",
     *     tags={"大后台-能力验证-能力验证"},
     *     summary="能力验证--列表",
     *     description="能力验证--列表......",
     *     operationId="adminQualityControlAbilitysAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_abilitys_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_abilitys"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_abilitys"}
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
//
//        $relations = [];//  ['siteResources']
//        // $handleKeyArr = ['projectStandards', 'projectSubmitItems'];
//        $extParams = [
//            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list' => '', 'project_submit_items_list' => ''], []),
//        ];
//
//        return  CTAPIAbilitysBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){


            $relations = [];//  ['siteResources']
            // $handleKeyArr = ['projectStandards', 'projectSubmitItems'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list' => '', 'project_submit_items_list' => ''], []),
            ];

            return  CTAPIAbilitysBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
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
//        $result = CTAPIAbilitysBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIAbilitysBusiness::getList($request, $this, 1 + 0);
//        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0);
//        });
//    }

    /**
     * 导出--报名的企业信息
     *
     * @param Request $request
     * @param int $ability_id 所属能力验证
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function export_join(Request $request, $ability_id = 0){
//        $this->InitParams($request);
//        CTAPIAbilitysBusiness::getList($request, $this, 1 + 0);
        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request, &$ability_id){
            $mergeParams = [
                'ability_id' => $ability_id,
                'is_export' => 1,
            ];
            CTAPIAbilityJoinItemsBusiness::mergeRequest($request, $this, $mergeParams);
            $handleKeyConfigArr = ['company_info_all', 'ability_info'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];
            $aa = CTAPIAbilityJoinItemsBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
            pr($aa);
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
//        CTAPIAbilitysBusiness::importTemplate($request, $this);
        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
            CTAPIAbilitysBusiness::importTemplate($request, $this);
        });
    }


    /**
     * @OA\Post(
     *     path="/api/admin/abilitys/ajax_del",
     *     tags={"大后台-能力验证-能力验证"},
     *     summary="能力验证--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="adminQualityControlAbilitysAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_abilitys_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_abilitys"}
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
//         $id = CommonRequest::getInt($request, 'id');
//        $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
//        if(empty($info)) throws('记录不存在！');
//        if($info['status'] != 1) throws('当前记录非【待开始】状态，不可删除！');
//        return CTAPIAbilitysBusiness::delAjax($request, $this);

        $tem_id = CommonRequest::get($request, 'id');
        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
            $id = CommonRequest::getInt($request, 'id');
            $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
            if(empty($info)) throws('记录不存在！');
            if($info['status'] != 1) throws('当前记录非【待开始】状态，不可删除！');
            return CTAPIAbilitysBusiness::delAjax($request, $this);
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
//        $childKV = CTAPIAbilitysBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIAbilitysBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
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
//        $resultDatas = CTAPIAbilitysBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPIAbilitysBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPIAbilitysBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * 单文件上传-上传excel
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function up_excel(Request $request)
    {
        $this->InitParams($request);
        // $this->company_id = 1;
        // 企业 的 个人--只能读自己的人员信息
//        $organize_id = $this->user_id;// CommonRequest::getInt($request, 'company_id');
//        if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');
//
//        $userInfo = $this->getStaffInfo($organize_id);
//        if(empty($userInfo)) throws('企业记录不存在！');

        // 上传并保存文件
        return CTAPIResourceBusiness::filePlupload($request, $this, 2);
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

        // 状态
        $reDataArr['status'] =  Abilitys::$statusArr;
        $reDataArr['defaultStatus'] = -1;// 列表页默认状态

        // 是否公布结果1未公布  2待公布 4  已公布
        $reDataArr['isPublish'] =  Abilitys::$isPublishArr;
        $reDataArr['defaultIsPublish'] = -1;// 列表页默认状态
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
            // $handleKeyArr = ['projectStandards', 'projectSubmitItems'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list' => '', 'project_submit_items_list' => ''], []),
            ];
            $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', $extParams);
        }
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
