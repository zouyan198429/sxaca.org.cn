<?php
// 试题
namespace App\Business\Controller\API\QualityControl;

use App\Business\DB\QualityControl\CompanySubjectDBBusiness;
use App\Models\QualityControl\CompanySubject;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPICompanySubjectBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\CompanySubjectAPI';
    public static $table_name = 'company_subject';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    public static $orderBy = CompanySubject::ORDER_BY;// ['sort_num' => 'desc', 'id' => 'desc'];// 默认的排序字段数组 ['id' => 'desc']--默认 或 ['sort_num' => 'desc', 'id' => 'desc']

    // 是否激活(0:未激活；1：已激活)
//    public static $isActiveArr = [
//        '0' => '未激活',
//        '1' => '已激活',
//    ];

    /**
     * 特殊的验证 关键字 -单个 的具体验证----具体的子类----重写此方法来实现具体的验证
     *
     * @param array $mustFields 表对象字段验证时，要必填的字段，指定必填字须，为后面的表字须验证做准备---一维数组
     * @param array $judgeData 需要验证的数据---数组-- 根据实际情况的维数不同。
     * @param string $key 验证规则的关键字 -单个
     * @param array $tableLangConfig 多语言单个数据表配置数组--也就是表多语言的那个配置数组
     * @param array $extParams 其它扩展参数，
     * @return  array 错误：非空数组；正确：空数组
     * @author zouyan(305463219@qq.com)
     */
    public static function singleJudgeDataByKey(&$mustFields = [], &$judgeData = [], $key = '', $tableLangConfig = [], $extParams = []){
        if(!is_array($mustFields)) $mustFields = [];
        $errMsgs = [];// 错误信息的数组--一维数组，可以指定下标
        // if( (is_string($key) && strlen($key) <= 0 ) || (is_array($key))) return $errMsgs;
        switch($key){
            case 'add':// 添加；

                break;
            case 'modify':// 修改
                break;
            case 'replace':// 新加或修改

                $id = $extParams['id'] ?? 0;
                if($id > 0){

                }

                break;
            default:
                break;
        }
        return $errMsgs;
    }

    // ****表关系***需要重写的方法**********开始***********************************
    /**
     * 获得处理关系表数据的配置信息--重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $relationKeys
     * @param array $extendParams  扩展参数---可能会用
     * @return  array 表关系配置信息
     * @author zouyan(305463219@qq.com)
     */
    public static function getRelationConfigs(Request $request, Controller $controller, $relationKeys = [], $extendParams = []){
        if(empty($relationKeys)) return [];
        list($relationKeys, $relationArr) = static::getRelationParams($relationKeys);// 重新修正关系参数
        $user_info = $controller->user_info;
        $user_id = $controller->user_id;
        $user_type = $controller->user_type;
        // 关系配置
        $relationFormatConfigs = [
            // 下标 'relationConfig' => []// 下一个关系
            // 获得企业名称
//            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
//                , ['admin_type' => 'admin_type', 'staff_id' => 'id']
//                , 1, 2
//                ,'','',
//                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
//                    static::getUboundRelation($relationArr, 'company_info'),
//                    static::getUboundRelationExtendParams($extendParams, 'company_info')),
//                static::getRelationSqlParams(['where' => [['admin_type', 2]]], $extendParams, 'company_info'), '', []),
            // 获得企业名称
            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['company_id' => 'id']
                , 1, 16
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_info'),
                    static::getUboundRelationExtendParams($extendParams, 'company_info')),
                static::getRelationSqlParams([], $extendParams, 'company_info'), '', []),// ['where' => [['admin_type', 2]]]
            // 获得试题分析
            'analyse_answer' => CTAPICompanySubjectAnalyseBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'subject_id']
                , 1, 2
                ,'','',
                CTAPICompanySubjectAnalyseBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'analyse_answer'),
                    static::getUboundRelationExtendParams($extendParams, 'analyse_answer')),
                static::getRelationSqlParams([], $extendParams, 'analyse_answer'), '', []),
            // 获得试题答案选项
            'subject_answer' => CTAPICompanySubjectAnswerBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'subject_id']
                , 2, 1
                ,'','',
                CTAPICompanySubjectAnswerBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'subject_answer'),
                    static::getUboundRelationExtendParams($extendParams, 'subject_answer')),
                static::getRelationSqlParams(['orderBy' => CTAPICompanySubjectAnswerBusiness::$orderBy], $extendParams, 'subject_answer'), '', []),
        ];
        return Tool::formatArrByKeys($relationFormatConfigs, $relationKeys, false);
    }

    /**
     * 获得要返回数据的return_data数据---每个对象，重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $return_num 指定要获得的关系数据类型格式化后的数据 编号 1[占用：原数据] 2 4 8..
     * @return  array 表关系配置信息
     * @author zouyan(305463219@qq.com)
     */
    public static function getRelationConfigReturnData(Request $request, Controller $controller, $return_num = 0){
        $return_data = [];// 为空，则会返回对应键=> 对应的数据， 具体的 结构可以参考 Tool::formatConfigRelationInfo  $return_data参数格式

        if(($return_num & 1) == 1) {// 返回源数据--特别的可以参考这个配置
            $return_data['old_data'] = ['ubound_operate' => 1, 'ubound_name' => '', 'fields_arr' => [], 'ubound_keys' => [], 'ubound_type' =>1];
        }

//        if(($return_num & 2) == 2){// 给上一级返回名称 company_name 下标
//            $one_field = ['key' => 'company_name', 'return_type' => 2, 'ubound_name' => 'company_name', 'split' => '、'];// 获得名称
//            if(!isset($return_data['one_field'])) $return_data['one_field'] = [];
//            array_push($return_data['one_field'], $one_field);
//        }

        return $return_data;
    }
    // ****表关系***需要重写的方法**********结束***********************************

    /**
     * 获得列表数据时，查询条件的参数拼接--有特殊的需要自己重写此方法--每个字类都有此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function joinListParams(Request $request, Controller $controller, &$queryParams, $notLog = 0){
        // 自己的参数查询拼接在这里-- 注意：多个id 的查询默认就已经有了，参数是 ids  多个用逗号分隔

//        $aaaa = CommonRequest::get($request, 'aaaa');
//        if(strlen($aaaa) > 0  && !in_array($aaaa, [0, '-1']))  Tool::appendCondition($queryParams, 'aaaa',  $aaaa . '=' . $aaaa, '&');

//        $bbbb = CommonRequest::get($request, 'bbbb');
//        if(strlen($bbbb) > 0 && !in_array($bbbb, [0, '-1']))  Tool::appendParamQuery($queryParams, $bbbb, 'bbbb', [0, '0', ''], ',', false);

        $type_no = CommonRequest::get($request, 'type_no');
        if(strlen($type_no) > 0  && !in_array($type_no, [0, '-1']))  Tool::appendCondition($queryParams, 'type_no',  $type_no . '=' . $type_no, '&');

        $subject_type = CommonRequest::get($request, 'subject_type');
        if(strlen($subject_type) > 0  && !in_array($subject_type, [0, '-1']))  Tool::appendCondition($queryParams, 'subject_type',  $subject_type . '=' . $subject_type, '&'
            , 'where', 1, false, CompanySubject::SUBJECT_TYPE_ARR);

        $answer = CommonRequest::get($request, 'answer');
        if(strlen($answer) > 0  && !in_array($answer, [0, '-1']))  Tool::appendCondition($queryParams, 'answer',  $answer . '=' . $answer, '&'
            , 'where', 1, false, CompanySubject::ANSWER_ARR);

        $open_status = CommonRequest::get($request, 'open_status');
        if(strlen($open_status) > 0  && !in_array($open_status, [0, '-1']))  Tool::appendCondition($queryParams, 'open_status',  $open_status . '=' . $open_status, '&'
            , 'where', 1, false, CompanySubject::OPEN_STATUS_ARR);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }


    /**
     * 格式化关系数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $main_list 关系主记录要格式化的数据
     * @param array $data_list 需要格式化的从记录数据---二维数组(如果是一维数组，是转成二维数组后的数据)
     * @param array $handleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     * @return array  新增的字段 一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function handleRelationDataFormat(Request $request, Controller $controller, &$main_list, &$data_list, $handleKeyArr, &$returnFields = []){
        // if(empty($data_list)) return $returnFields;
        // 重写开始

        // 组织分类编号文字
        if(in_array('initTypeNoText', $handleKeyArr)){
            $type_no_kv = CTAPICompanySubjectTypeBusiness::getListKV($request, $controller, ['key' => 'type_no', 'val' => 'type_name'], [
                // 'sqlParams' => ['orderBy' => CompanySubject::ORDER_BY]// 'where' => [['open_status', 1]],
            ]);
            foreach($data_list as $k => &$v){
                $type_no = $v['type_no'];
                $seled_type_no_kv = [];
                foreach($type_no_kv as $t_k => $t_v){
                    if( ($type_no & $t_k) === $t_k) $seled_type_no_kv[$t_k] = $t_v;
                }
                $v['type_no_kv'] = $seled_type_no_kv;
                $v['type_no_text'] = implode('、', $seled_type_no_kv);
            }
            $returnFields['type_no_kv'] = 'type_no_kv';
            $returnFields['type_no_text'] = 'type_no_text';
        }


        // 重写结束
        return $returnFields;
    }

    /**
     * 格式化数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 需要格式化的数据---二维数组(如果是一维数组，是转成二维数组后的数据)
     * @param array $finalHandleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     * @param boolean 原数据类型 true:二维[默认];false:一维  ----没有用了，到不到
     * @return  array
     * @author zouyan(305463219@qq.com)
     */
    public static function handleFinalDataFormat(Request $request, Controller $controller, &$data_list, $finalHandleKeyArr, &$returnFields = [], $isMulti = true)
    {

        // if(empty($data_list)) return $data_list;
        // 重写开始

//        if(in_array('mergeZeroName', $finalHandleKeyArr)){
//          // ...对 $data_list做特殊处理
//        }

        // 列表显示时对数据进行格式化--主有用于后台列表的格式化数据
        if(in_array('sysFormatData', $finalHandleKeyArr)){
            foreach($data_list as $k => &$v){

                $isExport = CommonRequest::getInt($request, 'is_export'); // 是否导出 0非导出 ；1导出数据
                CompanySubjectDBBusiness::formatAnswer($v, $returnFields, $isExport);
            }
        }

        return $data_list;
    }

    /**
     * 修改或修试题
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $ower_type 操作类型 0：大后台 ； >0 所属的用户
     * @return mixed  新增的字段 一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceInfo(Request $request, Controller $controller, $ower_type = 0){

        $id = CommonRequest::getInt($request, 'id');
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        $company_id = ($ower_type == 0) ? CommonRequest::getInt($request, 'company_id') : $controller->user_id;
        $type_no = CommonRequest::get($request, 'type_no');
        $subject_type = CommonRequest::getInt($request, 'subject_type');
        $title = CommonRequest::get($request, 'title');
        $title = stripslashes($title);
        $answer = CommonRequest::getInt($request, 'answer');
        $analyse_answer = CommonRequest::get($request, 'analyse_answer');
        $analyse_answer = stripslashes($analyse_answer);
        $open_status = CommonRequest::getInt($request, 'open_status');
        $sort_num = CommonRequest::getInt($request, 'sort_num');

        // 如果是字符，则转为数组
        Tool::valToArrVal($type_no);
        $sel_type_no = Tool::bitJoinVal($type_no);// 将位数组，合并为一个数值


        // 答案
        $answer_ids = CommonRequest::get($request, 'answer_id');// id值数组或 逗号分隔的多个id值
        Tool::valToArrVal($answer_ids, ',');// 不是数组，则转为数组
        // --特别要注意这个的分隔符不能用,逗号，
        $answer_contents = CommonRequest::get($request, 'answer_content');
        Tool::valToArrVal($answer_contents, CompanySubject::SUBJECT_TYPE_BIG_SPLIT_MID);// 不是数组，则转为数组
        $answer_val = CommonRequest::getInt($request, 'answer_val');// 1 2 4 8 ...
        $check_answer_vals = CommonRequest::get($request, 'check_answer_val');//一维数组或 逗号分隔的多个值
        Tool::valToArrVal($check_answer_vals, ',');// 不是数组，则转为数组

        // 答案
        $answerList = [];
        if(in_array($subject_type, [CompanySubject::SUBJECT_TYPE_RADIO, CompanySubject::SUBJECT_TYPE_CHECKBOX])){// 单选多选
            $answer = 0;
            $max_sort_num = count($answer_ids);
            if($max_sort_num <= 0) throws('试题[' . $title . ']不能没有选项!');
            $answerVal = 1;// 当前记录的值 1 2 4 8 ...

            // 对单选判断正确的答案是否正确
            $bitNumArr = [];
            if($subject_type == CompanySubject::SUBJECT_TYPE_RADIO && !Tool::isBitNumByArr($answer_val, $bitNumArr, $max_sort_num)){
                throws('单选正确答案编号不是有效的值【' . implode('、', $bitNumArr) . '】');
            }

            foreach($answer_ids as $k => $answer_id ){
                $is_right = 2;
                if($subject_type == CompanySubject::SUBJECT_TYPE_RADIO && $answer_val == $answerVal) $is_right = CompanySubject::ANSWER_RIGHT;// 单选
                // 多选
                if($subject_type == CompanySubject::SUBJECT_TYPE_CHECKBOX && in_array($answerVal, $check_answer_vals)) $is_right = CompanySubject::ANSWER_RIGHT;
                $temAnswer = [
                    'id' => $answer_id,
                    'company_id' => $company_id,
                    'sort_num' => $max_sort_num,
                    'answer_content' => $answer_contents[$k] ?? '',
                    'answer_val' => $answerVal,
                    'is_right' => $is_right,
                ];
                array_push($answerList, $temAnswer);
                if($is_right == CompanySubject::ANSWER_RIGHT) $answer = ($answer | $answerVal);
                $max_sort_num--;
                $answerVal *= 2;

            }
            if($answer <= 0 ){
                throws('试题[' . $title . ']不能没有正确答案!');
            }
        }else if(in_array($subject_type, [CompanySubject::SUBJECT_TYPE_COMPLETAION_HAND, CompanySubject::SUBJECT_TYPE_COMPLETION])){// 填空题[确切答案]  填空题[人工批阅]-- 判断是否有填空位置
            $temAnswerList = CompanySubjectDBBusiness::formatTitle($title, 0, $subject_type, true);
            if(empty($temAnswerList)) throws('试题[' . $title . ']不能没有填空位置！');
        }

        $saveData = [
            'company_id' => $company_id,
            'type_no' => $sel_type_no,
            'subject_type' => $subject_type,
            'title' => $title,
            'answer' => $answer,
            'open_status' => $open_status,
            'sort_num' => $sort_num,
            'analyse_answer' => $analyse_answer,
            'answer_list' => $answerList,
        ];

        if($id <= 0) {// 新加;要加入的特别字段
//                    $addNewData = [
//                        // 'account_password' => $account_password,
//                    ];
//                    $saveData = array_merge($saveData, $addNewData);
        }else{
            $info = CTAPICompanySubjectBusiness::getInfoData($request, $controller, $id, [], '', []);
            if(empty($info)) throws('记录不存在');
            if($ower_type > 0 && $company_id != $info['company_id']) throws('您没有此记录的操作权限');
            // 如果改变了所属企业,需要重新统计数
            if(isset($saveData['company_id']) && $company_id != $info['company_id']) $saveData['force_company_num'] = 1;
        }

        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPICompanySubjectBusiness::replaceById($request, $controller, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }
}
