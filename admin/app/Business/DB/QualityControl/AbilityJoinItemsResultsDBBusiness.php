<?php
// 能力验证单次结果
namespace App\Business\DB\QualityControl;

use App\Models\QualityControl\AbilityJoinItemsResults;
use App\Models\QualityControl\AbilityJoinItemsSamples;
use App\Services\DB\CommonDB;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class AbilityJoinItemsResultsDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItemsResults';
    public static $table_name = 'ability_join_items_results';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        return CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate){
            //        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
            //            throws('联系人不能为空！');
            //        }
            //
            //        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
            //            throws('手机不能为空！');
            //        }

            // 能力验证检测所用仪器
            $results_instrument = [];
            $has_results_instrument = false;// 是否有 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'results_instrument', $has_results_instrument, $results_instrument, 1);

            // 能力验证检测标准物质
            $results_standard = [];
            $has_results_standard = false;// 是否有 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'results_standard', $has_results_standard, $results_standard, 1);

            // 检测方法依据
            $results_method = [];
            $has_results_method = false;// 是否有 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'results_method', $has_results_method, $results_method, 1);

            // 能力验证取样登记表
            $items_samples = [];
            $has_items_samples = false;// 是否有 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'items_samples', $has_items_samples, $items_samples, 1);

            // 是否有图片资源
            $hasResource = false;
            $resourceIds = [];
            if(Tool::getInfoUboundVal($saveData, 'resourceIds', $hasResource, $resourceIds, 1)){
                // $saveData['resource_id'] = $resourceIds[0] ?? 0;// 第一个图片资源的id
            }
            $resource_ids = $saveData['resource_ids'] ?? '';// 图片资源id串(逗号分隔-未尾逗号结束)
            // if(isset($saveData['resource_ids']))  unset($saveData['resource_ids']);
            // if(isset($saveData['resource_id']))  unset($saveData['resource_id']);

            // 取样功能

            // 样品编号
            $sample_num_data = [];
            $has_sample_num_data = false;// 是否有 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'sample_num_data', $has_sample_num_data, $sample_num_data, 1);

            // 报名项
            $join_item_data = [];
            $has_join_item_data = false;// 是否有 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'join_item_data', $has_join_item_data, $join_item_data, 1);

            // 报名表
            $join_info_data = [];
            $has_join_info_data = false;// 是否有 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'join_data', $has_join_info_data, $join_info_data, 1);

            $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
            // 保存前的处理
            static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
            $modelObj = null;
            // ******************************************************************
            $isModify = false;

            // $ownProperty  自有属性值;
            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
            if($id > 0){
                $isModify = true;
                // 判断权限
                //            $judgeData = [
                //                'company_id' => $company_id,
                //            ];
                //            $relations = '';
                //            static::judgePower($id, $judgeData , $company_id , [], $relations);
                if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);

            }else {// 新加;要加入的特别字段
                //            $addNewData = [
                //                'company_id' => $company_id,
                //            ];
                //            $saveData = array_merge($saveData, $addNewData);
                // 加入操作人员信息
                if($temNeedStaffIdOrHistoryId) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);
            }

            // 新加或修改
            $resultDatas = [];
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData,$modelObj);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }

            // 能力验证检测所用仪器修改
            if($has_results_instrument){
                $results_instrument_ids = AbilityJoinItemsResultsInstrumentDBBusiness::updateByDataList(['result_id' => $id], ['result_id' => $id]
                    , $results_instrument, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }

            // 能力验证检测标准物质修改
            if($has_results_standard){
                $results_standard_ids = AbilityJoinItemsResultsStandardDBBusiness::updateByDataList(['result_id' => $id], ['result_id' => $id]
                    , $results_standard, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }

            // 检测方法依据修改
            if($has_results_method){
                $results_method_ids = AbilityJoinItemsResultsMethodDBBusiness::updateByDataList(['result_id' => $id], ['result_id' => $id]
                    , $results_method, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }

            // 能力验证取样登记表修改
            if($has_items_samples){
                $items_samples_ids = AbilityJoinItemsSamplesDBBusiness::updateByDataList(['result_id' => $id], ['result_id' => $id]
                    , $items_samples, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }

            // 能力验证取样登记表修改
            if($has_sample_num_data){

                $items_samples_num_ids = AbilityJoinItemsSamplesDBBusiness::updateByDataList(['result_id' => $id], ['result_id' => $id]
                    , $sample_num_data, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);

            }

            // 能力验证报名项表修改
            if($has_join_item_data){
                if(empty($resultDatas)) $resultDatas = static::getInfo($id);
                $join_item_data['operate_staff_id'] = $operate_staff_id;
                $join_item_data['operate_staff_id_history'] = $operate_staff_id_history;
                $ability_join_item_id = $resultDatas['ability_join_item_id'];
                AbilityJoinItemsDBBusiness::replaceById($join_item_data, $company_id, $ability_join_item_id, $operate_staff_id, $modifAddOprate);
//                $items_item_ids = AbilityJoinItemsDBBusiness::updateByDataList(['id' => $resultDatas['ability_join_item_id']], ['id' => $resultDatas['ability_join_item_id']]
//                    , $join_item_data, $isModify, $operate_staff_id, $operate_staff_id_history
//                    , 'id', $company_id, $modifAddOprate, []);
            }

            // 能力验证报名表修改
            if($has_join_info_data){
                if(empty($resultDatas)) $resultDatas = static::getInfo($id);
                $join_info_data['operate_staff_id'] = $operate_staff_id;
                $join_info_data['operate_staff_id_history'] = $operate_staff_id_history;
                $ability_join_id = $resultDatas['ability_join_id'];
                AbilityJoinDBBusiness::replaceById($join_info_data, $company_id, $ability_join_id, $operate_staff_id, $modifAddOprate);
                // 记录报名日志
                // 获得操作人员信息
                $operateInfo = StaffDBBusiness::getInfo($operate_staff_id);
                $logContent = '取样操作：' . json_encode(['join' => $join_info_data, 'join_item' => $join_item_data, 'join_item_result' => $saveData, 'sample_num' => $sample_num_data]);
                $ability_join_item_id = $resultDatas['ability_join_item_id'];
                AbilityJoinLogsDBBusiness::saveAbilityJoinLog($operateInfo['admin_type'], $operate_staff_id, $ability_join_id, $ability_join_item_id, $logContent, $operate_staff_id, $operate_staff_id_history);

            }

            // 同步修改图片资源关系
            if($hasResource){
//                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
//                // 更新图片资源表
//                if(!empty($resourceIds)) {
//                    $resourceArr = ['column_type' => 4, 'column_id' => $id];
//                    ResourceDBBusiness::saveByIds($resourceArr, $resourceIds);
//                }
                ResourceDBBusiness::resourceSync(static::thisObj(), 4, $id, $resourceIds, [], $operate_staff_id, $operate_staff_id_history);
            }
            // *********************************************************
            // 保存成功后的处理
            static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
            return $id;
        });
    }

    /**
     * 根据id判断记录结果
     *
     * @param array $saveData 要保存或修改的数组 [ 'result_status' =>  2满意、4有问题、8不满意   16满意【补测满意】 ]
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id--为0，测为系统脚本运行的--自动不满意
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeResultById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){
         $result_status = $saveData['result_status'] ?? 0;
//        if(!in_array($result_status, array_keys(AbilityJoinItemsResults::$resultStatusArr))) throws('参数【result_status】值无效！');
        $info = static::getInfo($id);
        if(empty($info)) throws('记录不存在！');
        $status = $info['status'] ?? 0;// 状态1已报名  2已取样  4已传数据   8已判定 16已完成
        $info_result_status = $info['result_status'] ?? 0;// 验证结果1待判定  2满意、4有问题、8不满意   16满意【补测满意】

        // 人员操作的  $status != 4 ||
        if($operate_staff_id > 0 && ( !in_array($status, [1,2,4]) || $info_result_status != 1) ) throws('非已传数据状态，不可进行此操作！');
        // 脚本跑的
        if($operate_staff_id <= 0 && ( !in_array($status, [2])|| $info_result_status != 1 )) throws('非已传数据状态，不可进行此操作！');
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
        $result_status_text = $resultStatus[$result_status] ?? '';
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);
        // 具体操作
        // 有问题  不满意
        //     如果是第一次测试：
        //            项目表 abilitys   初测不满意数 first_fail_num + 1
        //            主报名表 abilityJoin 状态改为 status 2' => '补测待取样； 初测不满意数 first_fail_num + 1
        //            报名项表 ability_join_items  status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；
        //                                          改为补测  retry_no =1 并生成新的 补测单次结果
        //            对当前的这条单次记录 ability_join_items_results    status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；
        // 如果是第二次补测
        //           项目表 abilitys   补测不满意数 repair_fail_num + 1 ; 判断是否都判定完了【初测 和 补测】 -- 未完，不操作；完了 is_publish   2待公布
        //           主报名表 abilityJoin  判断是否都判定完了【初测 和 补测】 status 状态改为 未完-- 8 部分评定【还有没有评定的】   或  已完-- 16 已评定【所有报名项都评定了】；
        //                                              补测不满意数 repair_fail_num + 1
        //            报名项表 ability_join_items status 状态改为 8 已判定 ； result_status ；是否评定  2 已评 judge_time；
        //           对当前的这条单次记录 ability_join_items_results  status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；
        // 满意
        //     如果是第一次测试： 第二次补测  补测满意数 repair_success_num + 1
        //            项目表 abilitys  初测满意数 first_success_num + 1; 判断是否都判定完了【初测 和 补测】 -- 未完，不操作；完了 is_publish   2待公布
        //            主报名表 abilityJoin 判断是否都判定完了【初测 和 补测】 status 状态改为 未完-- 8 部分评定【还有没有评定的】   或  已完-- 16 已评定【所有报名项都评定了】；
        //                                  初测满意数 first_success_num + 1;
        //            报名项表 ability_join_items status  status 状态改为 8 已判定 ； result_status ；是否评定  2 已评 judge_time；
        //           对当前的这条单次记录 ability_join_items_results  status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；

        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$operate_staff_id_history, &$modifAddOprate, &$result_status, &$retry_no, &$info, &$result_status_text){

            // $ownProperty  自有属性值;
            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
            if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);
            $ability_id = $info['ability_id'];// 所属能力验证
            $ability_join_id = $info['ability_join_id'];// 所属报名主表
            $ability_join_item_id = $info['ability_join_item_id'];// 所属能力验证报名项
            $admin_type = $info['admin_type'];// 类型1平台2企业4个人
            $staff_id = $info['staff_id'];// 所属人员id
            $currentNow = Carbon::now()->toDateTimeString();

            $abilityInfo = AbilitysDBBusiness::getInfo($ability_id);
            if(empty($abilityInfo)) throws('项目记录不存在！');
            // 8已结束 16 已取消【作废】)
            if(in_array($abilityInfo['status'], [8,16]))  throws('项目记录状态有误，不可进行此操作！');
            if(!in_array($abilityInfo['status'], [4]))throws('项目记录状态非进行中，不可进行此操作！');

            $joinInfo = AbilityJoinDBBusiness::getInfo($ability_join_id);
            if(empty($joinInfo)) throws('报名主记录不存在！');
            if(!in_array($joinInfo['status'],[4,8])) throws('报名主记录非进行中或部分评定状态，不可进行此操作！');

            $joinItemInfo = AbilityJoinItemsDBBusiness::getInfo($ability_join_item_id);
            if(empty($joinItemInfo)) throws('报名项记录不存在！');
            if(!in_array($joinItemInfo['status'],[2,4])) throws('报名项记录非进行中状态，不可进行此操作！');

            // 记录报名日志
            // 获得操作人员信息
            $operateInfo = StaffDBBusiness::getInfo($operate_staff_id);
            // 如果是脚本，测有为空的可能
            $admin_type = $operateInfo['admin_type'] ?? 0;
            $logContent = '判定[' . $result_status_text . ']数据：' . json_encode($saveData);
            if($operate_staff_id <= 0) $logContent = '企业上传数据超时，系统自动做不满意处理。' . $logContent;
                // $ability_join_id = $resultDatas['ability_join_id'] ?? 0;
            AbilityJoinLogsDBBusiness::saveAbilityJoinLog($admin_type, $operate_staff_id, $ability_join_id, $ability_join_item_id, $logContent, $operate_staff_id, $operate_staff_id_history);

            // 4有问题、8不满意
            if(in_array($result_status, [4, 8])){
                switch($retry_no){
                    case 0:// 初测
                        // 项目表 abilitys   初测不满意数 first_fail_num + 1
                        AbilitysDBBusiness::fieldValIncDec($ability_id, 'first_fail_num', 1, 'inc');
                        //  补测企业数量 + 1
                        AbilitysDBBusiness::fieldValIncDec($ability_id, 'repair_num', 1, 'inc');
                        // 主报名表 abilityJoin retry_no 改为补测 is_sample 4  补测待取样 8 进行中[有评定]； 初测不满意数 first_fail_num + 1
                        AbilityJoinDBBusiness::fieldValIncDec($ability_join_id, 'first_fail_num', 1, 'inc');
                        AbilityJoinDBBusiness::saveById(['retry_no' => 1, 'is_sample' => 4, 'status' => 8], $ability_join_id);
                        //            报名项表 ability_join_items  8 已判定 result_status ；judge_status 是否评定  2 已评 judge_time；
                        //
                        AbilityJoinItemsDBBusiness::saveById([
                            'retry_no' => 1,
                             'status' => 8,
                            'result_status' => $result_status,
                            'judge_status' => 2,
                            'judge_time' => $currentNow,
                            'is_sample' => 4,
                             'submit_status' => 4,
                            // 'judge_status' => 4,
                        ], $ability_join_item_id);
                        // 生成新的补测单结果
                        $updateFields = [
                            'ability_join_item_id' => $ability_join_item_id,
                            'retry_no' => 1,
                            'admin_type' => $joinItemInfo['admin_type'],
                            'staff_id' => $joinItemInfo['staff_id'],
                            'ability_join_id' => $joinItemInfo['ability_join_id'],
                            'ability_code' => $joinItemInfo['ability_code'],
                            'contacts' => $joinItemInfo['contacts'],
                            'mobile' => $joinItemInfo['mobile'],
                            'tel' => $joinItemInfo['tel'],
                            'ability_id' => $joinItemInfo['ability_id'],
                            'join_time' => $joinItemInfo['join_time'],
                            'status' => 1,
                            'is_sample' => 1,
                            // 'sample_time' => null,
                            'submit_status' => 1,
                            // 'submit_time' => null,
                            'judge_status' => 1,
                            // 'judge_time' => null,
                            'result_status' => 1,
                            'resource_ids' => '',
                            'submit_remarks' => '',
                        ];
                        $searchConditon = [
                            'ability_join_item_id' => $ability_join_item_id,
                            'retry_no' => 1,
                        ];
                        $itemResultObj = null;
                        static::updateOrCreate($itemResultObj, $searchConditon, $updateFields);

                        // 记录报名日志--新建补测
                        // 获得操作人员信息
                        // $operateInfo = StaffDBBusiness::getInfo($operate_staff_id);
                        $logContent = '新建补测数据：' . json_encode(array_merge($updateFields, $searchConditon));
                        // $ability_join_id = $resultDatas['ability_join_id'] ?? 0;
                        AbilityJoinLogsDBBusiness::saveAbilityJoinLog($admin_type, $operate_staff_id, $ability_join_id, $ability_join_item_id, $logContent, $operate_staff_id, $operate_staff_id_history);



                        //            对当前的这条单次记录 ability_join_items_results    status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；
                        static::saveById([
                            'status' => 16,
                            'result_status' => $result_status,
                            'judge_status' => 2,
                            'judge_time' => $currentNow,
                        ],$id);
                        break;
                    case 1:// 补测
                        //           对当前的这条单次记录 ability_join_items_results  status 8 已判定 ； result_status ；judge_status 是否评定  2 已评 judge_time；
                        static::saveById([
                            'status' => 16,
                            'result_status' => $result_status,
                            'judge_status' => 2,
                            'judge_time' => $currentNow,
                        ],$id);

                        // 报名项表 ability_join_items status 状态改为 32 无证 ； result_status ；是否评定  8  已评[补测] judge_time；

                        AbilityJoinItemsDBBusiness::saveById([
                            'status' => 32,
                            'result_status' => $result_status,
                            'judge_status' => 8,
                            'judge_time' => $currentNow,
                            // 'judge_status' => 4,
                        ], $ability_join_item_id);

                        //           主报名表 abilityJoin  判断是否都判定完了【初测 和 补测】 status 状态改为 未完-- 8 部分评定【还有没有评定的】   或  已完-- 16 已评定【所有报名项都评定了】；
                        //                                              补测不满意数 repair_fail_num + 1
                        AbilityJoinDBBusiness::fieldValIncDec($ability_join_id, 'repair_fail_num', 1, 'inc');
                        // 企业报名的状态可能是
                        //     还有没有判定的 时  8 有判定
                        //     全判定时   没有一个满意的： 32 无证书  ； 有一个满意的：16  待发证

                        $tem_join_result_status  = 8;
                        // 获得正在处理的一条记录
                        // , 'admin_type' => $admin_type, 'staff_id' => $staff_id
                        $queryParams = Tool::getParamQuery(['ability_join_id' => $ability_join_id], ['sqlParams' =>['whereIn' => ['status' => [1,2,4]]]], []);
                        $resultDoingInfo = static::getInfoByQuery(1, $queryParams, []);
                        if(empty($resultDoingInfo)){// 全都判定了
                            // 判断是否有一个满意的
                            // , 'admin_type' => $admin_type, 'staff_id' => $staff_id
                            $queryParams = Tool::getParamQuery(['ability_join_id' => $ability_join_id], ['sqlParams' =>['whereIn' => ['status' => [8,32]]]], []);
                            $resultSuccInfo = static::getInfoByQuery(1, $queryParams, []);
                            if(empty($resultSuccInfo)) $tem_join_result_status  = 32;// 没有一个满意的
                            if(!empty($resultSuccInfo))   $tem_join_result_status  = 16; // 肯定有一个满意的
                        }
                        AbilityJoinDBBusiness::saveById([
                            'status' => $tem_join_result_status
                        ], $ability_join_id);

                        // 整个项目的--- 判断是否还有未判定的项目
                        //           项目表 abilitys   补测不满意数 repair_fail_num + 1 ; 判断是否都判定完了【初测 和 补测】 -- 未完，不操作；完了 is_publish   2待公布
                        AbilitysDBBusiness::fieldValIncDec($ability_id, 'repair_fail_num', 1, 'inc');
                        // 状态可能是
                        //    还有没有要判定的  有  : 状态不变
                        //                      没有（全都判定时）：  状态 不变  is_publish 2 待公布；
                        // 获得正在处理的一条记录
                        $queryParams = Tool::getParamQuery(['ability_id' => $ability_id], ['sqlParams' =>['whereIn' => ['status' => [1,2,4]]]], []);
                        $resultDoingInfo = static::getInfoByQuery(1, $queryParams, []);
                        if(empty($resultDoingInfo)){// 全都判定了
                            $tem_ability_save = [
                                'is_publish' => 2,
                            ];
                            // 没有一条要发证的
//                            $queryParams = Tool::getParamQuery(['ability_id' => $ability_id], ['sqlParams' =>['whereIn' => ['status' => [1,2,4,8]]]], []);
//                            $resultGrantInfo = static::getInfoByQuery(1, $queryParams, []);
//                            if(empty($resultGrantInfo)) $tem_ability_save['status'] = 8;

                            // AbilitysDBBusiness::saveById($tem_ability_save, $ability_id);
                            $queryUpdateParams = Tool::getParamQuery(['id' => $ability_id, 'is_publish' => 1 ], [], []);
                            AbilitysDBBusiness::save($tem_ability_save, $queryUpdateParams);
                        }
                        break;
                    default:
                        break;
                }
            }else{// 2满意  16满意【补】
                $incFieldName = ($result_status == 2) ? 'first_success_num' : 'repair_success_num';
                // 对当前的这条单次记录 ability_join_items_results  status 8 待发证 ； result_status ；judge_status 是否评定  2 已评 judge_time；
                static::saveById([
                    'status' => 8,
                    'result_status' => $result_status,
                    'judge_status' => 2,
                    'judge_time' => $currentNow,
                ],$id);
                // 报名项表 ability_join_items status  status 状态改为 16 待发证 ； result_status ；是否评定  2 已评 judge_time；

                AbilityJoinItemsDBBusiness::saveById([
                    'status' => 16,
                    'result_status' => $result_status,
                    'judge_status' => ($result_status == 2) ? 2 : 8,
                    'judge_time' => $currentNow,
                ], $ability_join_item_id);

                //            主报名表 abilityJoin 判断是否都判定完了【初测 和 补测】 status 状态改为 未完-- 8 部分评定【还有没有评定的】   或  已完-- 16 已评定【所有报名项都评定了】；
                //                                  初测满意数 first_success_num + 1;

                AbilityJoinDBBusiness::fieldValIncDec($ability_join_id, $incFieldName, 1, 'inc');
                // 企业报名的状态可能是
                //     还有没有判定的 时  8 有判定
                //     全判定时   16  待发证

                $tem_join_result_status  = 8;
                // 获得正在处理的一条记录
                // , 'admin_type' => $admin_type, 'staff_id' => $staff_id
                $queryParams = Tool::getParamQuery(['ability_join_id' => $ability_join_id], ['sqlParams' =>['whereIn' => ['status' => [1,2,4]]]], []);
                $resultDoingInfo = static::getInfoByQuery(1, $queryParams, []);
                if(empty($resultDoingInfo)){
                    // 判断是否有一个满意的
                    $tem_join_result_status  = 16; // 肯定有一个满意的
                }
                AbilityJoinDBBusiness::saveById([
                    'status' => $tem_join_result_status
                ], $ability_join_id);
                // 满意项目数量 + 1
                AbilityJoinDBBusiness::fieldValIncDec($ability_join_id, 'passed_num', 1, 'inc');

                //            项目表 abilitys  初测满意数 first_success_num + 1; 判断是否都判定完了【初测 和 补测】 -- 未完，不操作；完了 is_publish   2待公布
                 AbilitysDBBusiness::fieldValIncDec($ability_id, $incFieldName, 1, 'inc');

                //    还有没有要判定的  有  : 状态不变
                //                      没有（全都判定时）：  状态 不变  is_publish 2 待公布；
                // 获得正在处理的一条记录

                $queryParams = Tool::getParamQuery(['ability_id' => $ability_id], ['sqlParams' =>['whereIn' => ['status' => [1,2,4]]]], []);

                $resultDoingInfo = static::getInfoByQuery(1, $queryParams, []);
                if(empty($resultDoingInfo)){
//                    AbilitysDBBusiness::saveById([
//                        'is_publish' => 2,
//                    ], $ability_id);
                    $queryUpdateParams = Tool::getParamQuery(['id' => $ability_id, 'is_publish' => 1 ], [], []);

                    AbilitysDBBusiness::save([
                        'is_publish' => 2,
                    ],$queryUpdateParams);
                }
            }
        });
        return $id;
    }

    // 如果企业没有按时提交数据，则自动判定结果为不满意--上传数据超时
    public static function autosSubmitOverTime(){
        $dateTime =  date('Y-m-d H:i:s');
        // 读取所有未开始的
//        $queryParams = [
//            'where' => [
//                // ['status', 2],
//                ['join_end_date', '<=', $dateTime],
//            ],
//            'whereIn' => [ 'status' => [1,2]],
//            'select' => ['id' ]
//        ];
        $queryParams = Tool::getParamQuery(['is_sample' => 2, 'submit_status' => 1, 'status' => 2], ['sqlParams' =>['select' =>['id' ], 'where' => [['submit_off_time', '<=', $dateTime]]]], []);
        $dataList = static::getAllList($queryParams, [])->toArray();

        if(!empty($dataList)){
            $ids = array_values(array_unique(array_column($dataList,'id')));

            // 去掉标记为000的样品--以后这里可以去掉
            $queryParams = Tool::getParamQuery(['result_id' => $ids, 'submit_status' => 1, 'sample_one' => '000'], ['sqlParams' =>['select' =>['id', 'result_id']]], []);
            $sampleDataList = AbilityJoinItemsSamplesDBBusiness::getAllList($queryParams, [])->toArray();
            $delIds = array_values(array_unique(array_column($sampleDataList,'result_id')));
            if(!empty($delIds))  $ids = array_diff($ids, $delIds);

            // 自动进行不满意操作
            if(!empty($ids)){
                foreach($ids as $tem_id){
                    $saveData = [
                        'result_status' => 8,// 不满意
                    ];
                    $company_id = 0;
                    $user_id = 0;
                    $modifAddOprate = 0;
                    static::judgeResultById($saveData, $company_id, $tem_id, $user_id, $modifAddOprate);
                }
            }
        }
    }

    /**
     * 根据项目id--获得指定的单个或多个状态的数量
     * @param mixed $status 单个或 多个：一维数组 ； 状态1已报名  2已取样  4已传数据   8已判定 16已完成
     * @param int $retry_no 测试序号 0正常测 1补测1 2 补测2 .....
     * @param int  $ability_id  项目id --可以为0：不参与查询
     * @param int  $admin_type  类型1平台2企业4个人--可以为0：不参与查询
     * @param int  $staff_id  所属人员id--可以为0：不参与查询
     * @return  mixed 数量
     * @author zouyan(305463219@qq.com)
     */
    public static function getCountNum($status = 1, $retry_no = 0, $ability_id = 0, $admin_type = 0, $staff_id = 0){
        $fieldValParams = [];
        if(is_numeric($retry_no) && $retry_no >= 0) $fieldValParams['retry_no'] = $retry_no;
        if(is_numeric($ability_id) && $ability_id > 0) $fieldValParams['ability_id'] = $ability_id;
        if(is_numeric($admin_type) && $admin_type > 0) $fieldValParams['admin_type'] = $admin_type;
        if(is_numeric($staff_id) && $staff_id > 0) $fieldValParams['staff_id'] = $staff_id;
        if(!empty($fieldValParams)) $queryParams = Tool::getParamQuery($fieldValParams, [], []);
        if(!empty($status)) Tool::appendParamQuery($queryParams, $status, 'status', [0, '0', ''], ',', false);

        $queryParams['count'] = 0;
        return static::getAllList($queryParams, []);
    }

    /**
     * 根据项目id--获得指定的单个记录
     * @param mixed $status 单个或 多个：一维数组 ； 状态1已报名  2已取样  4已传数据   8已判定 16已完成
     * @param int $retry_no 测试序号 0正常测 1补测1 2 补测2 .....
     * @param int  $ability_id  项目id --可以为0：不参与查询
     * @param int  $admin_type  类型1平台2企业4个人--可以为0：不参与查询
     * @param int  $staff_id  所属人员id--可以为0：不参与查询
     * @return  mixed 数量
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoOne($status = 1, $retry_no = 0, $ability_id = 0, $admin_type = 0, $staff_id = 0){
        $fieldValParams = [];
        if(is_numeric($retry_no) && $retry_no >= 0) $fieldValParams['retry_no'] = $retry_no;
        if(is_numeric($ability_id) && $ability_id > 0) $fieldValParams['ability_id'] = $ability_id;
        if(is_numeric($admin_type) && $admin_type > 0) $fieldValParams['admin_type'] = $admin_type;
        if(is_numeric($staff_id) && $staff_id > 0) $fieldValParams['staff_id'] = $staff_id;
        if(!empty($fieldValParams)) $queryParams = Tool::getParamQuery($fieldValParams, [], []);
        if(!empty($status)) Tool::appendParamQuery($queryParams, $status, 'status', [0, '0', ''], ',', false);
        $info = static::getInfoByQuery(1, $queryParams, []);
        return $info;
    }


    /**
     * 导入数据
     *
     * @param int  $test_year 年 如 2021
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id 操作的
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 单条数据 - 记录的id数组--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function importDatas($test_year, $saveData, $company_id, $operate_staff_id = 0, $modifAddOprate = 0){
//        ini_set('memory_limit','3072M');    // 临时设置最大内存占用为 3072M 3G
//        ini_set("max_execution_time", 0);
//        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期
        Tool::phpInitSet();
        $returnIds = [];
        if(empty($saveData)) return $returnIds;
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());

        // 验证结果1待判定 2满意、4有问题、8不满意   16满意【补测满意】
        $resultStatus = AbilityJoinItemsResults::$resultStatusArr;
        if(isset($resultStatus[1])) unset($resultStatus[1]);// 去掉 1待判定

        // 获得能力验证单次结果信息
        $abilityCodeArr = Tool::getArrFields($saveData, 'test_code');
        $resultDataList = AbilityJoinItemsResultsDBBusiness::getDBFVFormatList(1, 1, ['ability_code' => $abilityCodeArr]
            , false, [], [
                'sqlParams' => [
                    'orderBy' => ['retry_no' => 'desc']
                ]
            ]);
        $resultFormatDataList = Tool::arrUnderReset($resultDataList, 'ability_code,ability_id', 2, '_');

        $errsArr = [];// 错误数组
        // $saveArr = [];// 最终可以保存的数据
        foreach($saveData as $k => &$info) {
            $recordErr = [];
            $company_name = $info['company_name'] ?? '';// 单位名称
            // $test_year = $info['test_year'] ?? '';// 年
            $test_code = $info['test_code'] ?? '';// 检验检测机构代码
            $test_item = $info['test_item'] ?? '';// 检测项目
            $test_result = $info['test_result'] ?? '';// 验证结果
            $remarks = $info['remarks'] ?? '';// 备注

            if(!empty($remarks)){
                $remarks = replace_enter_char($remarks, 1);
                $info['remarks'] = $remarks;
            }

            // if(!is_numeric($test_year) || $test_year <= 0) array_push($recordErr, '年不能为空!');
            // if(empty($category_name) && empty($project_name) && empty($param_name)) array_push($recordErr, '类别、产品、项目不能都为空!');
            if(empty($company_name)) array_push($recordErr, '单位名称不能都为空!');
            if(empty($test_code)) array_push($recordErr, '检验检测机构代码不能都为空!');
            if(empty($test_item)) array_push($recordErr, '检测项目不能都为空!');
            if(empty($test_result)) array_push($recordErr, '验证结果不能都为空!');
            $resultIndex = array_search($test_result, $resultStatus);
            if($resultIndex === false) array_push($recordErr, '验证结果有效值[' . implode('、', $resultStatus) . ']!');

            // 根据企业名称，获得企业记录
//            $staffInfo = StaffDBBusiness::getDBFVFormatList(4, 1, ['admin_type' => 2, 'company_name' => $company_name, 'is_perfect' => 2, 'open_status' => 2, 'account_status' => 1], false);
//            if(empty($staffInfo)) array_push($recordErr, '企业信息不存在或信息非正常状态，请先注册或处理!');
//            $info['company_id'] = $tem_company_id = $staffInfo['id'] ?? 0;

            // 根据项目名称，获得项目记录
            $abilitysInfo = AbilitysDBBusiness::getDBFVFormatList(4, 1, ['ability_name' => $test_item], false, [], [
                'sqlParams' => [
                    'where' => [
                        ['join_begin_date', '>=' , $test_year . '-01-01 00:00:01'],
                        ['join_begin_date', '<=' , $test_year . '-12-31 23:59:59'],
                    ]
                ]
            ]);
            if(empty($abilitysInfo)) array_push($recordErr, $test_year . '年检测项目【' . $test_item . '】不存在!');
            $tem_ability_id = $abilitysInfo['id'] ?? 0;

            $temDataList = $resultFormatDataList[$test_code . '_' . $tem_ability_id] ?? [];
            if(empty($temDataList)) array_push($recordErr, '能力验证结果记录不存在!');
            $mustRetry = 1;// 1：可能是初测，也可能是补测； 2、肯定是初测 ； 4、肯定是补测
            if(strpos($test_result, '补测') !== false) { // 没有小数点
                $mustRetry = 4;
            }elseif($test_result == '满意'){
                $mustRetry = 2;
            }
            $dbLoopNo = 0;
            $resultId = 0;
            $isUnsetInfo = false;
            foreach($temDataList as $temInfo){
                $dbLoopNo++;
                if($dbLoopNo > 1) break;
                $resultId = $temInfo['id'];
                $dbResultStatus = $temInfo['result_status'];// 验证结果1待判定  2满意、4有问题、8不满意   16满意【补测满意】
                $dbRetryNo = $temInfo['retry_no'];// 测试序号 0正常测 1补测1 2 补测2 .....
                $dbIsSample = $temInfo['is_sample'];// 是否取样1待取样--未取 2已取样--已取
                $dbSubmitStatus = $temInfo['submit_status'];// 是否上传数据1待传 --未传  2 已传
                $dbJudgeStatus = $temInfo['judge_status'];// 是否评定1待评  2 已评

                if($dbJudgeStatus == 2 || $dbResultStatus > 1){// 已经判定
                    if($dbResultStatus == $resultIndex) {// 判定结果相同
                        unset($saveData[$k]);
                        $isUnsetInfo = true;
                        continue ;
                    }else{// 判断定结果不同
                        array_push($recordErr, '已有判定结果，但是与此操作结果不一致!');
                        continue;
                    }
                }else{// 待判断定
                    if($dbIsSample != 2){
                        array_push($recordErr, '记录未取样，不可进行判定操作!');
                        continue;
                    }
                    if($dbSubmitStatus != 2){// 未上传数据--只可以进行不满意操作
                        if($resultIndex != 8){// 非不满意时，才报错
                            array_push($recordErr, '记录未上传数据，不可进行判定操作!');
                            continue;
                        }
                    }

                    switch($mustRetry) {//  1：可能是初测，也可能是补测；
                        case 1:
                            break;
                        case 2:// 2、肯定是初测
                            if($dbRetryNo != 0) array_push($recordErr, '请核对数据当前是初测？');
                            break;
                        case 4:// 4、肯定是补测
                            if($dbRetryNo <= 0) array_push($recordErr, '请核对数据当前是补测？');
                            break;
                        default:
                            break;
                    }
                }

                break;// 只判断第一条
            }

            if(!empty($recordErr)){
                array_push($errsArr,'第' . ($k + 1) . '条记录'. $company_name .':<br/>' . implode('<br/>', $recordErr));
            }
            if(!$isUnsetInfo){
                $info['id'] = $resultId;
                $info['result_status'] = $resultIndex;
                $info['ability_id'] = $tem_ability_id;
            }
//            $queryParams = ['test_code' => $test_code, 'test_item' => $test_item];// 'test_year' => $test_year,
//            if($tem_company_id > 0) $queryParams['company_id'] = $tem_company_id;
//            $temInfo = static::getDBFVFormatList(4, 1, $queryParams, false);
//            $info['id'] = $temInfo['id'] ?? 0;
//            if(isset($info['company_name'])) unset($info['company_name']);
        }
        // 如果有错，则返回错误
        if(!empty($errsArr)) throws(implode('<br/>', $errsArr));

        CommonDB::doTransactionFun(function() use( &$saveData, &$returnIds, &$temNeedStaffIdOrHistoryId,
            &$operate_staff_id, &$company_id, &$modifAddOprate, &$operate_staff_id_history){
            // 对数据进行修改或新加
            // throws('对数据进行修改或新加');
            foreach($saveData as $k => &$info){
                // 加入操作人员信息
                if($temNeedStaffIdOrHistoryId) static::addOprate($info, $operate_staff_id,$operate_staff_id_history, 1);

                // $company_id = 0;
                static::judgeResultById([ 'result_status' => $info['result_status']], $company_id, $info['id'],  $operate_staff_id, $modifAddOprate);
                array_push($returnIds, $info['id']);
            }
        });
        return $returnIds;
    }
}
