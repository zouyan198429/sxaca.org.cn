<?php

namespace App\Http\Controllers\Expert\QualityControl\Abilitys;

use App\Business\Controller\API\QualityControl\CTAPIAbilityJoinBusiness;
use App\Business\Controller\API\QualityControl\CTAPIAbilityJoinItemsResultsBusiness;
use App\Business\Controller\API\QualityControl\CTAPIAbilitysBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\AbilityJoinItemsResults;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbilityJoinItemsResultsController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    /**
     * 首页
     *
     * @param Request $request
     * @param int $ability_id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request, $ability_id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('expert.QualityControl.AbilitysAdmin.AbilityJoinItemsResults.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'expert.QualityControl.AbilitysAdmin.AbilityJoinItemsResults.index', true
            , 'doListPage', ['ability_id' => $ability_id], function (&$reDataArr) use ($request){

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
//            $reDataArr['province_kv'] = CTAPIAbilityJoinItemsResultsBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPIAbilityJoinItemsResultsBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('expert.QualityControl.AbilitysAdmin.AbilityJoinItemsResults.select', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'expert.QualityControl.RrrDddd.select', true
//            , 'doListPage', [], function (&$reDataArr) use ($request){
//
//            });
//    }


    /**
     * 取样
     *
     * @param Request $request
     * @param int $ability_id
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function get_sample(Request $request, $ability_id = 0, $id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//
//            return view('expert.QualityControl.AbilityJoin.get_sample', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 0, 8, 'expert.QualityControl.AbilitysAdmin.AbilityJoinItemsResults.get_sample', true
            , '', [], function (&$reDataArr) use ($request, &$ability_id, &$id){

                if(!is_numeric($id) || $id <= 0){
                    throws('参数[id]有误！');
                }

                $reDataArr['ability_id'] = $ability_id;

                $abilityInfo = $this->getAbilityInfo($ability_id);
                $reDataArr['info'] = $abilityInfo;

                $operate = "取样";
                // $handleKeyArr = ['joinItems'];
                $extParams = [
                    // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                    'relationFormatConfigs'=> CTAPIAbilityJoinItemsResultsBusiness::getRelationConfigs($request, $this, ['company_info_data' ,'ability_info_name', 'join_items_samples_list'], []),// , 'join_items'
                ];

                $info = CTAPIAbilityJoinItemsResultsBusiness::getInfoData($request, $this, $id, [], '', $extParams);
                // $reDataArr = array_merge($reDataArr, $resultDatas);
                if(empty($info)) {
                    throws('记录不存在！');
                }
                $join_items_samples_list = $info['join_items_samples_list'] ?? [];

                $reDataArr['info'] = $info;
                $reDataArr['operate'] = $operate;
            });
    }

    /**
     * ajax保存数据--取样
     *
     * @param int $id
     * @param int $ability_id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save_sample(Request $request, $ability_id = 0)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');// 报名id
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        //$type_name = CommonRequest::get($request, 'type_name');
        //$sort_num = CommonRequest::getInt($request, 'sort_num');

        if(!is_numeric($id) || $id <= 0){
            throws('参数[id]有误！');
        }

        $reDataArr['ability_id'] = $ability_id;

        $abilityInfo = $this->getAbilityInfo($ability_id);
        $reDataArr['info'] = $abilityInfo;

        // 获得报名信息
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPIAbilityJoinItemsResultsBusiness::getRelationConfigs($request, $this, ['join_items_samples_list'], []),// , 'join_items'
        ];

        $info = CTAPIAbilityJoinItemsResultsBusiness::getInfoData($request, $this, $id, [], '', $extParams);
        if(empty($info)) throws('记录不存在！');
        $status = $info['status'] ?? '';
        if(!in_array($status, [1,2])) throws('不可进行取样操作！');
        $join_item_id = $info['ability_join_item_id'] ?? '0';
        $ability_join_id = $info['ability_join_id'] ?? '0';
        $retry_no = $info['retry_no'] ?? '';
        $currentNow = Carbon::now()->toDateTimeString();

        $join_info = CTAPIAbilityJoinBusiness::getInfoData($request, $this, $ability_join_id, [], '', []);
        if(empty($join_info)) throws('报名记录不存在！');

        $sample_num_data = [];
        $item_sample_nums = CommonRequest::get($request, 'items_samples');
        // 如果是字符，则转为数组
        if(is_string($item_sample_nums) || is_numeric($item_sample_nums)){
            if(strlen(trim($item_sample_nums)) > 0){
                $item_sample_nums = explode(',' ,$item_sample_nums);
            }
        }
        if(!is_array($item_sample_nums)) $item_sample_nums = [];
        if(empty($item_sample_nums)) throws('请输入取样编号');
        $i = 1;
        foreach($item_sample_nums as $sample_number){
            if(empty($sample_number)) continue;
            if(trim($sample_number) == '') continue;
            array_push($sample_num_data, [
                'id' => $info['join_items_samples_list'][$i - 1]['id'] ?? 0,
                'ability_join_item_id' => $join_item_id,
                'retry_no' => $retry_no,
                'serial_number' => $i,
                'sample_one' => $sample_number,
                'result_id' => $id, // 保存时再去查询
                'sample_time' => $currentNow
            ]);
            $i++;
        }
        if(empty($sample_num_data)) throws('没有领样信息');
        // throws(json_encode($sample_num_data));

        $ability_id = $info['ability_id'];
        $ability_info = CTAPIAbilitysBusiness::getInfoData($request, $this, $ability_id, [], '', []);
        if(empty($ability_info)) throws('项目【' . $ability_id . '】 不存在！');
        $duration_minute = $ability_info['duration_minute'];
        $submit_off_time = Tool::addMinusDate(date('Y-m-d 23:59:59'), ['+' . $duration_minute . ' day'], 'Y-m-d H:i:s', 1, '时间');;
        // 更新报名项目
        $save_item_info = [
            'duration_minute' => $duration_minute,
            'submit_off_time' => $submit_off_time,
        ];
        $saveData = [
            'status' => 2,
            'is_sample' => 2,
            'sample_time' => $currentNow,
            'duration_minute' => $duration_minute,
            'submit_off_time' => $submit_off_time,
        ];
        $join_data = [];
        if($join_info['status'] == 1) $join_data['status'] = 4;// 进行中
        if($retry_no == 0){
//            $saveData = array_merge($saveData, [
//                'status' => 4,
//                'is_sample' => 2,
//                'sample_time' => $currentNow,
//            ]);
            $saveData['join_item_data'] = [
                'id' => $join_item_id,
                'status' => 2,
                'is_sample' => 2,
                'sample_time' => $currentNow,
            ];
            $join_data['is_sample'] = 2;
            $join_data['sample_time'] = $currentNow;
        }else{
//            $saveData = array_merge($saveData, [
//                'is_sample' => 8,
//                'sample_time_repair' => $currentNow,
//            ]);
            $saveData['join_item_data'] = [// 报名具体项表
                'id' => $join_item_id,
                'status' => 2,
                'is_sample' => 8,
                'sample_time_repair' => $currentNow,
            ];
            $join_data['is_sample'] = 8;
            $join_data['sample_time_repair'] = $currentNow;
        }
        $saveData['join_item_data'] = array_merge($saveData['join_item_data'], $save_item_info);
        $saveData['sample_num_data'] = $sample_num_data;// 领样表
        $saveData['join_data'] = $join_data;// 报名主表

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
           // 'methodName' => 'sample_save'
        ];
        $resultDatas = CTAPIAbilityJoinItemsResultsBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }


    /**
     * ajax保存数据--已领样，未上传数据的--可以手动直接判断为不满意
     *
     * @param int $id
     * @param int $ability_id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save_dissatisfied(Request $request, $ability_id = 0)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');// 报名id
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        //$type_name = CommonRequest::get($request, 'type_name');
        //$sort_num = CommonRequest::getInt($request, 'sort_num');

        if(!is_numeric($id) || $id <= 0){
            throws('参数[id]有误！');
        }

        $reDataArr['ability_id'] = $ability_id;

        $abilityInfo = $this->getAbilityInfo($ability_id);
        $reDataArr['info'] = $abilityInfo;

        // 获得报名信息
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPIAbilityJoinItemsResultsBusiness::getRelationConfigs($request, $this, ['join_items_samples_list'], []),// , 'join_items'
        ];

        $info = CTAPIAbilityJoinItemsResultsBusiness::getInfoData($request, $this, $id, [], '', $extParams);
        if(empty($info)) throws('记录不存在！');
        $status = $info['status'] ?? '';
        if(!in_array($status, [1,2])) throws('不可进行不满意[未传]操作！');

        $result_status = $info['result_status'] ?? '';
        if($result_status != 1)  throws('非待判定状态，不可进行不满意[未传]操作！');

        $submit_status = $info['submit_status'] ?? '';
        if($submit_status != 1)  throws('非未传，不可进行不满意[未传]操作！');

        $is_sample = $info['is_sample'] ?? '';
        if($is_sample != 2)  throws('非已取样，不可进行不满意[未传]操作！');

        $saveData = [
            'result_status' => 8,// 不满意
        ];
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
            'methodName' => 'judgeResultById'
        ];
        $resultDatas = CTAPIAbilityJoinItemsResultsBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * 添加--判定
     *
     * @param Request $request
     * @param int $ability_id
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function add(Request $request, $ability_id = 0,$id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('expert.QualityControl.AbilitysAdmin.AbilityJoinItemsResults.add', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

        $pageNum = ($id > 0) ? 64 : 16;
        return $this->exeDoPublicFun($request, $pageNum, 1,'expert.QualityControl.AbilitysAdmin.AbilityJoinItemsResults.add', true
            , 'doInfoPage', ['id' => $id, 'ability_id' => $ability_id], function (&$reDataArr) use ($request){

        });
    }

    /**
     * @ OA\Get(
     *     path="/api/expert/ability_join_items/ajax_info",
     *     tags={"大后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="expertQualityControlAbilityJoinItemsAjax_info",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_ability_join_items"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
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
//        $info = CTAPIAbilityJoinItemsResultsBusiness::getInfoData($request, $this, $id, [], '', []);
//        $resultDatas = ['info' => $info];
//        return ajaxDataArr(1, $resultDatas, '');
//
////        $id = CommonRequest::getInt($request, 'id');
////        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
////        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
////
////        });
//    }

    /**
     * @ OA\Post(
     *     path="/api/expert/ability_join_items/ajax_save",
     *     tags={"大后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="expertQualityControlAbilityJoinItemsAjax_save",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join_items"}
     */

    /**
     * ajax保存数据
     *
     * @param int $id
     * @param int $ability_id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save(Request $request, $ability_id = 0)
    {
//        $this->InitParams($request);

        $id = CommonRequest::getInt($request, 'id');
        $pageNum = ($id > 0) ? 256 : 32;
        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
            , '', [], function (&$reDataArr) use ($request, &$ability_id){
                // throws('开发调试中...！');
                $abilityInfo = $this->getAbilityInfo($ability_id);
                $id = CommonRequest::getInt($request, 'id');
                // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                $type_name = CommonRequest::get($request, 'type_name');
//                $sort_num = CommonRequest::getInt($request, 'sort_num');
                $result_status = CommonRequest::getInt($request, 'result_status');

                $saveData = [
//                    'type_name' => $type_name,
//                    'sort_num' => $sort_num,
                    'result_status' => $result_status,
                ];

                $info = CTAPIAbilityJoinItemsResultsBusiness::getInfoData($request, $this, $id, [], '', []);
                if(empty($info)) throws('记录不存在！');
                $status = $info['status'] ?? 0;// 状态1已报名  2已取样  4已传数据   8已判定 16已完成
                $info_result_status = $info['result_status'] ?? 0;// 验证结果1待判定  2满意、4有问题、8不满意   16满意【补测满意】

                if($status != 4 || $info_result_status != 1) throws('非已传数据状态，不可进行此操作！');

        //        // $reDataArr = array_merge($reDataArr, $resultDatas);
                $retry_no = $info['retry_no'] ?? 0;
                // 验证结果1待判定 2满意、4有问题、8不满意   16满意【补测满意】
                $resultStatus = AbilityJoinItemsResults::$resultStatusArr;
                if(isset($resultStatus[1])) unset($resultStatus[1]);// 去掉 1待判定
                if($retry_no == 0){
                    if(isset($resultStatus[16])) unset($resultStatus[16]);// 去掉  16满意【补测满意】
                }else{
                    if(isset($resultStatus[2])) unset($resultStatus[2]);// 去掉 2满意
                }
                if(!in_array($result_status, array_keys($resultStatus))) throws('请选择正确的验证结果');

                $resultDatas = CTAPIAbilityJoinItemsResultsBusiness::judgeResultById($request, $this, $saveData, $id, true);
                return ajaxDataArr(1, $resultDatas, '');
        });
    }

    /**
     * @ OA\Get(
     *     path="/api/expert/ability_join_items/ajax_alist",
     *     tags={"大后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--列表",
     *     description="能力验证报名项--列表......",
     *     operationId="expertQualityControlAbilityJoinItemsAjax_alist",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_optional"),
     *     @ OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_ability_join_items"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
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
    public function ajax_alist(Request $request, $ability_id = 0){
//        $this->InitParams($request);
//        return  CTAPIAbilityJoinItemsResultsBusiness::getList($request, $this, 2 + 4);

        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request, &$ability_id){

            $mergeParams = [
                'ability_id' => $ability_id,
            ];
            CTAPIAbilityJoinItemsResultsBusiness::mergeRequest($request, $this, $mergeParams);

            $handleKeyConfigArr = ['company_info', 'ability_info'];
            // ['results_instrument_list','results_standard_list','results_method_list','items_samples_list','resource_list']
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilityJoinItemsResultsBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];
            return  CTAPIAbilityJoinItemsResultsBusiness::getList($request, $this, 2 + 4, [], [], $extParams);
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
//        $result = CTAPIAbilityJoinItemsResultsBusiness::getList($request, $this, 1 + 0);
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
    public function export(Request $request, $ability_id = 0){
//        $this->InitParams($request);
//        CTAPIAbilityJoinItemsResultsBusiness::getList($request, $this, 1 + 0);
        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request, &$ability_id){

            $mergeParams = [
                'ability_id' => $ability_id,
            ];
            CTAPIAbilityJoinItemsResultsBusiness::mergeRequest($request, $this, $mergeParams);

            $handleKeyConfigArr = ['company_info', 'ability_info'];

            $handleKeyConfigArr = array_merge($handleKeyConfigArr, ['items_samples_list', 'project_submit_items_list']);
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilityJoinItemsResultsBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];
            return  CTAPIAbilityJoinItemsResultsBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
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
//        CTAPIAbilityJoinItemsResultsBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @ OA\Post(
     *     path="/api/expert/ability_join_items/ajax_del",
     *     tags={"大后台-能力验证-能力验证报名项"},
     *     summary="能力验证报名项--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="expertQualityControlAbilityJoinItemsAjax_del",
     *     deprecated=false,
     *     @ OA\Parameter(ref="#/components/parameters/Accept"),
     *     @ OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_items_id_required"),
     *     @ OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @ OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @ OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
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
////        $this->InitParams($request);
////        return CTAPIAbilityJoinItemsResultsBusiness::delAjax($request, $this);
//
//        $tem_id = CommonRequest::get($request, 'id');
//        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
//        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            return CTAPIAbilityJoinItemsResultsBusiness::delAjax($request, $this);
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
//        $childKV = CTAPIAbilityJoinItemsResultsBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIAbilityJoinItemsResultsBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
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
//        $resultDatas = CTAPIAbilityJoinItemsResultsBusiness::importByFile($request, $this, $fileName);
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
//        $resultDatas = CTAPIAbilityJoinItemsResultsBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPIAbilityJoinItemsResultsBusiness::importByFile($request, $this, $fileName);
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
        $ability_id = $extendParams['params']['ability_id'];
        $reDataArr['ability_id'] = $ability_id;

        $abilityInfo = $this->getAbilityInfo($ability_id);
        $reDataArr['info'] = $abilityInfo;

        // 拥有者类型1平台2企业4个人
        $reDataArr['adminType'] =  AbilityJoinItemsResults::$adminTypeArr;
        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

        // 是否补测 0正常测 1补测1 2 补测2 .....
        $retry_no = CommonRequest::get($request, 'retry_no');
        $reDataArr['retryNo'] =  AbilityJoinItemsResults::$retryNoArr;
        $reDataArr['defaultRetryNo'] = (strlen($retry_no) > 0) ? $retry_no : -1;// 列表页默认

        // 状态
        $reDataArr['status'] =  AbilityJoinItemsResults::$statusArr;
        $reDataArr['defaultStatus'] = -1;// 列表页默认

        // 验证结果1待判定 2满意、4有问题、8不满意   16满意【补测满意】
        $reDataArr['resultStatus'] =  AbilityJoinItemsResults::$resultStatusArr;
        $reDataArr['defaultResultStatus'] = -1;// 列表页默认

        // 是否取样1待取样--未取 2已取样--已取
        $reDataArr['isSample'] =  AbilityJoinItemsResults::$isSampleArr;
        $reDataArr['defaultIsSample'] = -1;// 列表页默认状态

        // 是否上传数据1待传 --未传  2 已传
        $submit_status = CommonRequest::get($request, 'submit_status');
        $reDataArr['submitStatus'] =  AbilityJoinItemsResults::$submitStatusArr;
        $reDataArr['defaultSubmitStatus'] =  (strlen($submit_status) > 0) ? $submit_status : -1;// 列表页默认状态

        // 是否评定1待评  2 已评
        $judge_status = CommonRequest::get($request, 'judge_status');
        $reDataArr['judgeStatus'] =  AbilityJoinItemsResults::$judgeStatusArr;
        $reDataArr['defaultJudgeStatus'] =  (strlen($judge_status) > 0) ? $judge_status : -1;// 列表页默认状态

        // 操作类型 1 参加单位 2 上传数据 3 未合格单位 4 补测数据
        $operate_num = CommonRequest::get($request, 'operate_num');
        $reDataArr['operate_num'] = $operate_num;
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
        $ability_id = $extendParams['params']['ability_id'] ?? 0;
        $reDataArr['ability_id'] = $ability_id;

        $info = [
            'id'=>$id,
            //   'department_id' => 0,
        ];
        $operate = "添加";

//
        if ($id > 0) { // 获得详情数据
            $operate = "修改";
            $handleKeyConfigArr = ['company_info', 'ability_info'];

            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilityJoinItemsResultsBusiness::getRelationConfigs($request, $this, $handleKeyConfigArr, []),
            ];
            $info = CTAPIAbilityJoinItemsResultsBusiness::getInfoData($request, $this, $id, [], '', $extParams);
        }
        $status = $info['status'] ?? 0;// 状态1已报名  2已取样  4已传数据   8已判定 16已完成
        $result_status = $info['result_status'] ?? 0;// 验证结果1待判定  2满意、4有问题、8不满意   16满意【补测满意】

        if($status != 4 || $result_status != 1) throws('非已传数据状态，不可进行此操作！');

//        // $reDataArr = array_merge($reDataArr, $resultDatas);
        $reDataArr['info'] = $info;
        $reDataArr['operate'] = $operate;
        $retry_no = $info['retry_no'] ?? 0;
        // 验证结果1待判定 2满意、4有问题、8不满意   16满意【补测满意】
        $resultStatus = AbilityJoinItemsResults::$resultStatusArr;
        if(isset($resultStatus[1])) unset($resultStatus[1]);// 去掉 1待判定
        if($retry_no == 0){
            if(isset($resultStatus[16])) unset($resultStatus[16]);// 去掉  16满意【补测满意】
        }else{
            if(isset($resultStatus[2])) unset($resultStatus[2]);// 去掉 2满意
        }
        // 验证结果1待判定 2满意、4有问题、8不满意   16满意【补测满意】
        $reDataArr['resultStatus'] = array_reverse($resultStatus, true);
        $reDataArr['defaultResultStatus'] = $info['result_status'] ?? -1;// 列表页默认

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
