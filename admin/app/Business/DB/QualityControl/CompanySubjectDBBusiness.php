<?php
// 试题
namespace App\Business\DB\QualityControl;

use App\Models\QualityControl\CompanyPaperSubject;
use App\Models\QualityControl\CompanySubject;
use App\Models\QualityControl\CompanySubjectAnswer;
use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class CompanySubjectDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CompanySubject';
    public static $table_name = 'company_subject';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = ['subject_id'];
    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::getHistoryId($mainDBObj, $mainId, CompanySubjectHistoryDBBusiness::$model_name
            , CompanySubjectHistoryDBBusiness::$table_name, $historyDBObj, ['subject_id' => $mainId], static::$ignoreFields);
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
            'subject_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, CompanySubjectHistoryDBBusiness::$model_name
            , CompanySubjectHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, static::$ignoreFields, $forceIncVersion);
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
            throws('题目不能为空！');
        }

        // 是否有试题答案选项
        $hasAnswerList = false;
        $answerList = [];
        Tool::getInfoUboundVal($saveData, 'answer_list', $hasAnswerList, $answerList, 1);

        // 是否有试题分析
        $hasAnalyseAnswer = false;
        $analyseAnswer = '';
        Tool::getInfoUboundVal($saveData, 'analyse_answer', $hasAnalyseAnswer, $analyseAnswer, 1);

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
            , &$forceCompanyNum, &$force_company_num, &$companyNumIds, &$isBatchOperate, &$isBatchOperateVal, &$hasAnalyseAnswer, &$analyseAnswer,
            &$hasAnswerList, &$answerList){


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
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }

            // 如果是新加，则记录记录
            if($hasAnalyseAnswer) {
//                $analyseObj = null;
//                CompanySubjectAnalyseDBBusiness::updateOrCreate($analyseObj, ['subject_id' => $id], [
//                    'analyse_answer' => $analyseAnswer,
//                    'company_id' => $saveData['company_id'] ?? 0,
//                    'operate_staff_id' => $operate_staff_id,
//                    'operate_staff_id_history' => $operate_staff_id_history,
//                ] );
                $temInfo = [];
                if($isModify) $temInfo = CompanySubjectAnalyseDBBusiness::getDBFVFormatList(4, 1, ['subject_id' => $id], false);
                $analyseId = $temInfo['id'] ?? 0;
                $subject_analyse_ids = CompanySubjectAnalyseDBBusiness::updateByDataList(['subject_id' => $id]
                    , [
                        'subject_id' => $id,
                        // 'analyse_answer' => $analyseAnswer,
                        'company_id' => $saveData['company_id'] ?? 0,
                        // 'operate_staff_id' => $operate_staff_id,
                        // 'operate_staff_id_history' => $operate_staff_id_history,
                    ]
                    , [
                        'id' => $analyseId,
                       // 'subject_id' => $id,
                        'analyse_answer' => $analyseAnswer,
                        'company_id' => $saveData['company_id'] ?? 0,
                        'operate_staff_id' => $operate_staff_id,
                        'operate_staff_id_history' => $operate_staff_id_history,
                    ], $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, [
                        'del' => [
                            'del_type' => 2,
                            'extend_params' => [// 删除的扩展参数 一维数组  del_type = 2时：用
                                'organize_id' => $saveData['company_id'] ?? 0
                            ],
                        ]
                    ]);
            }

            // 试题答案选项
            if($hasAnswerList) {
                $subject_ids = CompanySubjectAnswerDBBusiness::updateByDataList(['subject_id' => $id]
                    , [
                        'subject_id' => $id,
                        // 'analyse_answer' => $analyseAnswer,
                        'company_id' => $saveData['company_id'] ?? 0,
//                         'operate_staff_id' => $operate_staff_id,
//                         'operate_staff_id_history' => $operate_staff_id_history,
                    ]
                    , Tool::arrAppendKeys($answerList, [
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
                    CompanySubjectTypeDBBusiness::saveDecIncByQuery('amount', 1,  'inc', $incQueryParams, []);
                }

                // 如果是新加，所需要更新企业能力范围数量
                // 注意，如果是批量操作，不在这里处理，在批量的业务地方再处理此功能
                // if(!$isBatchOperate && is_numeric($resultDatas['company_id']) && $resultDatas['company_id'] > 0){
                //    StaffDBBusiness::updateInvoiceAddrNum($resultDatas['company_id']);
                // }
                if(!$isBatchOperate && isset($saveData['company_id']) && is_numeric($saveData['company_id']) && $saveData['company_id'] > 0){
                    StaffDBBusiness::updateExtendNum($saveData['company_id'], 'subject');
                }
            }else if($forceCompanyNum && !empty($companyNumIds)){// 修改时 需要强制更新数量
                StaffDBBusiness::updateExtendNum($companyNumIds, 'subject');
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
        if(CompanyPaperSubjectDBBusiness::judgeNoUsed($id, $organize_id, $usedId)){
            throws('记录【' . $usedId . '】已使用，不可进行删除操作！');
        }

        return CommonDB::doTransactionFun(function() use(&$dataList ,&$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$extendParams, &$organizeIds){

            $delQueryParams = Tool::getParamQuery(['subject_id' => $id], [], []);
            // 删除试题分析
            CompanySubjectAnalyseDBBusiness::del($delQueryParams);

            // 删除试题答案
            CompanySubjectAnswerDBBusiness::del($delQueryParams);

            // 删除记录
            static::deleteByIds($id);
            // 删除员工--还需要重新统计企业的员工数
            if(!empty($organizeIds)){
                foreach($organizeIds as $organizeId){
                    // 根据企业id更此记录数
                    StaffDBBusiness::updateExtendNum($organizeId, 'subject');
                }
            }

            // 自减试题分类的数量
            foreach($dataList as $info) {
                $type_no = $info['type_no'] ?? 0;
                if (is_numeric($type_no) && $type_no > 0) {
                    $decQueryParams = [];
                    Tool::appendCondition($decQueryParams, 'type_no', $type_no . '=' . $type_no, '&');
                    CompanySubjectTypeDBBusiness::saveDecIncByQuery('amount', 1, 'dec', $decQueryParams, []);
                }
            }
            return $id;
        });
    }

    /**
     * 判断题目类型为 填空题[确切答案] 或  填空题[人工批阅] 是否有问题-- 引用
     *
     * @param string  $title 题目
     * @param int  $answerShopType 答案位置显示的类型   如果不显示答案，下划线用__[不要用css下划线],可显示也可以复制到word
     *                                                  0 不进行操作
     *                                                  1 显示性的【小括号或下划线】--下划线可复制到word ；用下划线__ -- 比2优先； 1、2 比 4 优先
     *                                                  2 显示性的【小括号或下划线】--下划线不可复制到word ；用 css下划线
     *                                                  4输入框式的[小括号或下划线] --在线答案用；下划线都用css控制
     *
     *                                                 8 前端自己相办法处理【都转为16填空题【人工打分】 的方式 ，让前端自己处理】
     *
     *                                                 16 显示答案--如用于显示完整的题及答案--主要用于 填空题【确切答案】--
     * // 题目类型1单选；2多选；4判断；8问答【人工打分】；16填空题【人工打分】；32填空题【确切答案】格式：1+1=[[{2}]]；1+1>=[[{0<|=|>1<|=|>2}]]
     * @param int $subjectType
     * @param boolean  $hasAnswerArr 如果有答案，是否返回答案 true:返回带答案数组【有答案】；false:返回不带答案数组【空数组】
     * @return  array 二维数组
     * [
     *    [
     *      'show_type' => $inputShowType,// 填空题填写位置类型 1下划线;2小括号;4小括号 + 下划线
     *      'val_arr' => [],// 答案数组【有答案】
     *      'val_len' => $val,// 占位指定的答案长度 ｛填空题[人工批阅]｝ 或 最大的答案长度 ｛填空题[确切答案]｝
     *    ]
     * ]
     * @author zouyan(305463219@qq.com)
     */
    public static function formatQuoteTitle(&$title, $answerShopType = 1, $subjectType = 32, $hasAnswerArr = false){
        $reArr = [];
        // 下划线格式  墙面装饰分为________和________，
        $temArr = static::formatTitleSingleSplit($title, $answerShopType, $subjectType, CompanySubject::SUBJECT_TYPE_BIG_SPLIT_BEGIN, CompanySubject::SUBJECT_TYPE_BIG_SPLIT_END, $hasAnswerArr);
        if(!empty($temArr)) $reArr = array_merge($reArr, $temArr);
        // 小括号格式   墙面装饰分为(          )和(          )
        $temArr = static::formatTitleSingleSplit($title, $answerShopType, $subjectType, CompanySubject::SUBJECT_TYPE_BRACKETS_SPLIT_BEGIN, CompanySubject::SUBJECT_TYPE_BRACKETS_SPLIT_END, $hasAnswerArr);
        if(!empty($temArr)) $reArr = array_merge($reArr, $temArr);
        // 小括号 + 下划线 格式    墙面装饰分为(________)和(________)
        $temArr = static::formatTitleSingleSplit($title, $answerShopType, $subjectType, CompanySubject::SUBJECT_TYPE_UNDERLINE_BRACKETS_SPLIT_BEGIN, CompanySubject::SUBJECT_TYPE_UNDERLINE_BRACKETS_SPLIT_END, $hasAnswerArr);
        if(!empty($temArr)) $reArr = array_merge($reArr, $temArr);

        if(empty($reArr)) throws('题目中必须设置填空信息才是一个完整的填空题！');
        return $reArr;

    }

    // 判断题目类型为 填空题[确切答案] 或  填空题[人工批阅] 是否有问题
    public static function formatTitle($title, $answerShopType = 1, $subjectType = 32, $hasAnswerArr = false){
        return static::formatQuoteTitle($title, $answerShopType, $subjectType, $hasAnswerArr);
    }

    /**
     * 对题目 为 填空题[确切答案] 或  填空题[人工批阅] 进行 单个占位类型进行替换
     *
     * @param string  $title 题目
     * @param int  $answerShopType 答案位置显示的类型   如果不显示答案，下划线用__[不要用css下划线],可显示也可以复制到word
     *                                                  0 不进行操作
     *                                                  1 显示性的【小括号或下划线】--下划线可复制到word ；用下划线__ -- 比2优先； 1、2 比 4 优先
     *                                                  2 显示性的【小括号或下划线】--下划线不可复制到word ；用 css下划线
     *                                                  4输入框式的[小括号或下划线] --在线答案用；下划线都用css控制
     *
     *                                                 8 前端自己相办法处理【都转为16填空题【人工打分】 的方式 ，让前端自己处理】
     *
     *                                                 16 显示答案--如用于显示完整的题及答案--主要用于 填空题【确切答案】--
     * // 题目类型1单选；2多选；4判断；8问答【人工打分】；16填空题【人工打分】；32填空题【确切答案】格式：1+1=[[{2}]]；1+1>=[[{0<|=|>1<|=|>2}]]
     * @param int $subjectType
     * @param string  $beginSplit 占位的前分隔符
     * @param string  $endSplit 占位的后分隔符
     * @param boolean  $hasAnswerArr 如果有答案，是否返回答案 true:返回带答案数组【有答案】；false:返回不带答案数组【空数组】
     * @return  array 二维数组
     * [
     *    [
     *      'show_type' => $inputShowType,// 填空题填写位置类型 1下划线;2小括号;4小括号 + 下划线
     *      'val_arr' => [],// 答案数组【有答案】
     *      'val_len' => $val,// 占位指定的答案长度 ｛填空题[人工批阅]｝ 或 最大的答案长度 ｛填空题[确切答案]｝
     *    ]
     * ]
     * @author zouyan(305463219@qq.com)
     */
    public static function formatTitleSingleSplit(&$title, $answerShopType = 1, $subjectType = 32, $beginSplit = '', $endSplit = '', $hasAnswerArr = false){
         $reArr = [];
         $title = CompanySubject::replace_special_char($title, 2);
         $valArr = Tool::getLabelArr($title, $beginSplit, $endSplit);
         if(empty($valArr)) return $reArr;
         $inputShowType = static::getInputShowType($beginSplit);
         foreach($valArr as $val){
             // 16填空题【人工打分】
             if($subjectType == CompanySubject::SUBJECT_TYPE_COMPLETAION_HAND){
                 if(!is_numeric($val) || $val <= 0) throws('题目中格式【' . $beginSplit . $val . $endSplit . '】必须为【' . $beginSplit . '【预计答案字符个数】' . $endSplit . '】！');
                 array_push($reArr, [
                     'show_type' => $inputShowType,// 填空题填写位置类型 1下划线;2小括号;4小括号 + 下划线
                     'val_arr' => [],// 答案数组【有答案】
                     'val_len' => $val,// 占位指定的答案长度 ｛填空题[人工批阅]｝ 或 最大的答案长度 ｛填空题[确切答案]｝
                 ]);
                 // 替换内容
                 if($answerShopType > 0) static::replaceInputStr($title, $subjectType, $answerShopType, '', $val , $beginSplit, $endSplit);
             }else if($subjectType == CompanySubject::SUBJECT_TYPE_COMPLETION){// 32填空题【确切答案】
                 if(strlen($val) <= 0) throws('题目中格式【' . $beginSplit . $val . $endSplit . '】必须为【' . $beginSplit . '【确定的答案】' . $endSplit . '】！');
                 $valInputArr = explode(CompanySubject::SUBJECT_TYPE_BIG_SPLIT_MID, $val);
                 // 去掉数组中的空值
                 $valMaxLen = 0;
                 foreach($valInputArr as $t_k => $t_v){
                     if(strlen($t_v) <= 0){
                         unset($valInputArr[$t_k]);
                         continue;
                     }
                     if($valMaxLen < Tool::getStrNum($t_v, 1,"utf8"))  $valMaxLen = Tool::getStrNum($t_v, 1,"utf8");
                 }
                 if(!empty($valInputArr)){
                     array_push($reArr, [
                         'show_type' => $inputShowType,
                         'val_arr' => $hasAnswerArr ? array_values($valInputArr) : [],
                         'val_len' => $valMaxLen,
                     ]);
                 }

                 // 替换内容
                 if($answerShopType > 0) static::replaceInputStr($title, $subjectType, $answerShopType, $val, $valMaxLen , $beginSplit, $endSplit);
             }
         }
         return $reArr;
    }

    /**
     * 对 填空题[确切答案] 或  填空题[人工批阅] 占位符，转为对应的显示符 -- 适合于最终打印
     *
     * @param string  $title 题目
     * // 题目类型1单选；2多选；4判断；8问答【人工打分】；16填空题【人工打分】；32填空题【确切答案】格式：1+1=[[{2}]]；1+1>=[[{0<|=|>1<|=|>2}]]
     * @param int $subjectType
     * @param int  $answerShopType 答案位置显示的类型   如果不显示答案，下划线用__[不要用css下划线],可显示也可以复制到word
     *                                                  0 不进行操作
     *                                                  1 显示性的【小括号或下划线】--下划线可复制到word ；用下划线__ -- 比2优先； 1、2 比 4 优先
     *                                                  2 显示性的【小括号或下划线】--下划线不可复制到word ；用 css下划线
     *                                                  4输入框式的[小括号或下划线] --在线答案用；下划线都用css控制
     *
     *                                                 8 前端自己相办法处理【都转为16填空题【人工打分】 的方式 ，让前端自己处理】
     *
     *                                                 16 显示答案--如用于显示完整的题及答案--主要用于 填空题【确切答案】--
     * @param string $val 填空填时，占位符的内容  填空题[人工批阅]时：为空 或 指定的其它内容；  填空题[确切答案]时：为占位的确切答案 ， ---有多个值时，可以直接传入 或 '<|=|>' 分隔 =》 会自动转为 ’ 或‘
     * @param int $strLen  占位指定的答案长度 ｛填空题[人工批阅]｝ 或 最大的答案长度 ｛填空题[确切答案]｝
     * @param string  $beginSplit 占位的前分隔符
     * @param string  $endSplit 占位的后分隔符
     * @param array  $extendParams 扩展参数
     *   $extendParams = [
     *      'input_name_pre' => 'aaa', // $answerShopType = 4时，输入框 的名称前部分 -- 中间会自动加上 _{填写位置类型 1、2、4}
     *      'input_name_back' => '[]', // $answerShopType = 4时，输入框 的名称后部分 一般为 []
     *      'subject_css' => '', // 试题 css的名称
     *  ];
     * @return  string 转换后的字符
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceInputStr(&$title, $subjectType = 32, $answerShopType = 1, $val = '', $strLen = 1 , $beginSplit = '', $endSplit = '', $extendParams = []){
        $title = CompanySubject::replace_special_char($title, 2);
        if($answerShopType <= 0) return $title;
        // 一个字的宽度 15px
        $inputWidth = $strLen * CompanySubject::ONE_FONT_WIDTH;
        // $replaceKV = [];
        $inputStr = '';

        if( ($answerShopType & 8) == 8){// 8 前端自己相办法处理【都转为16填空题【人工打分】 的方式 ，让前端自己处理】
            if($subjectType == 32){// 填空题【确切答案】
                $replaceKV = [$val => $beginSplit . $strLen . $endSplit];
            }else{// 填空题【人工打分】
                $replaceKV = [$strLen => $beginSplit . $strLen . $endSplit];
            }
            // 替换内容
            Tool::strReplaceKV($title, $replaceKV, $beginSplit, $endSplit);
            return $title;
        }
        $subject_css = $extendParams['subject_css'] ?? '';// 试题 css的名称
        $input_name_pre = $extendParams['input_name_pre'] ?? '';
        $input_name_back = $extendParams['input_name_back'] ?? '';
        $inputShowType = static::getInputShowType($beginSplit);
        $input_name = $input_name_pre . '_' . $inputShowType . $input_name_back;// 一般为 **_1[] 或 **_2[] 或 **_2[]
        $input_val = '';// 答案值
        $underlineCss = '';// 'text-decoration:underline;';// 下划线css样式
        $underlineStr = '__';// 默认的下划线字符 ： 值可以是 __ 或 '　'

        // 如果不显示答案，下划线用__[不要用css下划线],可显示也可以复制到word
        if(($answerShopType & 16) == 16){// 显示答案值
            // 显示 或 需要下划线
            if(($answerShopType & (1 | 2)) > 0 && in_array($beginSplit, [CompanySubject::SUBJECT_TYPE_BIG_SPLIT_BEGIN, CompanySubject::SUBJECT_TYPE_UNDERLINE_BRACKETS_SPLIT_BEGIN])){// 下划线
                if(($answerShopType & 1) == 1){
                    $input_val = str_replace([CompanySubject::SUBJECT_TYPE_BIG_SPLIT_MID],[$underlineStr .'或' . $underlineStr], $val);// 下划线可复制到word ；用下划线__
                }else  if(($answerShopType & 2) == 2){
                    $underlineCss = 'text-decoration:underline;';// 下划线css样式
                    $underlineStr = '　';
                    $input_val = str_replace([CompanySubject::SUBJECT_TYPE_BIG_SPLIT_MID],[$underlineStr . '或' . $underlineStr], $val);// 下划线不可复制到word ；用 css下划线
                }
            }else{// 空格  输入框 或 空格
                $underlineStr = '　';
                $input_val = str_replace([CompanySubject::SUBJECT_TYPE_BIG_SPLIT_MID],[$underlineStr . '或' . $underlineStr], $val);
            }
        }

        // 下划线格式  墙面装饰分为________和________，不同的墙面有着不同的装饰效果和功能。
        // 小括号 + 下划线 格式    墙面装饰分为(________)和(________)，不同的墙面有着不同的装饰效果和功能。
        if(in_array($beginSplit, [CompanySubject::SUBJECT_TYPE_BIG_SPLIT_BEGIN, CompanySubject::SUBJECT_TYPE_UNDERLINE_BRACKETS_SPLIT_BEGIN])){//
            if(($answerShopType & (1 | 2)) > 0){
                $inputStr = str_repeat($underlineStr, $strLen);

                if(($answerShopType & 16) == 16){// 显示答案值
                    $inputStr =  '' . str_repeat($underlineStr, 1) . $input_val . str_repeat($underlineStr, 1) . '';
                }

                $inputStr =  '<span  style="' . $underlineCss . '" class="' . $subject_css . '">' . $inputStr . '</span>';
            }else if(($answerShopType & 4) == 4){// 输入框式
                $inputStr = '<input type="text" value="' . $input_val . '" name="' . $input_name . '" style="border:none; border-bottom: 1px solid #000;width:' . $inputWidth . 'px;"  class="' . $subject_css . '"/>';
            }
            // 加小括号
            if($beginSplit == CompanySubject::SUBJECT_TYPE_UNDERLINE_BRACKETS_SPLIT_BEGIN){
                $inputStr = '(' . $inputStr . ')';
            }

        }else if($beginSplit == CompanySubject::SUBJECT_TYPE_BRACKETS_SPLIT_BEGIN){// 小括号格式   墙面装饰分为(          )和(          )，不同的墙面有着不同的装饰效果和功能。

            if(($answerShopType & (1 | 2)) > 0){
                $inputStr = '(<span  style="' . $underlineCss . '" class="' . $subject_css . '">' . str_repeat('　', $strLen) . '</span>)';
                if(($answerShopType & 16) == 16){// 显示答案值
                    $inputStr = '(<span  style="' . $underlineCss . '" class="' . $subject_css . '">' . '　' . $input_val . '　' . '</span>)';
                }

            }else if(($answerShopType & 4) == 4){// 输入框式
                $inputStr = '(<input type="text" value="' . $input_val . '" name="' . $input_name . '"  style="border:none;width:' . $inputWidth . 'px;"  class="' . $subject_css . '"/>)';
            }

        }
        if($subjectType == 32){// 填空题【确切答案】
            $replaceKV = [$val => $inputStr];
        }else{// 填空题【人工打分】
            $replaceKV = [$strLen => $inputStr];
        }
        // 替换内容
        Tool::strReplaceKV($title, $replaceKV, $beginSplit, $endSplit);
        return $title;
    }

    /**
     * 根据分隔符获得填空题填写位置类型
     *
     * @param string  $beginSplit
     * @return  int 填空题填写位置类型 1下划线;2小括号;4小括号 + 下划线
     * @author zouyan(305463219@qq.com)
     */
    public static function getInputShowType($beginSplit){
        if($beginSplit == CompanySubject::SUBJECT_TYPE_BIG_SPLIT_BEGIN){
            return CompanySubject::INPUT_SHOW_UNDERLINE;
        }else if($beginSplit == CompanySubject::SUBJECT_TYPE_BRACKETS_SPLIT_BEGIN){
            return CompanySubject::INPUT_SHOW_BRACKETS;
        }else if($beginSplit == CompanySubject::SUBJECT_TYPE_UNDERLINE_BRACKETS_SPLIT_BEGIN){
            return CompanySubject::INPUT_SHOW_UNDERLINE_BRACKETS;
        }
        return 0;
    }

    /**
     * 格式化 答案信息
     *
     * @param object $subjectInfo 试题信息--一维数组
     *         下标：subject_answer  单选、多选 的选项
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     * @param int $isExport 是否导出 0非导出 ；1导出数据
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
    //
    // $subjectInfo
    public static function formatAnswer(&$subjectInfo, &$returnFields, $isExport = 0){

        $formatTitle = $subjectInfo['title'] ?? '';
        // 单选、多选
        if(isset($subjectInfo['subject_answer']) && count($subjectInfo['subject_answer']) > 0){
            $subject_answer = $subjectInfo['subject_answer'];
            $orderKeys = CompanySubjectAnswerDBBusiness::$orderKeys;
            $subject_answer = Tool::php_multisort($subject_answer, $orderKeys);
            $subjectInfo['subject_answer'] = $subject_answer;

            $answers = [];
            $rightAnswers = [];
            $key = ord("A");
            $answerSplit = CompanySubject::SUBJECT_ITEM_ANSWER_SPLIT; //分隔符
            foreach($subject_answer as $an_k => $answer){
                $colum = chr($key);// 字母
                if($isExport == 1){// 导出
                    array_push($answers, $answer['answer_content'] . $answerSplit . ($answer['is_right'] == CompanySubjectAnswer::IS_RIGHT_RIGHT ? '√' : '×') );
                }else{
                    array_push($answers, $colum . '、' .$answer['answer_content'] . '   ' . ($answer['is_right'] == CompanySubjectAnswer::IS_RIGHT_RIGHT ? '<span class="right">√</span>' : '<span class="wrong">×</span>') );
                }

                if($answer['is_right'] == CompanySubjectAnswer::IS_RIGHT_RIGHT) array_push($rightAnswers, $colum);

                $subjectInfo['subject_answer'][$an_k]['colum'] = $colum;

                $subjectInfo[$colum] = $answer['answer_content'];
                if(!in_array($colum, $returnFields)) $returnFields[$colum] = $colum;
                $key += 1;
            }


            for($i = $key; $i <= ord(CompanySubject::ITEM_MAX_ANSWER_ORD); $i++){
                $colum = chr($i);
                $subjectInfo[$colum] = '';
                if(!in_array($colum, $returnFields)) $returnFields[$colum] = $colum;
            }

            $subjectInfo['answer_right'] = implode('、', $rightAnswers);// A、B、C
            if(!in_array('answer_right', $returnFields)) $returnFields['answer_right'] = 'answer_right';

            if($isExport == 1) {// 导出
                $subjectInfo['answer_txt'] = implode(PHP_EOL, $answers);
            }else{
                $subjectInfo['answer_txt'] = implode('<br/>', $answers);
            }
            if(!in_array('answer_txt', $returnFields)) $returnFields['answer_txt'] = 'answer_txt';
            // unset($subjectInfo['subject_answer']);
        } else {
            $key = ord("A");
            for($i = $key; $i <= ord(CompanySubject::ITEM_MAX_ANSWER_ORD); $i++){
                $colum = chr($i);
                $subjectInfo[$colum] = '';
                if(!in_array($colum, $returnFields)) $returnFields[$colum] = $colum;
            }
            $subjectInfo['answer_right'] = '';
            if(!in_array('answer_right', $returnFields)) $returnFields['answer_right'] = 'answer_right';
            $subjectInfo['answer_txt'] = '';
            if(!in_array('answer_txt', $returnFields))  $returnFields['answer_txt'] = 'answer_txt';

        }

        // 对判断题进行处理
        $subject_type = $subjectInfo['subject_type'] ?? '';
        $answer = $subjectInfo['answer'] ?? '';
        if($subject_type == CompanySubject::SUBJECT_TYPE_JUDGE && in_array($answer,array_keys(CompanySubject::ANSWER_ARR))){
            if($isExport == 1) {// 导出
                $answerTxt = ($answer == CompanySubject::ANSWER_RIGHT) ? '√' : '×';
            }else{
                $answerTxt = ($answer == CompanySubject::ANSWER_RIGHT) ? '<span class="right">√</span>' : '<span class="wrong">×</span>';
            }
            $subjectInfo['answer_txt'] = $answerTxt;
            $subjectInfo['answer_right'] = $answerTxt;
        }

        // 对 填空题[确切答案]   和  填空题[人工批阅] 的标题地进行处理
        if(in_array($subject_type, [CompanySubject::SUBJECT_TYPE_COMPLETAION_HAND, CompanySubject::SUBJECT_TYPE_COMPLETION])){

            if($isExport == 1) {// 导出
                $temAnswerList = static::formatQuoteTitle($formatTitle, 1 | 16, $subject_type, true);
            }else{
                $temAnswerList = static::formatQuoteTitle($formatTitle, 1 | 16, $subject_type, true);
            }
        }
        $subjectInfo['title_format'] = $formatTitle;
        if(!in_array('title_format', $returnFields)) $returnFields['title_format'] = 'title_format';
        return $subjectInfo;
    }

}
