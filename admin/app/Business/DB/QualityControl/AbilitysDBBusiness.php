<?php
// 能力验证
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class AbilitysDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\Abilitys';
    public static $table_name = 'abilitys';// 表名称
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
            // 同一年的名称不能重复
            if(isset($saveData['ability_name']) &&  isset($saveData['ability_name'])){
                $temAbilityName = $saveData['ability_name'];
                $temJoinBeginDate = $saveData['join_begin_date'];
                $temYear = judgeDate($temJoinBeginDate,'Y');
                if( $temYear === false ) throws('报名开始日期不是有效的日期格式！');
                if(static::judgeFieldExist($company_id, $id ,"ability_name", $saveData['ability_name'], [
                    ['join_begin_date', '>=' , $temYear . '-01-01 00:00:01'],
                    ['join_begin_date', '<=' , $temYear . '-12-31 23:59:59'],
                ],1)) throws($temYear . '年检测项目【' . $temAbilityName . '】名称已存在!');
            }

            // 方法标准
            $project_standards = [];
            $has_project_standard = false;// 是否有方法修改 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'project_standards', $has_project_standard, $project_standards, 1);

            // 验证数据项
            $submit_items = [];
            $has_submit_item = false;// 是否有验证数据项修改 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'submit_items', $has_submit_item, $submit_items, 1);

            // 有此下标，会去判断状态应该是不是可以到完成状态
            $judge_complete = [];
            $has_judge_complete = false;// 是否有方法修改 false:没有 ； true:有
            Tool::getInfoUboundVal($saveData, 'judge_complete', $has_judge_complete, $judge_complete, 1);


            $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
            // 保存前的处理
            static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
            $modelObj = null;
            //************************************************************************************
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

            // 如果有方法标准 修改
            if($has_project_standard){
                $standardNum = count($project_standards);
                $saveStandardData = [];
                foreach($project_standards as $k => $standard_info) {
                    $standard_id = $standard_info['id'];
                    array_push($saveStandardData, [
                        'id' => $standard_id,
                        'ability_id' => $id,
                        'name' => $standard_info['name'],
                        'sort_num' => $standardNum - $k,
                    ]);
                }
                $project_standard_ids = ProjectStandardsDBBusiness::updateByDataList(['ability_id' => $id], ['ability_id' => $id]
                    , $saveStandardData, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }
//            if($has_project_standard){
//                $standardListArr = [];
//                $standardIds = [];
//                if($isModify){// 是修改
//                    // 获得所有的方法标准
//                    $queryParams = [
//                        'where' => [
////                ['company_id', $organize_id],
//                            ['ability_id', $id],
////                ['teacher_status',1],
//                        ],
//                        // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//                    ];
//                    $standardDataListObj = ProjectStandardsDBBusiness::getAllList($queryParams, []);
//                    $standardListArr = $standardDataListObj->toArray();
//                    if(!empty($standardListArr)) $standardIds = array_values(array_unique(array_column($standardListArr,'id')));
//                }
//
//                $standardNum = count($project_standards);
//                foreach($project_standards as $k => $standard_info){
//                    $standard_id = $standard_info['id'];
//                    $saveStandardData = [
//                        'ability_id' => $id,
//                        'name' => $standard_info['name'],
//                        'sort_num' => $standardNum - $k,
//                    ];
//                    if($operate_staff_id > 0) $saveStandardData['operate_staff_id'] = $operate_staff_id;
//                    if($operate_staff_id_history > 0) $saveStandardData['operate_staff_id_history'] = $operate_staff_id_history;
//                    // 不存在的id
//                    if( $standard_id > 0 && !empty($standardIds) && !in_array($standard_id, $standardIds))  $standard_id = 0;
////                    if($standard_id <= 0 ){
//                        ProjectStandardsDBBusiness::replaceById($saveStandardData, $company_id, $standard_id, $operate_staff_id, $modifAddOprate);
////                    }else{
//                        // 可能会是修改
////                        $updateFields = $saveStandardData;
////                        $searchConditon = [
////                            'ability_id' => $id,
////                            'id' => $standard_id,
////                        ];
////                        $standardObj = null;
////                        ProjectStandardsDBBusiness::firstOrCreate($standardObj, $searchConditon, $updateFields );
////                    }
//                    // 移除当前的id
//                    $recordUncode = array_search($standard_id, $standardIds);
//                    if($recordUncode !== false) unset($standardIds[$recordUncode]);// 存在，则移除
//
//                }
//                if($isModify && !empty($standardIds)) {// 是修改 且不为空
//                    // 删除记录
//                    ProjectStandardsDBBusiness::deleteByIds($standardIds);
//                }
//
//
//            }


            // 如果有验证数据项 修改
            if($has_submit_item){
                // 处理数据
                $submitNum = count($submit_items);
                $saveStandardData = [];
                foreach($submit_items as $k => $submit_info) {
                    $standard_id = $submit_info['id'];
                    array_push($saveStandardData, [
                        'id' => $standard_id,
                        'ability_id' => $id,
                        'name' => $submit_info['name'],
                        'sort_num' => $submitNum - $k,
                    ]);
                }
                $submit_item_ids = ProjectSubmitItemsDBBusiness::updateByDataList(['ability_id' => $id], ['ability_id' => $id]
                    , $saveStandardData, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
            }
//            if($has_submit_item){
//                $submitItemListArr = [];
//                $submitItemIds = [];
//                if($isModify){// 是修改
//                    // 获得所有的方法标准
//                    $queryParams = [
//                        'where' => [
////                ['company_id', $organize_id],
//                            ['ability_id', $id],
////                ['teacher_status',1],
//                        ],
//                        // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//                    ];
//                    $standardDataListObj = ProjectSubmitItemsDBBusiness::getAllList($queryParams, []);
//                    $submitItemListArr = $standardDataListObj->toArray();
//                    if(!empty($submitItemListArr)) $submitItemIds = array_values(array_unique(array_column($submitItemListArr,'id')));
//                }
//
//                $submitNum = count($submit_items);
//                foreach($submit_items as $k => $submit_info){
//                    $standard_id = $submit_info['id'];
//                    $saveStandardData = [
//                        'ability_id' => $id,
//                        'name' => $submit_info['name'],
//                        'sort_num' => $submitNum - $k,
//                    ];
//                    if($operate_staff_id > 0) $saveStandardData['operate_staff_id'] = $operate_staff_id;
//                    if($operate_staff_id_history > 0) $saveStandardData['operate_staff_id_history'] = $operate_staff_id_history;
//                    // 不存在的id
//                    if( $standard_id > 0 && !empty($submitItemIds) && !in_array($standard_id, $submitItemIds))  $standard_id = 0;
////                    if($standard_id <= 0 ){
//                    ProjectSubmitItemsDBBusiness::replaceById($saveStandardData, $company_id, $standard_id, $operate_staff_id, $modifAddOprate);
////                    }else{
//                    // 可能会是修改
////                        $updateFields = $saveStandardData;
////                        $searchConditon = [
////                            'ability_id' => $id,
////                            'id' => $standard_id,
////                        ];
////                        $standardObj = null;
////                        ProjectSubmitItemsDBBusiness::firstOrCreate($standardObj, $searchConditon, $updateFields );
////                    }
//                    // 移除当前的id
//                    $recordUncode = array_search($standard_id, $submitItemIds);
//                    if($recordUncode !== false) unset($submitItemIds[$recordUncode]);// 存在，则移除
//
//                }
//                if($isModify && !empty($submitItemIds)) {// 是修改 且不为空
//                    // 删除记录
//                    ProjectSubmitItemsDBBusiness::deleteByIds($submitItemIds);
//                }
//            }
            // 有此下标，会去判断状态应该是不是可以到完成状态
            // 两个条件，1当前记录是已公布状态 2下面的证书都领取完了
            if($has_judge_complete){
                static::judgeAndDoComplete($id);
            }

            // 保存成功后的处理
            static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
            return $id;
        });
    }

    /**
     * 根据id判断是否应该已结束当前项目
     *
     * @param int $id id
     * @return  null
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeAndDoComplete($id){

        // 判断是否已经公布
//                $tem_is_publish = $saveData['is_publish'] ?? 0;
//                if($tem_is_publish <= 0 ){
        $tem_info = static::getInfo($id);
        $tem_is_publish = $tem_info['is_publish'] ?? 0;
        $tem_status = $tem_info['status'] ?? 0;
//                }
        if($tem_is_publish == 4 && $tem_status == 4){
            // 2下面的证书都领取完了
            // 获得正在处理的一条记录
            $queryParams = Tool::getParamQuery(['ability_id' => $id], ['sqlParams' =>['whereIn' => ['status' => [1,2,4,8]]]], []);
            $resultDoingInfo = AbilityJoinItemsResultsDBBusiness::getInfoByQuery(1, $queryParams, []);
            if(empty($resultDoingInfo)){
                static::saveById([
                    'status' => 8,
                    'complete_time' => date('Y-m-d H:i:s'),
                ], $id);
            }
        }
    }

    /**
     * 根据id删除
     *
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @param array $extendParams 其它参数--扩展用参数
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }
        $info = static::getInfo($id);
        if(empty($info)) throws('记录不存在');
        if($info['status'] != 1) throws('当前记录非【待开始】状态，不可删除！');
//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        CommonDB::doTransactionFun(function() use(&$id){

            // 删除主记录
            static::deleteByIds($id);
            // 删除 项目标准
//            $delQueryParams = [
//                'where' => [
//                    ['ability_id', $id],
//                ],
//            ];
            // Tool::appendParamQuery($delQueryParams, $id, 'id', [0, '0', ''], ',', false);
            $delQueryParams = Tool::getParamQuery(['ability_id' => $id], [], []);
            ProjectStandardsDBBusiness::del($delQueryParams);
            // 删除  验证数据项
            ProjectSubmitItemsDBBusiness::del($delQueryParams);
        });
        return $id;
    }

    /**
     * 未开始的，时间一到进入到开始报名--每一分钟跑一次
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function autoBeginJoin()
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
        $queryParams = Tool::getParamQuery(['status' => 1], ['sqlParams' =>['select' =>['id' ], 'where' => [['join_begin_date', '<=', $dateTime]]]], []);
        $dataList = static::getAllList($queryParams, [])->toArray();

        if(!empty($dataList)){
            $ids = array_values(array_unique(array_column($dataList,'id')));
            $saveDate = [
                'status' => 2,
            ];
//            $saveQueryParams = [
//                'where' => [
//                    ['status', 1],
//                    // ['status_business', '!=', 1],
//                ],
//            ];
            $saveQueryParams = Tool::getParamQuery(['status' => 1], [], []);
            Tool::appendParamQuery($saveQueryParams, $ids, 'id', [0, '0', ''], ',', false);
            static::save($saveDate, $saveQueryParams);
        }
    }

    /**
     * 开始报名的，时间一到结束，进入到进行中--每一分钟跑一次
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function autoBeginDoing()
    {
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
        $queryParams = Tool::getParamQuery(['status' => [1,2]], ['sqlParams' =>['select' =>['id' ], 'where' => [['join_end_date', '<=', $dateTime]]]], []);
        $dataList = static::getAllList($queryParams, [])->toArray();

        if(!empty($dataList)){
            $ids = array_values(array_unique(array_column($dataList,'id')));
            $saveDate = [
                'status' => 4,
            ];
//            $saveQueryParams = [
//                'where' => [
//                    // ['status', 2],
//                    // ['status_business', '!=', 1],
//                ],
//                'whereIn' => [ 'status' => [1,2]],
//            ];
//            Tool::appendParamQuery($saveQueryParams, $ids, 'id', [0, '0', ''], ',', false);
            $saveQueryParams = Tool::getParamQuery(['status' => [1,2], 'id' => $ids], [], []);
            static::save($saveDate, $saveQueryParams);
        }
    }

    /**
     * 指定时间公布的，时间一到结束，进行公布--每一分钟跑一次
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function autoPublishDoing()
    {
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
        $queryParams = Tool::getParamQuery(['status' => 4, 'publish_type' => 4, 'is_publish' => 2], ['sqlParams' =>['select' =>['id' ], 'where' => [['publish_time', '<=', $dateTime]]]], []);
        $dataList = static::getAllList($queryParams, [])->toArray();

        if(!empty($dataList)){
            $ids = array_values(array_unique(array_column($dataList,'id')));
            $saveDate = [
                'is_publish' => 4,
                 // 'judge_complete' => 1,// 有此下标，会去判断状态应该是不是可以到完成状态
            ];
//            $saveQueryParams = [
//                'where' => [
//                    // ['status', 2],
//                    // ['status_business', '!=', 1],
//                ],
//                'whereIn' => [ 'status' => [1,2]],
//            ];
//            Tool::appendParamQuery($saveQueryParams, $ids, 'id', [0, '0', ''], ',', false);
            $saveQueryParams = Tool::getParamQuery(['status' => 4, 'id' => $ids], [], []);
            static::save($saveDate, $saveQueryParams);
            // 判断是否应该进入完成状态
            foreach($ids as $tem_id){
                static::judgeAndDoComplete($tem_id);
            }
        }
    }

    /**
     * 根据id--指定数量 自增或自减
     * @param int  $ability_id  项目id
     * @param string $field_name 字段名 join_num 报名企业(数量)； first_submit_num 初测提交数据数；repair_submit_num 补测提交数据数；
     *                             first_success_num 初测满意数 ；repair_success_num  补测满意数；first_fail_num 初测不满意数；repair_fail_num 补测不满意数
     * @param string incDecVal 增减值
     * @param string incDecType 增减类型 inc 增[默认] ;dec 减
     * @return  null
     * @author zouyan(305463219@qq.com)
     */
    public static function fieldValIncDec($ability_id, $field_name, $incDecVal = 1, $incDecType = 'inc'){
        $queryParams = Tool::getParamQuery(['id' => $ability_id], [], []);
        static::saveDecIncByQuery($field_name, $incDecVal,  $incDecType, $queryParams, []);
    }

    /**
     * 根据id--判断是否已经判定
     * @param int  $ability_id  项目id
     * @param int $retry_no 测试序号 0正常测 1补测1 2 补测2 .....
     * @return  boolean 是否已经完成  true:都判定了， false :有未判定的
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeIsJudged($ability_id, $retry_no = 0){
        $count = AbilityJoinItemsResultsDBBusiness::getCountNum([1,2,4], $retry_no, $ability_id, 0, 0);
        return ($count > 0) ? false :true;
    }

    /**
     * 根据id--判断是否已经完成
     * @param int  $ability_id  项目id
     * @param int $retry_no 测试序号 0正常测 1补测1 2 补测2 .....
     * @return  boolean 是否已经完成  true:都完成了， false :有未完成的
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeIsFinished($ability_id, $retry_no = 0){
        $count = AbilityJoinItemsResultsDBBusiness::getCountNum([1,2,4,8], $retry_no, $ability_id, 0, 0);
        return ($count > 0) ? false :true;
    }
}
