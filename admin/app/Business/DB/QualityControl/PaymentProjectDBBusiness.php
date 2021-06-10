<?php
// 付款/收款项目
namespace App\Business\DB\QualityControl;

use App\Models\QualityControl\PaymentProject;
use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class PaymentProjectDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\PaymentProject';
    public static $table_name = 'payment_project';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = ['payment_project_id'];
    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::getHistoryId($mainDBObj, $mainId, PaymentProjectHistoryDBBusiness::$model_name
            , PaymentProjectHistoryDBBusiness::$table_name, $historyDBObj, ['payment_project_id' => $mainId], static::$ignoreFields);
    }

    /**
     * 对比主表和历史表是否相同，相同：不更新版本号，不同：版本号+1
     *
     * @param mixed $mId 主表对象主键值
     * @param int $forceIncVersion 如果需要主表版本号+1,是否更新主表 1 更新 ;0 不更新
     * @return array 不同字段的内容 数组 [ '字段名' => ['原表中的值','历史表中的值']]; 空数组：不用版本号+1;非空数组：版本号+1
     * @author zouyan(305463219@qq.com)
     */
    public static function compareHistory($id = 0, $forceIncVersion = 0, &$mainDBObj = null, &$historyDBObj = null){
        // 判断版本号是否要+1
        $historySearch = [
            //  'company_id' => $company_id,
            'payment_project_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, PaymentProjectHistoryDBBusiness::$model_name
            , PaymentProjectHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, static::$ignoreFields, $forceIncVersion);
    }

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
        //        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }
//
//        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
//            throws('手机不能为空！');
//        }

        if(isset($saveData['title']) && empty($saveData['title'])  ){
            throws('收费名称不能为空！');
        }

        if(isset($saveData['pay_valid_status'])){
            if($saveData['pay_valid_status'] == PaymentProject::PAY_VALID_STATUS_FIXED){
                $saveData['pay_status'] = PaymentProject::PAY_STATUS_WAIT;
            }else{
                $saveData['pay_status'] = PaymentProject::PAY_STATUS_DOING;
            }
        }


        // 是否有图片资源
        $hasResource = false;

        $resourceIds = [];
        if(Tool::getInfoUboundVal($saveData, 'resourceIds', $hasResource, $resourceIds, 1)){
            // $saveData['resource_id'] = $resourceIds[0] ?? 0;// 第一个图片资源的id
        }
        // $resource_ids = $saveData['resource_ids'] ?? '';// 图片资源id串(逗号分隔-未尾逗号结束)
        // if(isset($saveData['resource_ids']))  unset($saveData['resource_ids']);
        // if(isset($saveData['resource_id']))  unset($saveData['resource_id']);

        // 是否有付款/收款项目字段
        $hasFieldList = false;
        $fieldList = [];
        Tool::getInfoUboundVal($saveData, 'field_list', $hasFieldList, $fieldList, 1);

        // 修改时 需要强制更新数量
        $forceCompanyNum =  false;
        $force_company_num = '';
        $companyNumIds = [];// 需要更新的企业id数组
        if(Tool::getInfoUboundVal($saveData, 'force_company_num', $forceCompanyNum, $force_company_num, 1)){
            if(isset($saveData['company_id']) && is_numeric($saveData['company_id']) && $saveData['company_id'] > 0 ){
                array_push($companyNumIds, $saveData['company_id']);
            }
        }
        // 是否批量操作标识 true:批量操作； false:单个操作 ---因为如果批量操作，有些操作就不能每个操作都执行，也要批量操作---为了运行效率
        // 有此下标就代表批量操作
        $isBatchOperate = false;
        $isBatchOperateVal = '';
        Tool::getInfoUboundVal($saveData, 'isBatchOperate', $isBatchOperate, $isBatchOperateVal, 1);

        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history, &$modelObj, &$isModify
            , &$forceCompanyNum, &$force_company_num, &$companyNumIds, &$isBatchOperate, &$isBatchOperateVal, &$hasResource, &$resourceIds,
            &$hasFieldList, &$fieldList){


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
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData,$modelObj);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                if($forceCompanyNum){
                    $info_old = static::getInfo($id);
                    $tem_company_id = $info_old['company_id'];
                    if($tem_company_id > 0 && !in_array($tem_company_id, $companyNumIds)) array_push($companyNumIds, $tem_company_id);

                }
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            // 同步修改图片资源关系
            if($hasResource){
//                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
//                // 更新图片资源表
//                if(!empty($resourceIds)) {
//                    $resourceArr = ['column_type' => 8, 'column_id' => $id];
//                    ResourceDBBusiness::saveByIds($resourceArr, $resourceIds);
//                }
                // ResourceDBBusiness::bathResourceSync(static::thisObj(), 8, [['id' => $id, 'resourceIds' => $resourceIds, 'otherData' => []]], $operate_staff_id, $operate_staff_id_history);
                ResourceDBBusiness::resourceSync(static::thisObj(), 131072, $id, $resourceIds, [], $operate_staff_id, $operate_staff_id_history);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }

            // 如果是新加，则记录记录

            // 付款/收款项目字段
            if($hasFieldList) {
                $field_ids = PaymentProjectFieldsDBBusiness::updateByDataList(['payment_project_id' => $id]
                    , [
                        'payment_project_id' => $id,
                        // 'analyse_answer' => $analyseAnswer,
                        'company_id' => $saveData['company_id'] ?? 0,
//                         'operate_staff_id' => $operate_staff_id,
//                         'operate_staff_id_history' => $operate_staff_id_history,
                    ]
                    , Tool::arrAppendKeys($fieldList, [
                        // 'id' => $analyseId,
                        // 'subject_id' => $id,
                        // 'analyse_answer' => $analyseAnswer,
                        'company_id' => $saveData['company_id'] ?? 0,
                        'operate_staff_id' => $operate_staff_id,
                        'operate_staff_id_history' => $operate_staff_id_history,
                    ]), $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, [
                        'del' => [
                            'del_type' => 2,
                            'extend_params' => [// 删除的扩展参数 一维数组  del_type = 2时：用
                                'organize_id' => $saveData['company_id'] ?? 0
                            ],
                        ]
                    ]);
            }

            // 如果是新加，则记录注册记录
            if(!$isModify){
                // 自增试题分类的数量
                $type_no = $saveData['type_no'] ?? 0;
                if(is_numeric($type_no) && $type_no > 0){
                    $incQueryParams = [];
                    Tool::appendCondition($incQueryParams, 'type_no',  $type_no . '=' . $type_no, '&');
                    PaymentTypeDBBusiness::saveDecIncByQuery('amount', 1,  'inc', $incQueryParams, []);
                }

                // 如果是新加，所需要更新企业能力范围数量
                // 注意，如果是批量操作，不在这里处理，在批量的业务地方再处理此功能
                // if(!$isBatchOperate && is_numeric($resultDatas['company_id']) && $resultDatas['company_id'] > 0){
                //    StaffDBBusiness::updateInvoiceAddrNum($resultDatas['company_id']);
                // }
                if(!$isBatchOperate && isset($saveData['company_id']) && is_numeric($saveData['company_id']) && $saveData['company_id'] > 0){
                    StaffDBBusiness::updateExtendNum($saveData['company_id'], 'payment_project');
                }
            }else if($forceCompanyNum && !empty($companyNumIds)){// 修改时 需要强制更新数量
                StaffDBBusiness::updateExtendNum($companyNumIds, 'payment_project');
            }
        });
        // ************************************************
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }

    /**
     * 根据id删除
     *
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @param array $extendParams 其它参数--扩展用参数
     *  [
     *       'organize_id' => 3,操作的企业id 可以为0：不指定具体的企业
     *       'doOperate' => 1,执行的操作 0 不删除 1 删除源图片[默认]
     *  ]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){
        $organize_id = $extendParams['organize_id'] ?? 0;// 操作的企业id 可以为0：不指定具体的企业

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }
        $dataList = static::getDBFVFormatList(1, 1, ['id' => $id], false);
        if(empty($dataList)) throws('操作记录不存在！');

        foreach($dataList as $info){
            $t_company_id = $info['company_id'] ?? 0;// 企业 id
            if($organize_id > 0 && $organize_id != $t_company_id) throws('没有操作记录【' . $info['id'] . '】的权限');
//            if(static::judgeTypeNoUsed($info['type_no'], $t_company_id)){
//                throws('记录【' . $info['id'] . '】的分类编号【' . $info['type_no'] . '】已使用，不可进行删除操作！');
//            }
        }

        $organizeIds = Tool::getArrFields($dataList, 'company_id');
        $usedId = 0;
        if(PaymentRecordDBBusiness::judgeNoUsed($id, $organize_id, $usedId)){
            throws('记录【' . $usedId . '】已使用，不可进行删除操作！');
        }

        return CommonDB::doTransactionFun(function() use(&$dataList ,&$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$extendParams, &$organizeIds){

            $delQueryParams = Tool::getParamQuery(['payment_project_id' => $id], [], []);

            // 删除付款/收款项目字段
             PaymentProjectFieldsDBBusiness::del($delQueryParams);
            // 删除资源及文件
            ResourceDBBusiness::delResourceByIds(static::thisObj(), $id, 131072);

            // 删除记录
            static::deleteByIds($id);
            // 删除员工--还需要重新统计企业的员工数
            if(!empty($organizeIds)){
                foreach($organizeIds as $organizeId){
                    // 根据企业id更此记录数
                    StaffDBBusiness::updateExtendNum($organizeId, 'payment_project');
                }
            }

            // 自减试题分类的数量
            foreach($dataList as $info) {
                $type_no = $info['type_no'] ?? 0;
                if (is_numeric($type_no) && $type_no > 0) {
                    $decQueryParams = [];
                    Tool::appendCondition($decQueryParams, 'type_no', $type_no . '=' . $type_no, '&');
                    PaymentTypeDBBusiness::saveDecIncByQuery('amount', 1, 'dec', $decQueryParams, []);
                }
            }
            return $id;
        });
    }

    /**
     * 对待收费的进行开始收费处理--每一分钟跑一次
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function autoPayStart()
    {
        $dateTime =  date('Y-m-d H:i:s');
        // 读取所有未开始的
//        $queryParams = [
//            'where' => [
//                ['status', 1],
//                ['join_begin_date', '<=', $dateTime],
//            ],
//             'select' => ['id' ]
//        ];
        $queryParams = Tool::getParamQuery([
            'pay_valid_status' => PaymentProject::PAY_VALID_STATUS_FIXED,
            'open_status' => PaymentProject::OPEN_STATUS_OPEN,
            'pay_status' => PaymentProject::PAY_STATUS_WAIT
        ], [
            'sqlParams' =>[
                'select' =>['id' ]
                ,
                'where' => [
                    ['pay_begin_time', '<=', $dateTime]
                ],
//                'whereIn' => [
//                    'pay_status' => [2,4,8],
//                ],
            ]
        ], []);
        $dataList = static::getAllList($queryParams, [])->toArray();

        if(!empty($dataList)){
            $ids = array_values(array_unique(array_column($dataList,'id')));
            $saveDate = [
                'pay_status' => PaymentProject::PAY_STATUS_DOING
//                'finish_date' => date('Y-m-d'),
//                'finish_time' => date('Y-m-d H:i:s'),
            ];
//            $saveQueryParams = [
//                'where' => [
//                    ['status', 1],
//                    // ['status_business', '!=', 1],
//                ],
//            ];
            $saveQueryParams = Tool::getParamQuery([
                'pay_valid_status' => PaymentProject::PAY_VALID_STATUS_FIXED,
                'open_status' => PaymentProject::OPEN_STATUS_OPEN,
                'pay_status' => PaymentProject::PAY_STATUS_WAIT
            ], [], []);
            Tool::appendParamQuery($saveQueryParams, $ids, 'id', [0, '0', ''], ',', false);
            static::save($saveDate, $saveQueryParams);
        }
    }

    /**
     * 对开始收费的进行结束收费处理--每一分钟跑一次
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function autoPayEnd()
    {
        $dateTime =  date('Y-m-d H:i:s');
        // 读取所有未开始的
//        $queryParams = [
//            'where' => [
//                ['status', 1],
//                ['join_begin_date', '<=', $dateTime],
//            ],
//             'select' => ['id' ]
//        ];
        $queryParams = Tool::getParamQuery([
            'pay_valid_status' => PaymentProject::PAY_VALID_STATUS_FIXED,
            'open_status' => PaymentProject::OPEN_STATUS_OPEN,
            'pay_status' => PaymentProject::PAY_STATUS_DOING
        ], [
            'sqlParams' =>[
                'select' =>['id' ]
                ,
                'where' => [
                    ['pay_end_time', '<=', $dateTime]
                ],
//                'whereIn' => [
//                    'pay_status' => [2,4,8],
//                ],
            ]
        ], []);
        $dataList = static::getAllList($queryParams, [])->toArray();

        if(!empty($dataList)){
            $ids = array_values(array_unique(array_column($dataList,'id')));
            $saveDate = [
                'pay_status' => PaymentProject::PAY_STATUS_DOED
//                'finish_date' => date('Y-m-d'),
//                'finish_time' => date('Y-m-d H:i:s'),
            ];
//            $saveQueryParams = [
//                'where' => [
//                    ['status', 1],
//                    // ['status_business', '!=', 1],
//                ],
//            ];
            $saveQueryParams = Tool::getParamQuery([
                'pay_valid_status' => PaymentProject::PAY_VALID_STATUS_FIXED,
                'open_status' => PaymentProject::OPEN_STATUS_OPEN,
                'pay_status' => PaymentProject::PAY_STATUS_DOING
            ], [], []);
            Tool::appendParamQuery($saveQueryParams, $ids, 'id', [0, '0', ''], ',', false);
            static::save($saveDate, $saveQueryParams);
        }
    }
}
