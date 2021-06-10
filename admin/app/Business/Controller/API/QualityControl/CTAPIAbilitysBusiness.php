<?php
//能力验证
namespace App\Business\Controller\API\QualityControl;

use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPIAbilitysBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\AbilitysAPI';
    public static $table_name = 'abilitys';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

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
     * @param array $extendParams  扩展参数---可能会用；需要指定的实时特别的 条件配置
     *          格式： [
     *                    '关系下标' => [
     *                          'fieldValParams' => [ '字段名1' => '字段值--多个时，可以是一维数组或逗号分隔字符', ...],// 也可以时 Tool getParamQuery 方法的参数$fieldValParams的格式
     *                          'sqlParams' => []// 与参数 $sqlDefaultParams 相同格式的条件
     *                          '关系下标' => ... 下下级的
     *                       ]
     *                ]
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
            // 获得项目标准
            'project_standards_list' => CTAPIProjectStandardsBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'ability_id']
                , 2, 4 | 8
                ,'','',
                CTAPIProjectStandardsBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'project_standards_list'),
                    static::getUboundRelationExtendParams($extendParams, 'project_standards_list')),
                static::getRelationSqlParams([], $extendParams, 'project_standards_list'), '', []),
            // 获得验证数据项
            'project_submit_items_list' => CTAPIProjectSubmitItemsBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'ability_id']
                , 2, 4 | 8
                ,'','',
                CTAPIProjectSubmitItemsBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'project_submit_items_list'),
                    static::getUboundRelationExtendParams($extendParams, 'project_submit_items_list')),
                static::getRelationSqlParams([], $extendParams, 'project_submit_items_list'), '', []),
            // 获得能力验证报名项-- 企业报名的
            'ability_join_items_info' => CTAPIAbilityJoinItemsBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'ability_id']
                , 1, 2
                ,'','',
                CTAPIAbilityJoinItemsBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'ability_join_items_info'),
                    static::getUboundRelationExtendParams($extendParams, 'ability_join_items_info')),
                static::getRelationSqlParams(['where' => [
                    ['admin_type', $user_info['admin_type']],
                    ['staff_id', $user_info['id']]
                ]], $extendParams, 'ability_join_items_info'), '', ['extendConfig' => ['infoHandleKeyArr' => ['judgeJoined']]]),
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

        if(($return_num & 2) == 2){// 给上一级返回下标名称 ability_name => '项目名称'
            $one_field = ['key' => 'ability_name', 'return_type' => 2, 'ubound_name' => 'ability_name', 'split' => '、'];// 获得名称
            if(!isset($return_data['one_field'])) $return_data['one_field'] = [];
            array_push($return_data['one_field'], $one_field);
        }

        return $return_data;
    }
    // ****表关系***需要重写的方法**********结束***********************************


    /**
     * 格式化数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 需要格式化的数据---二维数组(如果是一维数组，是转成二维数组后的数据)
     * @param array $handleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @param boolean 原数据类型 true:二维[默认];false:一维
     * @return  boolean true
     * @author zouyan(305463219@qq.com)
     */
//    public static function handleDataFormat(Request $request, Controller $controller, &$data_list, $handleKeyArr, $isMulti = true){
//
//        // 重写开始
//
//        $isNeedHandle = false;// 是否真的需要遍历处理数据 false:不需要：true:需要 ；只要有一个需要处理就标记
//
//        $projectStandardsArr = [];// 项目标准
//        $projectSubmitItemsArr = [];// 验证数据项
//        //        if(!empty($data_list) ){
//        // 获得项目标准
//        if(in_array('projectStandards', $handleKeyArr)){
//            $abilityIdArr = array_values(array_filter(array_column($data_list,'id')));// 资源id数组，并去掉值为0的
//            $projectStandardsList = [];
//            // 查询条件
////            if(!empty($abilityIdArr)){
////                // 获得企业资质证书
////                $projectStandardsQueryParams = [
////                    'where' => [
////                        // ['type_id', 5],
////    //                //['mobile', $keyword],
////                    ],
////    //            'select' => [
////    //                'id','company_id','position_name','sort_num'
////    //                //,'operate_staff_id','operate_staff_id_history'
////    //                ,'created_at'
////    //            ],
////                    // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
////                ];
////                Tool::appendParamQuery($projectStandardsQueryParams, $abilityIdArr, 'ability_id', [0, '0', ''], ',', false);
////                $projectStandardsList = CTAPIProjectStandardsBusiness::getBaseListData($request, $controller, '', $projectStandardsQueryParams,[], 1,  1)['data_list'] ?? [];
////            }
//            $extParams = [];
//            $projectStandardsList =  CTAPIProjectStandardsBusiness::getFVFormatList( $request,  $controller, 1, 1,  ['ability_id' => $abilityIdArr], false,[], $extParams);
//            if(!empty($projectStandardsList)) $projectStandardsArr = Tool::arrUnderReset($projectStandardsList, 'ability_id', 2);
//            if(!$isNeedHandle && !empty($projectStandardsArr)) $isNeedHandle = true;
//        }
//        // 获得验证数据项
//        if(in_array('projectSubmitItems', $handleKeyArr)){
//            $abilityIdArr = array_values(array_filter(array_column($data_list,'id')));// 资源id数组，并去掉值为0的
////            $projectSubmitItemsList = [];
////            // 查询条件
////            if(!empty($abilityIdArr)){
////                // 获得企业资质证书
////                $projectSubmitItemsQueryParams = [
////                    'where' => [
////                        // ['type_id', 5],
////                        //                //['mobile', $keyword],
////                    ],
////                    //            'select' => [
////                    //                'id','company_id','position_name','sort_num'
////                    //                //,'operate_staff_id','operate_staff_id_history'
////                    //                ,'created_at'
////                    //            ],
////                    // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
////                ];
////                Tool::appendParamQuery($projectSubmitItemsQueryParams, $abilityIdArr, 'ability_id', [0, '0', ''], ',', false);
////                $projectSubmitItemsList = CTAPIProjectSubmitItemsBusiness::getBaseListData($request, $controller, '', $projectSubmitItemsQueryParams,[], 1,  1)['data_list'] ?? [];
////            }
//            $extParams = [];
//            $projectSubmitItemsList =  CTAPIProjectSubmitItemsBusiness::getFVFormatList( $request,  $controller, 1, 1,  ['ability_id' => $abilityIdArr], false,[], $extParams);
//            if(!empty($projectSubmitItemsList)) $projectSubmitItemsArr = Tool::arrUnderReset($projectSubmitItemsList, 'ability_id', 2);
//            if(!$isNeedHandle && !empty($projectSubmitItemsArr)) $isNeedHandle = true;
//        }
//
//
//        // 判断自己是否已经报名
//        $joinedAbilityIds = [];// 已报名的项目ID数组
//        if(in_array('joined', $handleKeyArr)){
//
//            $user_info = $controller->user_info;
//            $abilityIds = array_values(array_unique(array_column($data_list,'id')));
//            if(!empty($abilityIds)){
//                // 还得查一下不是状态2的记录，再获得kv把
////                $queryParams = [
////                    'where' => [
////                        ['admin_type', $user_info['admin_type']],
////                        ['staff_id', $user_info['id']],
////                        // ['ability_id', $id],
////                    ],
////                    // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
////                ];
////                Tool::appendParamQuery($queryParams, $abilityIds, 'ability_id', [0, '0', ''], ',', false);
////                $joinItemDataList = CTAPIAbilityJoinItemsBusiness::ajaxGetQueryListCTL($request, $controller, '', $queryParams, [], 1);
//
//                $extParams = [];
//                $joinItemDataList =  CTAPIAbilityJoinItemsBusiness::getFVFormatList( $request,  $controller, 1, 1,  [
//                    'ability_id' => $abilityIds , 'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']], false,[], $extParams);
//                if(!empty($joinItemDataList)) $joinedAbilityIds = array_values(array_unique(array_column($joinItemDataList,'ability_id')));
//
//            }
//        }
//
//
//        //        }
//        // 改为不返回，好让数据下面没有数据时，有一个空对象，方便前端或其它应用处理数据
//        // if(!$isNeedHandle){// 不处理，直接返回 // if(!$isMulti) $data_list = $data_list[0] ?? [];
//        //    return true;
//        // }
//
//        foreach($data_list as $k => $v){
//        //            // 公司名称
//        //            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//        //            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//
//            // 格式化发布时间
//            if(isset($v['created_at']) && !empty($v['created_at'])){
//                $data_list[$k]['created_at_format'] = judgeDate($v['created_at'],'Y-m-d');
//            }
//
//            // 获得项目标准
//            if(in_array('projectStandards', $handleKeyArr)){
//                //  [{'id': 0, 'tag_name': '标签名称'},..]
//                $configArr = [];
//                $temArr = $projectStandardsArr[$v['id']] ?? [];
//                foreach($temArr as $info){
//                    array_push($configArr, [
//                        'id' => $info['id'],
//                        'tag_name' => $info['name'],
//                    ]);
//                }
//                $data_list[$k]['project_standards'] = $configArr;
//                $data_list[$k]['project_standards_text'] = implode('<br/>', Tool::getArrFields($temArr, 'name'));
//            }
//            // 获得验证数据项
//            if(in_array('projectSubmitItems', $handleKeyArr)){
//                //  [{'id': 0, 'tag_name': '标签名称'},..]
//                $configArr = [];
//                $temArr = $projectSubmitItemsArr[$v['id']] ?? [];
//                foreach($temArr as $info){
//                    array_push($configArr, [
//                        'id' => $info['id'],
//                        'tag_name' => $info['name'],
//                    ]);
//                }
//                $data_list[$k]['submit_items'] = $configArr;
//                $data_list[$k]['submit_items_text'] = implode('<br/>', Tool::getArrFields($temArr, 'name'));
//            }
//            // 判断自己是否已经报名
//            if(in_array('joined', $handleKeyArr)){
//                $is_joined = 0;
//                $is_joined_text = '未报名';
//                if(in_array($v['id'], $joinedAbilityIds)){
//                    $is_joined = 1;
//                    $is_joined_text = '已报名';
//                }
//                $data_list[$k]['is_joined'] = $is_joined;
//                $data_list[$k]['is_joined_text'] = $is_joined_text;
//            }
//        }
//
//        // 重写结束
//        return true;
//    }

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

        $ability_type_id = CommonRequest::getInt($request, 'ability_type_id');
        if($ability_type_id > 0 )  array_push($queryParams['where'], ['ability_type_id', '=', $ability_type_id]);

        $is_publish = CommonRequest::getInt($request, 'is_publish');
        if($is_publish > 0 )  array_push($queryParams['where'], ['is_publish', '=', $is_publish]);

        $status = CommonRequest::get($request, 'status');
        if(strlen($status) > 0 && $status != 0)  Tool::appendParamQuery($queryParams, $status, 'status', [0, '0', ''], ',', false);

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

    /**
     * 判断记录是否到开始报名时间--方法回全时throws错误，根据调用场景 可以用try  catch
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $dataList 能力验证记录  二维或一维数组  ；必须有 id ability_name join_begin_date join_end_date status 下标
     * @param int $judge_type 判断类型  1 判断是否 开始  2 判断是否结束 [说明 3 是 1+2 ：非有效的报名就会报错]   4 判断是否已经报过名
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeCanJoin(Request $request, Controller $controller, $dataList = [], $judge_type = 1, $notLog = 0){
        $currentNow = Carbon::now()->toDateTimeString();
        // 如果是一维数组，转为二维
        $isMulti = Tool::isMultiArr($dataList, true);

        $user_info = $controller->user_info;

        foreach($dataList as $k => $v){
            $id = $v['id'];
            $ability_name = $v['ability_name'];
            $join_begin_date = $v['join_begin_date'];
            $join_end_date = $v['join_end_date'];
            $status = $v['status'];// 状态(1待开始 、2报名中、4进行中、8已结束 16 已取消【作废】)
            $errStr = '';
            // 判断是否已开始报名
            if(in_array($status, [1,2])){
                if( ($judge_type & 1) == 1 && Tool::diffDate($currentNow, $join_begin_date, 1, '时间', 2) > 0) $errStr = $ability_name . '还未到开始报名时间！';
                if( ($judge_type & 2) == 2 && Tool::diffDate($join_end_date, $currentNow, 1, '时间', 2) > 0) $errStr = $ability_name . '报名已结束！' . $join_end_date. ' -- ' . $currentNow;
                if( ($judge_type & 3) == 3 && $status == 1){// 说明可以开始报名了--修改状态
                    $saveData = [
                        'status' => 2,
                    ];
                    static::replaceById($request, $controller, $saveData,$id, [], true, 1);
                }
            }else{
                if( ($judge_type & 3) == 3 ) $errStr = $ability_name . ($v['status_text'] ?? '');
            }

            // 判断是否已经报过名
            // $ability_ids = array_values(array_unique(array_column($dataList,'id')));
            // if(!empty($ability_ids)){
            if( ($judge_type & 4) == 4 ){
                $queryParams = [
                    'where' => [
                        ['admin_type', $user_info['admin_type']],
                        ['staff_id', $user_info['id']],
                        ['ability_id', $id],
//                ['teacher_status',1],
                    ],
                    // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
                ];
                // Tool::appendParamQuery($queryParams, $ability_ids, 'ability_id', [0, '0', ''], ',', false);
                $joinItemInfo = CTAPIAbilityJoinItemsBusiness::getInfoQuery($request, $controller, '', 0, 1, $queryParams, [], 1);
                if(!empty($joinItemInfo))  $errStr = $ability_name . '已报名，不可重复报名！';

            }
            // }
            if($errStr != ''){
                throws($errStr);
                break;
            }
        }
    }


    /**
     * 获得列表数据时，对查询结果进行导出操作--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 初始数据  -- 二维数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function importTemplateExcel(Request $request, Controller $controller, $data_list = [], $notLog = 0){
        $data_list = [];
        $headArr = ['company_name'=>'机构名称', 'test_code'=>'机构代码', 'test_item'=>'检测项目',
            'test_result'=>'验证结果', 'remarks'=>'备注' ];
        ImportExport::export('','能力验证结果导入模版',$data_list,1, $headArr, 0, ['sheet_title' => '能力验证结果导入模版']);
    }


    /**
     * 批量导入员工--通过文件路径
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $fileName 文件全路径
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function importByFile(Request $request, Controller $controller, $fileName = '', $notLog = 0){
        // $fileName = 'staffs.xlsx';
        $dataStartRow = 1;// 数据开始的行号[有抬头列，从抬头列开始],从1开始
        // 需要的列的值的下标关系：一、通过列序号[1开始]指定；二、通过专门的列名指定;三、所有列都返回[文件中的行列形式],$headRowNum=0 $headArr=[]
        $headRowNum = 1;//0:代表第一种方式，其它数字：第二种方式; 1开始 -必须要设置此值，$headArr 参数才起作用
        // 下标对应关系,如果设置了，则只获取设置的列的值
        // 方式一格式：['1' => 'name'，'2' => 'chinese',]
        // 方式二格式: ['姓名' => 'name'，'语文' => 'chinese',]
        $headArr = [
            '机构名称' => 'company_name',
            '机构代码' => 'test_code',
            '检测项目' => 'test_item',
            '验证结果' => 'test_result',
            '备注' => 'remarks',
        ];
//        $headArr = [
//            '1' => 'name',
//            '2' => 'chinese',
//            '3' => 'maths',
//            '4' => 'english',
//        ];
        try{
            $dataArr = ImportExport::import($fileName, $dataStartRow, $headRowNum, $headArr);
        } catch ( \Exception $e) {
            throws($e->getMessage());
        }
        return self::import($request, $controller, $dataArr, $notLog);
    }


    /**
     * 批量导入
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $saveData 要保存或修改的数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function import(Request $request, Controller $controller, $saveData , $notLog = 0)
    {
        $company_id = $controller->company_id;

        $test_year = CommonRequest::getInt($request, 'test_year');
//
//        Tool::arrAppendKeys($saveData, ['test_year' => $test_year]);

        // 参数
        $apiParams = [
            'test_year' => $test_year,
            'save_data' => $saveData,
            'company_id' => $company_id,
            'operate_staff_id' =>  $controller->user_id,
            'modifAddOprate' => 1,
        ];
        $methodName = 'importDatas';
//        if(isset($saveData['mini_openid']))  $methodName = 'replaceByIdWX';
        $result = CTAPIAbilityJoinItemsResultsBusiness::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);
        return $result;
//        $requestData = [
//            'company_id' => $company_id,
//            'staff_id' =>  $controller->user_id,
//            'admin_type' =>  $controller->admin_type,//self::$admin_type,
//            'save_data' => $saveData,
//        ];
//        $url = config('public.apiUrl') . config('apiUrl.apiPath.staffImport');
//        // 生成带参数的测试get请求
//        // $requestTesUrl = splicQuestAPI($url , $requestData);
//        return HttpRequest::HttpRequestApi($url, $requestData, [], 'POST');
    }
}
