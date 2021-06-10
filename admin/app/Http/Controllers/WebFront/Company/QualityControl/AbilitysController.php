<?php

namespace App\Http\Controllers\WebFront\Company\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIAbilityJoinBusiness;
use App\Business\Controller\API\QualityControl\CTAPIAbilitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPICompanyScheduleBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffExtendBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Carbon\Carbon;
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
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('company.QualityControl.Abilitys.index', $reDataArr);
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'company.QualityControl.Abilitys.index', true
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
            $reDataArr['province_kv'] = CTAPIAbilitysBusiness::getCityByPid($request, $this,  0);
            $reDataArr['province_kv'] = CTAPIAbilitysBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
            $reDataArr['province_id'] = 0;
            return view('company.QualityControl.Abilitys.select', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'admin.QualityControl.RrrDddd.select', true
//            , 'doListPage', [], function (&$reDataArr) use ($request){
//
//            });
    }

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
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('company.QualityControl.Abilitys.add', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//
//        $pageNum = ($id > 0) ? 64 : 16;
//        return $this->exeDoPublicFun($request, $pageNum, 1,'company.QualityControl.Abilitys.add', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
//    }

    /**
     * 添加--报名
     *
     * @param Request $request
     * @param string $ids  报名的项目id ,  多个用逗号分隔
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function join(Request $request,$ids = '')
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$ids){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            if(empty($ids)) {
                throws('参数[ids]有误！');
            }
            $info = $this->user_info;
            // 根据条件获得项目列表数据
            $mergeParams = [
                // 'status' => 2,// 状态(1待开始 、2报名中、4进行中、8已结束 16 已取消【作废】)
                'ids' => $ids
            ];
            CTAPIAbilitysBusiness::mergeRequest($request, $this, $mergeParams);

            $relations = [];//  ['siteResources']
            // $handleKeyArr = ['projectStandards'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list' => '', 'project_submit_items_list' => ''], []),
            ];
            $dataList = CTAPIAbilitysBusiness::getList($request, $this, 1, [], $relations, $extParams)['result']['data_list'] ?? [];


//        $dbIds = array_values(array_unique(array_column($dataList,'id')));
//        $idsArr = explode(',', $ids);
//        $diffIds = array_diff($idsArr, $dbIds);
//        if(!empty($diffIds)){
//            // 还得查一下不是状态2的记录，再获得kv把
//            $queryParams = [
//                'where' => [
////                ['company_id', $organize_id],
//                   // ['ability_id', $id],
////                ['teacher_status',1],
//                ],
//                // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//            ];
//            Tool::appendParamQuery($queryParams, $diffIds, 'id', [0, '0', ''], ',', false);
//            $diffDataList = CTAPIAbilitysBusiness::ajaxGetQueryListCTL($request, $this, '', $queryParams, [], 1);
//            $abilityNameText = implode('、',Tool::getArrFields($diffDataList, 'ability_name'));// formatArrKeyVal($dataList, 'id', 'ability_name');
//            throws('项目[' . $abilityNameText . ']非可报名状态，不可报名！');
//        }
            // 判断是否已经过了报名时间
            CTAPIAbilitysBusiness::judgeCanJoin( $request, $this, $dataList, 1 | 2 | 4);

            // 获得企业今年的报名
            $currentNow = Carbon::now();
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                // 'relationFormatConfigs'=> CTAPIAbilityJoinBusiness::getRelationConfigs($request, $this, ['join_items_save'], []),
            ];
            $joinedInfo = CTAPIAbilityJoinBusiness::getFVFormatList( $request,  $this, 4, 1
                , ['join_year' => $currentNow->year, 'staff_id' => $this->user_id, 'admin_type' => $info['admin_type'] ], false, [], $extParams);
            // $ability_join_items = $joinedInfo['ability_join_items'] ?? [];
            // 已经有报名
            if(!empty($joinedInfo)){
                // 非 16 已评定   32 已完成
                if(!in_array($joinedInfo['status'], [16, 32])){
                    $reDataArr['joinInfo'] = [
                        'ability_code' => $joinedInfo['ability_code'],
                        'contacts' => $joinedInfo['contacts'],
                        'mobile' => $joinedInfo['mobile'],
                        'tel' => $joinedInfo['tel'],
                    ];
                }
            }


            $reDataArr['data_list'] = $dataList;
            $reDataArr['ids'] = $ids;
            $reDataArr['data_num'] = count($dataList);
            return view('company.QualityControl.Abilitys.join', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
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
            return view('company.QualityControl.Abilitys.info', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 17179869184, 1,'web.QualityControl.admin.info', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//            });
    }

    /**
     * @OA\Get(
     *     path="/api/company/abilitys/ajax_info",
     *     tags={"企业后台-能力验证-能力验证"},
     *     summary="能力验证--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="companyQualityControlAbilitysAjax_info",
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
//    public function ajax_info(Request $request){
//        $this->InitParams($request);
//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
//        $resultDatas = ['info' => $info];
//        return ajaxDataArr(1, $resultDatas, '');

//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
//    }

    /**
     * @OA\Post(
     *     path="/api/company/abilitys/ajax_save",
     *     tags={"企业后台-能力验证-能力验证"},
     *     summary="能力验证--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="companyQualityControlAbilitysAjax_save",
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
//    public function ajax_save(Request $request)
//    {
//        $this->InitParams($request);
//
//        $id = CommonRequest::getInt($request, 'id');
//        $pageNum = ($id > 0) ? 256 : 32;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
//            , '', [], function (&$reDataArr) use ($request){
//                $id = CommonRequest::getInt($request, 'id');
//                // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                $ability_name = CommonRequest::get($request, 'ability_name');
//                $estimate_add_num = CommonRequest::getInt($request, 'estimate_add_num');
//                $join_begin_date = CommonRequest::get($request, 'join_begin_date');
//                $join_end_date = CommonRequest::get($request, 'join_end_date');
//                $duration_minute = CommonRequest::getInt($request, 'duration_minute');
//                // 判断开始结束日期
//                Tool::judgeBeginEndDate($join_begin_date, $join_end_date, 1 + 2 + 16 + 128 + 256 + 512, 1, date('Y-m-d H:i:s'), '报名时间');
//                if(!is_numeric($duration_minute) || $duration_minute <= 0 ) throws('数据提交时限必须是数值且大于0！');
//
//
//                // 方法标准
//                $project_standard_ids = CommonRequest::get($request, 'project_standard_ids');// 值id数组
//                if(is_string($project_standard_ids) || !is_array($project_standard_ids)) $project_standard_ids = explode(',', $project_standard_ids);
//
//                $project_standard_names = CommonRequest::get($request, 'project_standard_names');// 值数组
//                if(is_string($project_standard_names) || !is_array($project_standard_names)) $project_standard_names = explode(',', $project_standard_names);
//
//                $project_standards = [];// 数组
//                foreach ($project_standard_ids as $k => $temId){
//                    array_push($project_standards,[
//                        'id' => $temId,
//                        'name' => $project_standard_names[$k],
//                    ]);
//                }
//
//                // 验证数据项
//                $submit_item_ids = CommonRequest::get($request, 'submit_item_ids');// 值id数组
//                if(is_string($submit_item_ids) || !is_array($submit_item_ids)) $submit_item_ids = explode(',', $submit_item_ids);
//
//                $submit_item_names = CommonRequest::get($request, 'submit_item_names');// 值数组
//                if(is_string($submit_item_names) || !is_array($submit_item_names)) $submit_item_names = explode(',', $submit_item_names);
//
//                $submit_items = [];// 数组
//                foreach ($submit_item_ids as $k => $temId){
//                    array_push($submit_items,[
//                        'id' => $temId,
//                        'name' => $submit_item_names[$k],
//                    ]);
//                }
//
//                $saveData = [
//                    'ability_name' => $ability_name,
//                    'estimate_add_num' => $estimate_add_num,
//                    'duration_minute' => $duration_minute,
//                    'join_begin_date' => $join_begin_date,
//                    'join_end_date' => $join_end_date,
//                    'project_standards' => $project_standards,// 方法标准 - 数组
//                    'submit_items' => $submit_items,// 验证数据项  - 数组
//                ];
//                // 开始报名前，可以增删改，后面就不可以修改、删除
//                if($id > 0){
//                    $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
//                    if(empty($info)) throws('记录不存在！');
//                    if($info['status'] != 1) throws('当前记录非【待开始】状态，不可修改！');
//                }
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
//                $resultDatas = CTAPIAbilitysBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
//                return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * ajax保存数据--报名
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_join_save(Request $request)
//    {
//        $this->InitParams($request);
//        $ids = CommonRequest::get($request, 'ids');// 多个用,号分隔
//        if(empty($ids)) throws('参数[ids]有误！');
//        if(!is_string($ids) && !is_array($ids))  throws('参数[ids]有误！');
//        // 字符转数组
//        if(is_string($ids)) $ids = explode(',', $ids);
//        $contacts = CommonRequest::get($request, 'contacts');
//        $mobile = CommonRequest::get($request, 'mobile');
//        $tel = CommonRequest::get($request, 'tel');
//
//        // 对记录进行判断，是否可以报名
//        // 根据条件获得项目列表数据
//        $mergeParams = [
//            // 'status' => 2,// 状态(1待开始 、2报名中、4进行中、8已结束 16 已取消【作废】)
//            'id' => implode(',', $ids),
//        ];
//        CTAPIAbilitysBusiness::mergeRequest($request, $this, $mergeParams);
//
//        $relations = [];//  ['siteResources']
//        $handleKeyArr = ['projectStandards'];
//        $extParams = [
//            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            // 'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list' => ''], []),
//        ];
//        $dataList = CTAPIAbilitysBusiness::getList($request, $this, 1, [], $relations, $extParams)['result']['data_list'] ?? [];
//        // 判断是否已经过了报名时间
//         CTAPIAbilitysBusiness::judgeCanJoin( $request, $this, $dataList, 1 | 2 | 4);
//
//        $user_info = $this->user_info;
//
//        $currentNow = Carbon::now()->toDateTimeString();
//        // $ability_code = '';// 单号 生成  2020NLYZ0001
//
//        $ability_join_id = 0;
//
//        $ability_join_items = [];
//
//        $info = $this->user_info;
//
//        // 获得企业今年的报名
//        $extParams = [
//            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> CTAPIAbilityJoinBusiness::getRelationConfigs($request, $this, ['join_items_save'], []),
//        ];
//        $joinedInfo = CTAPIAbilityJoinBusiness::getFVFormatList( $request,  $this, 4, 1
//            , ['join_year' => Carbon::now()->year, 'staff_id' => $this->user_id, 'admin_type' => $info['admin_type'] ], false, [], $extParams);
//        // 已经有报名-- 一个企业当前有报名，用当前的号
//        $ability_code = '';
//        if(!empty($joinedInfo)){
//            $ability_code = $joinedInfo['ability_code'];
//            // 非 16 已评定   32 已完成
//            if(!in_array($joinedInfo['status'], [16, 32])){
//                $ability_join_id = $joinedInfo['id'];
//                $ability_join_items = $joinedInfo['ability_join_items'] ?? [];
//            }
//        }
//
//        foreach($ids as $tem_id){
//            $ability_join_items_standards = [];
//            $project_standard_id = CommonRequest::get($request, 'project_standard_id_' . $tem_id);// 数组或字符串[多个逗号分隔]
//            if(is_string($project_standard_id)) $project_standard_id = explode(',', $project_standard_id);
//            $project_standard_name = CommonRequest::get($request, 'project_standard_name_' . $tem_id);// 其它时：的  方法标准
//            // if($project_standard_name != '') $project_standard_name = stripslashes($project_standard_name);
//            if($project_standard_name != '') $project_standard_name = replace_enter_char($project_standard_name, 1);
//
//            if(empty($project_standard_id))  throws('参数[方法标准]有误！');
//            if(!in_array(0, $project_standard_id)) $project_standard_name = '';
//
//            foreach($project_standard_id as $tem_sid){
//                array_push($ability_join_items_standards, [
//                    'project_standard_id' => $tem_sid,
//                    'project_standard_name' => ($tem_sid == 0) ? $project_standard_name : '',
//                ]);
//            }
//
//            $temItemInfo = [
//                'admin_type' => $user_info['admin_type'],
//                'staff_id' => $user_info['id'],
//                // 'ability_code' => $ability_code,
//                'ability_id' => $tem_id,
//                'join_time' => $currentNow,
//                'status' => 1,
//                'retry_no' => 0,
//                'is_sample' => 1,
//                'ability_join_items_standards' => $ability_join_items_standards,
//            ];
//            if(!empty($ability_code)) $temItemInfo['ability_code'] = $ability_code;
//            if($ability_join_id > 0)  $temItemInfo['ability_join_id'] = $ability_join_id;
//
//            array_push($ability_join_items, $temItemInfo);
//        }
//
//        $saveData = [
//            'admin_type' => $user_info['admin_type'],
//            'staff_id' => $user_info['id'],
//            // 'ability_code' => $ability_code,// 单号 生成  2020NLYZ0001
//            'contacts' => $contacts,
//            'mobile' => $mobile,
//            'tel' => $tel,
//            'items_num' => count($ability_join_items),
//            'ability_join_items' => $ability_join_items
//        ];
//        if(!empty($ability_code)) $saveData['ability_code'] = $ability_code;
//
//        if($ability_join_id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                'join_year' => Carbon::now()->year,
//                'join_time' => $currentNow,
//
//                'status' => 1,
//                'passed_num' => 0,
//                'is_print' => 1,
//                'is_grant' => 1,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
//
//
//        $extParams = [
//            'judgeDataKey' => 'replace',// 数据验证的下标
//        ];
//        $resultDatas = CTAPIAbilityJoinBusiness::replaceById($request, $this, $saveData, $ability_join_id, $extParams, true);
//        return ajaxDataArr(1, $resultDatas, '');
//    }

    /**
     * ajax保存数据--报名
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_new_join_save(Request $request)
    {
        $this->InitParams($request);
        $ids = CommonRequest::get($request, 'ids');// 多个用,号分隔
        if(empty($ids)) throws('参数[ids]有误！');
        if(!is_string($ids) && !is_array($ids))  throws('参数[ids]有误！');
        // 字符转数组
        if(is_string($ids)) $ids = explode(',', $ids);
        $contacts = CommonRequest::get($request, 'contacts');
        $mobile = CommonRequest::get($request, 'mobile');
        $tel = CommonRequest::get($request, 'tel');

        $contactInfo = [
            'contacts' => $contacts,
            'mobile' => $mobile,
            'tel' => $tel,
        ];

        // 对记录进行判断，是否可以报名
        // 根据条件获得项目列表数据
        $mergeParams = [
            // 'status' => 2,// 状态(1待开始 、2报名中、4进行中、8已结束 16 已取消【作废】)
            'id' => implode(',', $ids),
        ];
        CTAPIAbilitysBusiness::mergeRequest($request, $this, $mergeParams);

        $relations = [];//  ['siteResources']
        $handleKeyArr = ['projectStandards'];
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            // 'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list' => ''], []),
        ];
        $dataList = CTAPIAbilitysBusiness::getList($request, $this, 1, [], $relations, $extParams)['result']['data_list'] ?? [];
        // 判断是否已经过了报名时间
        CTAPIAbilitysBusiness::judgeCanJoin( $request, $this, $dataList, 1 | 2 | 4);
        $user_info = $this->user_info;

        $currentNow = Carbon::now()->toDateTimeString();
        // $ability_code = '';// 单号 生成  2020NLYZ0001

        $ability_join_id = 0;

        $ability_join_items = [];

        $items_num = 0;// 已经报名的数量

        $info = $this->user_info;

        // 获得企业今年的报名
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPIAbilityJoinBusiness::getRelationConfigs($request, $this, ['join_items_save'], []),
        ];
        $joinedInfo = CTAPIAbilityJoinBusiness::getFVFormatList( $request,  $this, 4, 1
            , ['join_year' => Carbon::now()->year, 'staff_id' => $this->user_id, 'admin_type' => $info['admin_type'] ], false, [], $extParams);

        // 已经有报名-- 一个企业当前有报名，用当前的号
        $ability_code = '';
        if(!empty($joinedInfo)){
            $ability_code = $joinedInfo['ability_code'];// 能力验证代码
            // 非 16 已评定   32 已完成
            if(!in_array($joinedInfo['status'], [16, 32])){
                $ability_join_id = $joinedInfo['id'];// 报名主表id
                $tem_ability_join_items = $joinedInfo['ability_join_items'] ?? [];// 报名项目及选择的方法--已报名的
                $ability_join_items = $tem_ability_join_items;// 报名项目及选择的方法--已报名的
                // $items_num = count($tem_ability_join_items);
                foreach($ability_join_items as $k => $v){
                    if(!isset($v['ability_join_items_results']) || empty($v['ability_join_items_results'])) continue;
                    Tool::arrAppendKeys($ability_join_items[$k]['ability_join_items_results'], $contactInfo);

                }
                // 加入最新的联系人信息
                Tool::arrAppendKeys($ability_join_items, $contactInfo);
            }
        }

        // 遍历选择的项目id
        foreach($ids as $tem_id){
            $ability_join_items_standards = [];
            $project_standard_id = CommonRequest::get($request, 'project_standard_id_' . $tem_id);// 数组或字符串[多个逗号分隔]
            if(is_string($project_standard_id)) $project_standard_id = explode(',', $project_standard_id);
            $project_standard_name = CommonRequest::get($request, 'project_standard_name_' . $tem_id);// 其它时：的  方法标准
            // if($project_standard_name != '') $project_standard_name = stripslashes($project_standard_name);
            if($project_standard_name != '') $project_standard_name = replace_enter_char($project_standard_name, 1);

            if(empty($project_standard_id))  throws('参数[方法标准]有误！');
            if(!in_array(0, $project_standard_id)) $project_standard_name = '';

            foreach($project_standard_id as $tem_sid){
                array_push($ability_join_items_standards, [
                    'project_standard_id' => $tem_sid,
                    'project_standard_name' => ($tem_sid == 0) ? $project_standard_name : '',
                ]);
            }

            $temItemInfo = [
                'admin_type' => $user_info['admin_type'],
                'staff_id' => $user_info['id'],
                // 'ability_code' => $ability_code,
                'contacts' => $contacts,
                'mobile' => $mobile,
                'tel' => $tel,
                'ability_id' => $tem_id,
                'join_time' => $currentNow,
                'status' => 1,
                'retry_no' => 0,
                'is_sample' => 1,
                'ability_join_items_standards' => $ability_join_items_standards,
                'ability_join_items_results' => [
                    'admin_type' => $user_info['admin_type'],
                    'staff_id' => $user_info['id'],
                    // 'ability_code' => $ability_code,
                    'contacts' => $contacts,
                    'mobile' => $mobile,
                    'tel' => $tel,
                    'ability_id' => $tem_id,
                    'join_time' => $currentNow,
                    'status' => 1,
                    'retry_no' => 0,
                    'is_sample' => 1,
                ]
            ];
            if(!empty($ability_code)){
                $temItemInfo['ability_code'] = $ability_code;
                $temItemInfo['ability_join_items_results']['ability_code'] = $ability_code;
            }
            if($ability_join_id > 0){
                $temItemInfo['ability_join_id'] = $ability_join_id;
                $temItemInfo['ability_join_items_results']['ability_join_id'] = $ability_join_id;
            }

            array_push($ability_join_items, $temItemInfo);
        }

        $saveData = [
            'admin_type' => $user_info['admin_type'],
            'staff_id' => $user_info['id'],
            // 'ability_code' => $ability_code,// 单号 生成  2020NLYZ0001
            'contacts' => $contacts,
            'mobile' => $mobile,
            'tel' => $tel,
            'items_num' => count($ability_join_items),//  $items_num + count($ability_join_items),//
            'ability_join_items' => $ability_join_items
        ];
        if(!empty($ability_code)) $saveData['ability_code'] = $ability_code;

        if($ability_join_id <= 0) {// 新加;要加入的特别字段
            $addNewData = [
                'join_year' => Carbon::now()->year,
                'join_time' => $currentNow,

                'status' => 1,
                'passed_num' => 0,
                'is_print' => 1,
                'is_grant' => 1,
            ];
            $saveData = array_merge($saveData, $addNewData);
        }


        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIAbilityJoinBusiness::replaceById($request, $this, $saveData, $ability_join_id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * ajax保存数据--报名
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_company_extend(Request $request)
    {
        $this->InitParams($request);
        $company_id = $this->user_id;
        $userInfo = $this->user_info;
        // 获得企业扩展  'sqlParams' => ['count' => 0]
        $resultDatas = CTAPIStaffExtendBusiness::getFVFormatList( $request,  $this, 1, 1
            ,  ['staff_id' => $company_id, 'admin_type' => $userInfo['admin_type']], false,[], []);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * ajax保存数据--报名前，获得能力附表数量【】
     *  必须要有pdf ,再必须要有excel
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_schedule_num(Request $request)
    {
        $this->InitParams($request);
        $company_id = $this->user_id;
        $userInfo = $this->user_info;
        // 获得企业扩展-pdf的数量
        $resultDatas = CTAPICompanyScheduleBusiness::getFVFormatList( $request,  $this, 8, 1
            ,  ['company_id' => $company_id], false,[], ['sqlParams' => ['where' => [['type_id' , '>', 0]]]]);
        if($resultDatas > 0){
            // 获得企业扩展-excel的数量
            $resultExcelDatas = CTAPICompanyScheduleBusiness::getFVFormatList( $request,  $this, 8, 1
                ,  ['company_id' => $company_id], false,[], ['sqlParams' => ['where' => [['type_id' , '=', 0]]]]);
            if(!is_numeric($resultExcelDatas) || $resultExcelDatas <= 0){
                $resultDatas = 0;// 如果没有excel，上传数量也归0
            }else{
                $resultDatas += $resultExcelDatas;
            }
        }
        return ajaxDataArr(1, ['schedule_num' => $resultDatas], '');
    }
    /**
     * @OA\Get(
     *     path="/api/company/abilitys/ajax_alist",
     *     tags={"企业后台-能力验证-能力验证"},
     *     summary="能力验证--列表",
     *     description="能力验证--列表......",
     *     operationId="companyQualityControlAbilitysAjax_alist",
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
//        // 8已结束 16 已取消【作废】 不列出
//        $mergeParams = [
//            'status' => '1,2,4'// 状态(1待开始 、2报名中、4进行中、8已结束 16 已取消【作废】)
//        ];
//        CTAPIAbilitysBusiness::mergeRequest($request, $this, $mergeParams);
//
//        $relations = [];//  ['siteResources']
//        // $handleKeyArr = ['projectStandards', 'projectSubmitItems', 'joined'];
//        $extParams = [
//            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list' => '', 'project_submit_items_list' => '', 'ability_join_items_info' => ''], []),
//        ];
//        return  CTAPIAbilitysBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            // 8已结束 16 已取消【作废】 不列出
            $mergeParams = [
                'status' => '1,2,4'// 状态(1待开始 、2报名中、4进行中、8已结束 16 已取消【作废】)
            ];
            CTAPIAbilitysBusiness::mergeRequest($request, $this, $mergeParams);

            $relations = [];//  ['siteResources']
            // $handleKeyArr = ['projectStandards', 'projectSubmitItems', 'joined'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list' => '', 'project_submit_items_list' => '', 'ability_join_items_info' => ''], []),
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
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import_template(Request $request){
//        $this->InitParams($request);
//        CTAPIAbilitysBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @OA\Post(
     *     path="/api/company/abilitys/ajax_del",
     *     tags={"企业后台-能力验证-能力验证"},
     *     summary="能力验证--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="companyQualityControlAbilitysAjax_del",
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
//    public function ajax_del(Request $request)
//    {
//        $this->InitParams($request);
//         $id = CommonRequest::getInt($request, 'id');
//        $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', []);
//        if(empty($info)) throws('记录不存在！');
//        if($info['status'] != 1) throws('当前记录非【待开始】状态，不可删除！');
//        return CTAPIAbilitysBusiness::delAjax($request, $this);
//
//        $tem_id = CommonRequest::get($request, 'id');
//        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
//        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            return CTAPIRrrDdddBusiness::delAjax($request, $this);
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

//            $info = [
//                'id'=>$id,
//                //   'department_id' => 0,
//            ];
//            $operate = "添加";
//
//            if ($id > 0) { // 获得详情数据
//                $operate = "修改";
//                // $handleKeyArr = ['projectStandards', 'projectSubmitItems'];
//                $extParams = [
//                    // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//                    'relationFormatConfigs'=> CTAPIAbilitysBusiness::getRelationConfigs($request, $this, ['project_standards_list', 'project_submit_items_list'], []),
//                ];
//                $info = CTAPIAbilitysBusiness::getInfoData($request, $this, $id, [], '', $extParams);
//            }
//            // $reDataArr = array_merge($reDataArr, $resultDatas);
//            $reDataArr['info'] = $info;
//            $reDataArr['operate'] = $operate;

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
