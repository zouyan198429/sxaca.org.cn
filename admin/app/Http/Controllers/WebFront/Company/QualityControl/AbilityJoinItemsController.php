<?php

namespace App\Http\Controllers\WebFront\Company\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIAbilityJoinItemsBusiness;
use App\Business\Controller\API\QualityControl\CTAPIResourceBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Http\Controllers\WorksController;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbilityJoinItemsController extends BasicController
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
//            return view('company.QualityControl.AbilityJoinItems.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'company.QualityControl.AbilityJoinItems.index', true
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
//            $reDataArr['province_kv'] = CTAPIAbilityJoinItemsBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPIAbilityJoinItemsBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('company.QualityControl.AbilityJoinItems.select', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'admin.QualityControl.RrrDddd.select', true
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
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);

//            return view('company.QualityControl.AbilityJoinItems.add', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

//        $pageNum = ($id > 0) ? 64 : 16;
//        return $this->exeDoPublicFun($request, $pageNum, 1,'company.QualityControl.AbilityJoinItems.add', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
//    }

    /**
     * 单文件上传-上传pdf
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function up_pdf(Request $request)
    {
        $this->InitParams($request);
        // $this->company_id = 1;
        // 企业 的 个人--只能读自己的人员信息
        $organize_id = $this->user_id;// CommonRequest::getInt($request, 'company_id');
        if(!is_numeric($organize_id) || $organize_id <= 0) throws('所属企业参数有误！');

        $userInfo = $this->getStaffInfo($organize_id);
        if(empty($userInfo)) throws('企业记录不存在！');

        // 上传并保存文件
        return CTAPIResourceBusiness::filePlupload($request, $this, 8);
    }

    /**
     * 数据上报
     *
     * @param Request $request
     * @param int $id 报名附表 项目相关表id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function sample_result(Request $request,$id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('company.QualityControl.AbilityJoinItems.sample_result', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);


        return $this->exeDoPublicFun($request, 0, 8, 'company.QualityControl.AbilityJoinItems.sample_result', true
            , '', [], function (&$reDataArr) use ($request, &$id){


                if(!is_numeric($id) || $id <= 0){
                    throws('参数[id]有误！');
                }
                $operate = "数据上报";
                $reDataArr['operate'] = $operate;
                // $handleKeyArr = ['joinItems'];
                $extParams = [
                    // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                    // 'relationFormatConfigs'=> CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $this, ['ability_info', 'join_item_reslut_info_updata', 'project_submit_items_list'], []),// , 'join_items'
                ];


                $info = CTAPIAbilityJoinItemsBusiness::getInfoData($request, $this, $id, [], '', $extParams);
                // $reDataArr = array_merge($reDataArr, $resultDatas);
                if(empty($info)) {
                    throws('记录不存在！');
                }
                $user_info = $this->user_info;
                if($info['admin_type'] != $user_info['admin_type'] || $info['staff_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');

                if(!in_array($info['status'], [2]) || !in_array($info['is_sample'], [2, 8]) || !in_array($info['submit_status'], [1, 4]) ) throws('非已取样状态，不可进行此操作');
                $this->infoItemResult($request, $reDataArr, $info, $info['retry_no']);
            });

    }

    /**
     * 获得指定测试序号的 单次测试数据
     *
     * @param Request $request
     * @param int $id 报名附表 项目相关表id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function sample_result_info(Request $request,$id = 0, $retry_no = 0)
    {

        return $this->exeDoPublicFun($request, 0, 8, 'company.QualityControl.AbilityJoinItems.sample_result_info', true
            , '', [], function (&$reDataArr) use ($request, &$id, &$retry_no){


                if(!is_numeric($id) || $id <= 0){
                    throws('参数[id]有误！');
                }
                $operate = "数据上报";
                $reDataArr['operate'] = $operate;
                // $handleKeyArr = ['joinItems'];
                $extParams = [
                    // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                    // 'relationFormatConfigs'=> CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $this, ['ability_info', 'join_item_reslut_info_updata', 'project_submit_items_list'], []),// , 'join_items'
                ];


                $info = CTAPIAbilityJoinItemsBusiness::getInfoData($request, $this, $id, [], '', $extParams);
                // $reDataArr = array_merge($reDataArr, $resultDatas);
                if(empty($info)) {
                    throws('记录不存在！');
                }
                $user_info = $this->user_info;
                if($info['admin_type'] != $user_info['admin_type'] || $info['staff_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');

                // if(!in_array($info['status'], [2]) || !in_array($info['is_sample'], [2])) throws('非已取样状态，不可进行此操作');
                $this->infoItemResult($request, $reDataArr, $info, $retry_no);
            });
    }

    /**
     * 公用方法
     * 对单次测试数据进行相关表获取---通过 retry_no  测试序号【冗余】 0正常测 1补测1 2 补测2 .....
     *
     * @param Request $request
     * @param array $reDataArr 可以传参到前端视图的变量数组
     * @param int $id 报名附表 项目相关表id
     * @param int $info 当前报名项详情
     * @param int $retry_no 测试序号【冗余】 0正常测 1补测1 2 补测2 .....
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function infoItemResult(Request $request, &$reDataArr, &$info, $retry_no = 0){
        if(!is_numeric($retry_no) || $retry_no < 0) $retry_no = 0;
        $info['retry_no'] = $retry_no;
        $relationFormatConfigs = CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $this, ['ability_info', 'join_item_reslut_info_updata', 'project_submit_items_list'], []);// , 'join_items'
        CTAPIAbilityJoinItemsBusiness::formatRelationList( $request, $this, $info, $relationFormatConfigs);
        // 所用仪器
        $results_instrument_list = $info['join_item_reslut_info_updata']['results_instrument_list'] ?? [];
        // -- 没有，则默认加入一条为0的
        if(empty($results_instrument_list)){
            if(!isset($info['join_item_reslut_info_updata']['results_instrument_list'])) $info['join_item_reslut_info_updata']['results_instrument_list'] = [];
            array_push($info['join_item_reslut_info_updata']['results_instrument_list'], ['id'=> 0]);
        }
        // 检测标准物质
        $results_standard_list = $info['join_item_reslut_info_updata']['results_standard_list'] ?? [];
        // -- 没有，则默认加入一条为0的
        if(empty($results_standard_list)){
            if(!isset($info['join_item_reslut_info_updata']['results_standard_list'])) $info['join_item_reslut_info_updata']['results_standard_list'] = [];
            array_push($info['join_item_reslut_info_updata']['results_standard_list'], ['id'=> 0]);
        }

        // 检测方法依据
        $results_method_list = $info['join_item_reslut_info_updata']['results_method_list'] ?? [];
        // -- 没有，则默认加入一条为0的
        if(empty($results_method_list)){
            if(!isset($info['join_item_reslut_info_updata']['results_method_list'])) $info['join_item_reslut_info_updata']['results_method_list'] = [];
            array_push($info['join_item_reslut_info_updata']['results_method_list'], ['id'=> 0]);
        }

        // pr($info);
        $reDataArr['info'] = $info;
    }

    /**
     * @OA\Get(
     *     path="/api/company/ability_join_items/ajax_info",
     *     tags={"企业后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="companyQualityControlAbilityJoinItemsAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_ability_join_items"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join_items"}
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
//        $info = CTAPIAbilityJoinItemsBusiness::getInfoData($request, $this, $id, [], '', []);
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
     *     path="/api/company/ability_join_items/ajax_save",
     *     tags={"企业后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="companyQualityControlAbilityJoinItemsAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join_items"}
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
//                $type_name = CommonRequest::get($request, 'type_name');
//                $sort_num = CommonRequest::getInt($request, 'sort_num');
//
//                $saveData = [
//                    'type_name' => $type_name,
//                    'sort_num' => $sort_num,
//                ];
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
//                $resultDatas = CTAPIAbilityJoinItemsBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
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
    public function ajax_save_result_sample(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');// 报名项的数据表id
        // CommonRequest::judgeEmptyParams($request, 'id', $id);

        if(!is_numeric($id) || $id <= 0){
            throws('参数[id]有误！');
        }
        $operate = "数据上报";
        // $handleKeyArr = ['joinItems'];
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
             'relationFormatConfigs'=> CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $this, ['join_item_reslut_info_save', 'project_submit_items_list'], []),// , 'join_items'
        ];

        $info = CTAPIAbilityJoinItemsBusiness::getInfoData($request, $this, $id, [], '', $extParams);
        // $reDataArr = array_merge($reDataArr, $resultDatas);
        if(empty($info)) {
            throws('记录不存在！');
        }
        $user_info = $this->user_info;
        if($info['admin_type'] != $user_info['admin_type'] || $info['staff_id'] != $this->user_id) throws('非法访问，您没有访问此记录的权限！');


        if(!in_array($info['status'], [2]) || !in_array($info['is_sample'], [2, 8]) || !in_array($info['submit_status'], [1, 4]) ) throws('非已取样状态，不可进行此操作');
        $item_reslut_info = $info['join_item_reslut_info_save'] ?? [];
        if(empty($item_reslut_info)) throws('还没有能力验证单次结果记录');

        $item_retry_no = $info['retry_no'];

        // 领样列表
        $items_samples_list = $item_reslut_info['items_samples_list'] ?? [];
        if(empty($items_samples_list)) throws('您还没有领样记录！');

        // 每个样品，需要获得的数据信息
        $project_submit_items_list = $info['project_submit_items_list'] ?? [];
        if(empty($project_submit_items_list)) throws('样品验证数据项不存在！');

        $currentNow = Carbon::now()->toDateTimeString();

        // 开始获得数据--样品
        foreach($items_samples_list as $k => $sample_info){
            $tem_sample_id = $sample_info['id'];
            $join_items_sample_result = [];
            $sample_result_list = $sample_info['sample_result_list'] ?? [];// 当前样品，所有的数据
            if(isset($sample_info['sample_result_list'])) unset($sample_info['sample_result_list']);
            foreach($project_submit_items_list as $t_k => $submit_item_info){
                $tem_sample_item_id = $submit_item_info['id'];

                $sample_result = CommonRequest::get($request, 'sample_result_' . $tem_sample_id . '_' . $tem_sample_item_id);
                if($sample_result == '') throws('样品编号：' . $sample_info['sample_one'] . '-【' . $submit_item_info['name'] .'】不能为空！');
                array_push($join_items_sample_result, [
                    'id' => $sample_result_list[$tem_sample_id . '_' . $tem_sample_item_id] ?? 0,
                    'ability_join_item_id' =>  $sample_info['ability_join_item_id'],
                    'retry_no' =>  $sample_info['retry_no'],
                    'result_id' =>  $sample_info['result_id'],
                    'sample_id' =>  $tem_sample_id,
                    'submit_item_id' =>  $tem_sample_item_id,
                    'sample_result' =>  $sample_result,
                ]);
            }
            $items_samples_list[$k] = array_merge($sample_info, [
                'submit_status' => 2,
                'submit_time' => $currentNow,
                'items_sample_result' => $join_items_sample_result,// 只有这个没有id,操作时，再去先查询
            ]);


        }

        // 检测所用仪器
        $instrument_arr = [];
        // id
        $instrument_id = CommonRequest::get($request, 'instrument_id');
        Tool::formatOneArrVals($instrument_id, [null, ''], ',', 1 | 8);
        // 名称/型号
        $instrument_model = CommonRequest::get($request, 'instrument_model');
        Tool::formatOneArrVals($instrument_model, [null, ''], ',', 1 | 8);
        // 出厂编号
        $factory_number = CommonRequest::get($request, 'factory_number');
        Tool::formatOneArrVals($factory_number, [null, ''], ',', 1 | 8);
        // 检定日期
        $check_date = CommonRequest::get($request, 'check_date');
        Tool::formatOneArrVals($check_date, [null, ''], ',', 1 | 8);
        // 有效期
        $valid_date = CommonRequest::get($request, 'valid_date');
        Tool::formatOneArrVals($valid_date, [null, ''], ',', 1 | 8);
        foreach($instrument_id as $k => $instrument_id_val){
            if($instrument_model[$k] == '') throws('检测所用仪器名称/型号不能为空');
            array_push($instrument_arr,[
                'id' => $instrument_id[$k],
                'ability_join_item_id' => $item_reslut_info['ability_join_item_id'],
                'retry_no' => $item_reslut_info['retry_no'],
                'result_id' => $item_reslut_info['id'],
                'instrument_model' => $instrument_model[$k],
                'factory_number' => $factory_number[$k],
                'check_date' => $check_date[$k],
                'valid_date' => $valid_date[$k],
            ]);
        }

        // 标准物质
        $standard_arr = [];
        // id
        $standard_id = CommonRequest::get($request, 'standard_id');
        Tool::formatOneArrVals($standard_id, [null, ''], ',', 1 | 8);
        // 名称
        $standard_name = CommonRequest::get($request, 'standard_name');
        Tool::formatOneArrVals($standard_name, [null, ''], ',', 1 | 8);
        // 生产单位
        $produce_unit = CommonRequest::get($request, 'produce_unit');
        Tool::formatOneArrVals($produce_unit, [null, ''], ',', 1 | 8);
        // 批号
        $batch_number = CommonRequest::get($request, 'batch_number');
        Tool::formatOneArrVals($batch_number, [null, ''], ',', 1 | 8);
        // 有效期
        $standard_valid_date = CommonRequest::get($request, 'standard_valid_date');
        Tool::formatOneArrVals($standard_valid_date, [null, ''], ',', 1 | 8);
        foreach($standard_id as $k => $standard_id_val){
            if($standard_name[$k] == '' && $produce_unit[$k] == '' && $batch_number[$k] == '' && $standard_valid_date[$k] == '') continue;
            if($standard_name[$k] == '') throws('标准物质名称不能为空');
            array_push($standard_arr,[
                'id' => $standard_id[$k],
                'ability_join_item_id' => $item_reslut_info['ability_join_item_id'],
                'retry_no' => $item_reslut_info['retry_no'],
                'result_id' => $item_reslut_info['id'],
                'name' => $standard_name[$k],
                'produce_unit' => $produce_unit[$k],
                'batch_number' => $batch_number[$k],
                'valid_date' => $standard_valid_date[$k],
            ]);
        }

        // 方法依据
        $method_arr = [];
        // id
        $method_id = CommonRequest::get($request, 'method_id');
        Tool::formatOneArrVals($method_id, [null, ''], ',', 1 | 8);
        // 内容
        $content = CommonRequest::get($request, 'content');
        Tool::formatOneArrVals($content, [null, ''], ',', 1 | 8);
        foreach($method_id as $k => $method_id_val){
            if($content[$k] == '') throws('方法依据内容不能为空');
            array_push($method_arr,[
                'id' => $method_id[$k],
                'ability_join_item_id' => $item_reslut_info['ability_join_item_id'],
                'retry_no' => $item_reslut_info['retry_no'],
                'result_id' => $item_reslut_info['id'],
                'content' => replace_enter_char($content[$k], 1),
            ]);
        }

        // 图片资源

        // 图片资源
        $resource_id = CommonRequest::get($request, 'resource_id');
        // 如果是字符，则转为数组
        Tool::formatOneArrVals($resource_id, [null, ''], ',', 1 | 8);
//        if(is_string($resource_id) || is_numeric($resource_id)){
//            if(strlen(trim($resource_id)) > 0){
//                $resource_id = explode(',' ,$resource_id);
//            }
//        }
        if(!is_array($resource_id)) $resource_id = [];
        if(empty($resource_id)) throws('请选择要上传的图片资料！');

        // 再转为字符串
        $resource_ids = implode(',', $resource_id);
        if(!empty($resource_ids)) $resource_ids = ',' . $resource_ids . ',';

        $saveData = [
            // 'status' => 4,
            'join_items_result' => [// 能力验证单次结果
                'id' => $item_reslut_info['id'],
                'status' => 4,
                'submit_status' => 2,
                'submit_time' => $currentNow,
                'results_instrument' => $instrument_arr,// 能力验证检测所用仪器
                // 'results_standard' => $standard_arr,// 能力验证检测标准物质
                'results_method' => $method_arr,// 检测方法依据
                'items_samples' => $items_samples_list,// 能力验证取样登记表
                // 'resource_id' => $resource_id[0] ?? 0,// 第一个图片资源的id
                'resource_type' => 2,
                'resource_ids' => $resource_ids,// 图片资源id串(逗号分隔-未尾逗号结束)
                'resourceIds' => $resource_id,// 此下标为图片资源关系
            ]
        ];
        // 标准物质可以为空
        if(!empty($standard_arr)) $saveData['join_items_result']['results_standard'] = $standard_arr;
        if($item_retry_no == 0){
            $saveData = array_merge($saveData, [
                'submit_status' => 2,
                'submit_time' => $currentNow,
            ]);
        }else{
            $saveData = array_merge($saveData, [
                'submit_status' => 8,
                'submit_time' => $currentNow,
            ]);

        }

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
            // 'methodName' => 'save_result_sample', // 数据上传保存方法
        ];

        $resultDatas = CTAPIAbilityJoinItemsBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * @OA\Get(
     *     path="/api/company/ability_join_items/ajax_alist",
     *     tags={"企业后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--列表",
     *     description="能力验证报名项--列表......",
     *     operationId="companyQualityControlAbilityJoinItemsAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_ability_join_items"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join_items"}
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
//        $user_info = $this->user_info;
//        // 根据条件获得项目列表数据
//        $mergeParams = [
//            'admin_type' => $user_info['admin_type'],
//            'staff_id' => $this->user_id,
//        ];
//        CTAPIAbilityJoinItemsBusiness::mergeRequest($request, $this, $mergeParams);
//        $relations = [];//  ['siteResources']
//        // $handleKeyArr = [];// 'company'
//        $extParams = [
//            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $this, ['ability_info', 'join_item_reslut_info'], []),
//        ];
//
//       $result = CTAPIAbilityJoinItemsBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
//       $data_list = $result['result']['data_list'] ?? [];
//       foreach($data_list as $k => $info){
//           if(!isset($info['join_item_reslut_info']) || empty($info['join_item_reslut_info'])){
//               $data_list[$k]['join_item_reslut_info']['submit_status_text'] = '未上传';
//           }
//       }
//        $result['result']['data_list'] = $data_list;
//       return $result;
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            $user_info = $this->user_info;
            // 根据条件获得项目列表数据
            $mergeParams = [
                'admin_type' => $user_info['admin_type'],
                'staff_id' => $this->user_id,
            ];
            CTAPIAbilityJoinItemsBusiness::mergeRequest($request, $this, $mergeParams);
            $relations = [];//  ['siteResources']
            // $handleKeyArr = [];// 'company'
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $this, ['ability_info', 'join_item_reslut_info', 'join_item_standards'], []),
            ];

            $result = CTAPIAbilityJoinItemsBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
            $data_list = $result['result']['data_list'] ?? [];
            foreach($data_list as $k => $info){
                if(!isset($info['join_item_reslut_info']) || empty($info['join_item_reslut_info'])){
                    $data_list[$k]['join_item_reslut_info']['submit_status_text'] = '未上传';
                }
                $is_publish = $info['ability_info']['is_publish'] ?? 0;
                if($is_publish != 4){// 未公布
                    $data_list[$k]['result_status'] = 0;
                    $data_list[$k]['result_status_text'] = '待公布';
                }

            }
            $result['result']['data_list'] = $data_list;
            return $result;
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
//        $result = CTAPIAbilityJoinItemsBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIAbilityJoinItemsBusiness::getList($request, $this, 1 + 0);
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
//        CTAPIAbilityJoinItemsBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @OA\Post(
     *     path="/api/company/ability_join_items/ajax_del",
     *     tags={"企业后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="companyQualityControlAbilityJoinItemsAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join_items"}
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
//        return CTAPIAbilityJoinItemsBusiness::delAjax($request, $this);
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
//        $childKV = CTAPIAbilityJoinItemsBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIAbilityJoinItemsBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
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
//        $resultDatas = CTAPIAbilityJoinItemsBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPIAbilityJoinItemsBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPIAbilityJoinItemsBusiness::importByFile($request, $this, $fileName);
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

}
