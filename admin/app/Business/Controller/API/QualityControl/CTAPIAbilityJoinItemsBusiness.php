<?php
//能力验证报名项表
namespace App\Business\Controller\API\QualityControl;

use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPIAbilityJoinItemsBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\AbilityJoinItemsAPI';
    public static $table_name = 'ability_join_items';// 表名称
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
//        //        if(!empty($data_list) ){
//        // 获得能力验证名称
//        $abilityDataList = [];// 能力id 为下标 二维数组
//        $abilityKVList = [];// 能力id => 能力验证名称 的键值对
//        if(in_array('ability', $handleKeyArr)){
//            $abilityIdsArr = array_values(array_filter(array_column($data_list,'ability_id')));// 资源id数组，并去掉值为0的
//            // 查询条件
////            $abilityList = [];
////            if(!empty($abilityIdsArr)){
////                // 获得企业信息
////                $abilityQueryParams = [
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
////                Tool::appendParamQuery($abilityQueryParams, $abilityIdsArr, 'id', [0, '0', ''], ',', false);
////                $abilityList = CTAPIAbilitysBusiness::getBaseListData($request, $controller, '', $abilityQueryParams,[], 1,  1)['data_list'] ?? [];
////            }
//
//            $extParams = [];
//            $abilityList =  CTAPIAbilitysBusiness::getFVFormatList( $request,  $controller, 1, 1,  ['id' => $abilityIdsArr], false,[], $extParams);
//            if(!empty($abilityList)){
//                $abilityDataList = Tool::arrUnderReset($abilityList, 'id', 1);
//                $abilityKVList = Tool::formatArrKeyVal($abilityList, 'id', 'ability_name');
//            }
//            if(!$isNeedHandle && !empty($abilityDataList)) $isNeedHandle = true;
//        }
//        // 企业信息
//        $companyDataList = [];// 企业id 为下标 二维数组
//        $companyKVList = [];// 企业id => 企业名称 的键值对
//        if(in_array('company', $handleKeyArr)){
//            $staffIdsArr = array_values(array_filter(array_column($data_list,'staff_id')));// 资源id数组，并去掉值为0的
//            // 查询条件
////            $companyList = [];
////            if(!empty($staffIdsArr)){
////                // 获得企业信息
////                $companyQueryParams = [
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
////                Tool::appendParamQuery($companyQueryParams, $staffIdsArr, 'id', [0, '0', ''], ',', false);
////                $companyList = CTAPIStaffBusiness::getBaseListData($request, $controller, '', $companyQueryParams,[], 1,  1)['data_list'] ?? [];
////            }
//
//            $extParams =[];
//            $companyList =  CTAPIStaffBusiness::getFVFormatList( $request,  $controller, 1, 1,  ['id' => $staffIdsArr], false,[], $extParams);
//            if(!empty($companyList)){
//                $companyDataList = Tool::arrUnderReset($companyList, 'id', 1);
//                $companyKVList = Tool::formatArrKeyVal($companyList, 'id', 'company_name');
//            }
//            if(!$isNeedHandle && !empty($companyDataList)) $isNeedHandle = true;
//        }
//
//        // 获得报名的标准方法
//        $joinItemStandardKeyDataList = [];// 报名主表 id 为下标 二维数组
//        if(in_array('joinItemsStandards', $handleKeyArr)){
//            $joinStandardIdsArr = array_values(array_filter(array_column($data_list,'id')));// 资源id数组，并去掉值为0的
//            // 查询条件
////            $joinItemStandardList = [];
////            if(!empty($joinStandardIdsArr)){
////                // 获得企业信息
////                $joinItemStandardQueryParams = [
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
////                Tool::appendParamQuery($joinItemStandardQueryParams, $joinStandardIdsArr, 'ability_join_item_id', [0, '0', ''], ',', false);
////                // $joinItemStandardList = CTAPIAbilityJoinItemsBusiness::getBaseListData($request, $controller, '', $joinItemStandardQueryParams, [], 1,  1)['data_list'] ?? [];
////
////                $extParams = [
////                    'handleKeyArr' => ['project_standards'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
////                ];
////                $joinItemStandardList = CTAPIAbilityJoinItemsStandardsBusiness::getList($request, $controller, 1, $joinItemStandardQueryParams, [], $extParams)['result']['data_list'] ?? [];
////            }
//
//            $extParams = [
//                'handleKeyArr' => ['project_standards'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            ];
//            $joinItemStandardList =  CTAPIAbilityJoinItemsStandardsBusiness::getFVFormatList( $request,  $controller, 1, 1,  ['ability_join_item_id' => $joinStandardIdsArr], false,[], $extParams);
//
//            if(!empty($joinItemStandardList)){
//                $joinItemStandardKeyDataList = Tool::arrUnderReset($joinItemStandardList, 'ability_join_item_id', 2);
//
//            }
//            if(!$isNeedHandle && !empty($joinItemStandardKeyDataList)) $isNeedHandle = true;
//
//            // 获得项目标准
//            if(in_array('projectStandards', $handleKeyArr)){
//                $abilityIdArr = array_values(array_filter(array_column($data_list,'ability_id')));// 资源id数组，并去掉值为0的
////                $projectStandardsList = [];
////                // 查询条件
////                if(!empty($abilityIdArr)){
////                    // 获得企业资质证书
////                    $projectStandardsQueryParams = [
////                        'where' => [
////                            // ['type_id', 5],
////                            //                //['mobile', $keyword],
////                        ],
////                        //            'select' => [
////                        //                'id','company_id','position_name','sort_num'
////                        //                //,'operate_staff_id','operate_staff_id_history'
////                        //                ,'created_at'
////                        //            ],
////                        // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
////                    ];
////                    Tool::appendParamQuery($projectStandardsQueryParams, $abilityIdArr, 'ability_id', [0, '0', ''], ',', false);
////                    $projectStandardsList = CTAPIProjectStandardsBusiness::getBaseListData($request, $controller, '', $projectStandardsQueryParams,[], 1,  1)['data_list'] ?? [];
////                }
//
//                $extParams = [];
//                $projectStandardsList =  CTAPIProjectStandardsBusiness::getFVFormatList( $request,  $controller, 1, 1,  ['ability_id' => $abilityIdArr], false,[], $extParams);
//
//                if(!empty($projectStandardsList)) $projectStandardsArr = Tool::arrUnderReset($projectStandardsList, 'ability_id', 2);
//                if(!$isNeedHandle && !empty($projectStandardsArr)) $isNeedHandle = true;
//            }
//        }
//
//        //        }
//        // 改为不返回，好让数据下面没有数据时，有一个空对象，方便前端或其它应用处理数据
//        // if(!$isNeedHandle){// 不处理，直接返回 // if(!$isMulti) $data_list = $data_list[0] ?? [];
//        //    return true;
//        // }
//
//        foreach($data_list as $k => $v){
//            //            // 公司名称
//            //            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            //            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//
//
//            // 获得能力验证名称
//            if(in_array('ability', $handleKeyArr)){
//                $data_list[$k]['ability_info'] = $abilityDataList[$v['ability_id']] ?? '';
//                $data_list[$k]['ability_name'] = $abilityKVList[$v['ability_id']] ?? '';
//            }
//
//            // 企业信息
//            if(in_array('company', $handleKeyArr)){
//                $data_list[$k]['company_info'] = $companyDataList[$v['staff_id']] ?? '';
//                $data_list[$k]['company_name'] = $companyKVList[$v['staff_id']] ?? '';
//            }
//            // 获得报名的标准方法
//            if(in_array('joinItemsStandards', $handleKeyArr)){
//                $tem_arr = $joinItemStandardKeyDataList[$v['id']] ?? [];
//                // $data_list[$k]['join_item_standards'] = $tem_arr;
//                $data_list[$k]['join_item_standards'] = Tool::arrUnderReset($tem_arr, 'project_standard_id', 1);
//                $data_list[$k]['join_item_standard_ids'] = array_values(array_column($tem_arr,'project_standard_id'));// 资源id数组，并去掉值为0的
//            }
//
//            // 获得项目标准
//            if(in_array('projectStandards', $handleKeyArr)){
//                //  [{'id': 0, 'tag_name': '标签名称'},..]
//                $configArr = [];
//                $temArr = $projectStandardsArr[$v['ability_id']] ?? [];
//                foreach($temArr as $info){
//                    array_push($configArr, [
//                        'id' => $info['id'],
//                        'tag_name' => $info['name'],
//                    ]);
//                }
//                $data_list[$k]['project_standards'] = $configArr;
//                $data_list[$k]['project_standards_text'] = implode('<br/>', Tool::getArrFields($temArr, 'name'));
//            }
//
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

        $admin_type = CommonRequest::getInt($request, 'admin_type');
        if($admin_type > 0 )  array_push($queryParams['where'], ['admin_type', '=', $admin_type]);

        $company_id = CommonRequest::getInt($request, 'company_id');
        if($company_id > 0 )  array_push($queryParams['where'], ['staff_id', '=', $company_id]);

        $staff_id = CommonRequest::getInt($request, 'staff_id');
        if($staff_id > 0 )  array_push($queryParams['where'], ['staff_id', '=', $staff_id]);

        $ability_join_id = CommonRequest::getInt($request, 'ability_join_id');
        if($ability_join_id > 0 )  array_push($queryParams['where'], ['ability_join_id', '=', $ability_join_id]);

        $ability_id = CommonRequest::getInt($request, 'ability_id');
        if($ability_id > 0 )  array_push($queryParams['where'], ['ability_id', '=', $ability_id]);

        $status = CommonRequest::getInt($request, 'status');
        if($status > 0 )  array_push($queryParams['where'], ['status', '=', $status]);

//        $status = CommonRequest::get($request, 'status');
//        if(strlen($status) > 0 && $status != 0)  Tool::appendParamQuery($queryParams, $status, 'status', [0, '0', ''], ',', false);


        $retry_no = CommonRequest::get($request, 'retry_no');
        if(is_numeric($retry_no) && $retry_no >= 0 )  array_push($queryParams['where'], ['retry_no', '=', $retry_no]);

        $result_status = CommonRequest::getInt($request, 'result_status');
        if($result_status > 0 )  array_push($queryParams['where'], ['result_status', '=', $result_status]);

        $is_sample = CommonRequest::getInt($request, 'is_sample');
        if($is_sample > 0 )  array_push($queryParams['where'], ['is_sample', '=', $is_sample]);

        $submit_status = CommonRequest::getInt($request, 'submit_status');
        if($submit_status > 0 )  array_push($queryParams['where'], ['submit_status', '=', $submit_status]);

        $judge_status = CommonRequest::getInt($request, 'judge_status');
        if($judge_status > 0 )  array_push($queryParams['where'], ['judge_status', '=', $judge_status]);

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
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
            // 获得企业名称
            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['admin_type' => 'admin_type', 'staff_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_info'),
                    static::getUboundRelationExtendParams($extendParams, 'company_info')),
                static::getRelationSqlParams(['where' => [['admin_type', 2]]], $extendParams, 'company_info'), '', []),
            // 获得企业名称
            'company_info_all' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['admin_type' => 'admin_type', 'staff_id' => 'id']
                , 1, 32
                ,'','', [
                    // 企业的城市信息 1：1
                    'city_info' => CTAPICitysBusiness::getTableRelationConfigInfo($request, $controller
                        , ['city_id' => 'id']
                        , 1, 2
                        ,'','', [], [], '', []),
                ], ['where' => [['admin_type', 2]]], '', []),
            // 获得项目
            'ability_info' => CTAPIAbilitysBusiness::getTableRelationConfigInfo($request, $controller
                , ['ability_id' => 'id']
                , 1, 1 | 2
                ,'','',
                CTAPIAbilitysBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'ability_info'),
                    static::getUboundRelationExtendParams($extendParams, 'ability_info')),
                static::getRelationSqlParams([], $extendParams, 'ability_info'), '', []),
            // 下一级关系 每一项的取样结果  1:1
            'join_item_reslut_info' => CTAPIAbilityJoinItemsResultsBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'ability_join_item_id', 'retry_no' => 'retry_no']
                , 1, 1// 项目名称  测试4
                ,'',''
                ,CTAPIAbilityJoinItemsResultsBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'join_item_reslut_info'),
                    static::getUboundRelationExtendParams($extendParams, 'join_item_reslut_info')),
                static::getRelationSqlParams([], $extendParams, 'join_item_reslut_info'), '', []),
            // 需要验证数据项 1:n
            'project_submit_items_list' => CTAPIProjectSubmitItemsBusiness::getTableRelationConfigInfo($request, $controller
                , ['ability_id' => 'ability_id']
                , 2, 1
                ,'','',
                CTAPIProjectSubmitItemsBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'project_submit_items_list'),
                    static::getUboundRelationExtendParams($extendParams, 'project_submit_items_list')),
                static::getRelationSqlParams([], $extendParams, 'project_submit_items_list'), '', []),

            // 下一级关系 能力验证单次结果  1:1 -- 获得当前正在操作的要上传数据的结果 -- 上传数据用
            'join_item_reslut_info_updata' => CTAPIAbilityJoinItemsResultsBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'ability_join_item_id', 'retry_no' => 'retry_no']
                , 1, 1// 项目名称  测试4
                ,'',''
                ,[
                    // 所用仪器 1：n
                    'results_instrument_list' => CTAPIAbilityJoinItemsResultsInstrumentBusiness::getTableRelationConfigInfo($request, $controller
                        , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'id' => 'result_id']
                        , 2, 1
                        ,'','', [], [], '', []),
                    // 检测标准物质 1：n
                    'results_standard_list' => CTAPIAbilityJoinItemsResultsStandardBusiness::getTableRelationConfigInfo($request, $controller
                        , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'id' => 'result_id']
                        , 2, 1
                        ,'','', [], [], '', []),
                    // 检测方法依据 1：n
                    'results_method_list' => CTAPIAbilityJoinItemsResultsMethodBusiness::getTableRelationConfigInfo($request, $controller
                        , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'id' => 'result_id']
                        , 2, 1
                        ,'','', [], [], '', []),
                    // 登记样品 1：n
                    'items_samples_list' => CTAPIAbilityJoinItemsSamplesBusiness::getTableRelationConfigInfo($request, $controller
                        , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'id' => 'result_id']
                        , 2, 1
                        ,'','', [
                            // 提交的登记样品结果 1：n
                            'sample_result_list' => CTAPIAbilityJoinItemsSampleResultBusiness::getTableRelationConfigInfo($request, $controller
                                , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'result_id' => 'result_id', 'id' => 'sample_id']
                                , 2, 4
                                ,'','', [], [], '', []),
                        ], [], '', []),
                    // 企业上传的图片资料信息
                    'resource_list' => CTAPIResourceBusiness::getTableRelationConfigInfo($request, $controller
                        , ['id' => 'column_id']
                        , 2, 0
                        ,'','', [], ['where' => [['column_type', 4]]], ''
                        , ['extendConfig' => ['listHandleKeyArr' => ['format_resource'], 'infoHandleKeyArr' => ['resource_list']]]),
                ], [], '', []),
            // 下一级关系 能力验证单次结果  1:1 -- 获得当前正在操作的要上传数据的结果 -- 上传数据--保存用
            'join_item_reslut_info_save' => CTAPIAbilityJoinItemsResultsBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'ability_join_item_id', 'retry_no' => 'retry_no']
                , 1, 1// 项目名称  测试4
                ,'',''
                ,[
                    // 登记样品 1：n
                    'items_samples_list' => CTAPIAbilityJoinItemsSamplesBusiness::getTableRelationConfigInfo($request, $controller
                        , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'id' => 'result_id']
                        , 2, 1
                        ,'','', [
                            // 提交的登记样品结果 1：n
                            'sample_result_list' => CTAPIAbilityJoinItemsSampleResultBusiness::getTableRelationConfigInfo($request, $controller
                                , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'result_id' => 'result_id', 'id' => 'sample_id']
                                , 2, 4
                                ,'','', [], [], '', []),

                        ], [], '', []),
                ], [], '', []),
            // 下一级关系的  能力验证报名项-项目标准 1:n
            'join_item_standards' => CTAPIAbilityJoinItemsStandardsBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'ability_join_item_id']
                , 2
                ,  2 | 8
                ,'',''
                ,[
                    // 获得报名项选的方法对应的名称 1:1
                    'project_standard_info' => CTAPIProjectStandardsBusiness::getTableRelationConfigInfo($request, $controller
                        , ['project_standard_id' => 'id']
                        , 1, 2
                        ,'',''
                        ,[], [], '', []),
                ], [], '', ['extendConfig' => ['listHandleKeyArr' => ['mergeZeroName']]]),
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

//        if(($return_num & 2) == 2){// 给上一级返回下标名称 ability_name => '项目名称'
//            $one_field = ['key' => 'ability_name', 'return_type' => 2, 'ubound_name' => 'ability_name', 'split' => '、'];// 获得名称
//            if(!isset($return_data['one_field'])) $return_data['one_field'] = [];
//            array_push($return_data['one_field'], $one_field);
//        }

        if(($return_num & 2) == 2){// 企业已报名的项目id 数组  ability_id_joined => [1,25]
            $one_field = ['key' => 'ability_id', 'return_type' => 1, 'ubound_name' => 'ability_id_joined', 'split' => ','];
            if(!isset($return_data['one_field'])) $return_data['one_field'] = [];
            array_push($return_data['one_field'], $one_field);
        }

        if(($return_num & 4) == 4){// 标准id 数组  join_item_standard_ids => [0,25]
            $many_fields = [ 'ubound_name' => 'ability_join_items', 'fields_arr'=> Tool::arrEqualKeyVal(['id', 'admin_type' , 'staff_id', 'ability_id', 'ability_join_items_standards', 'ability_join_items_results'],true),'reset_ubound' => 0];
            if(!isset($return_data['many_fields'])) $return_data['many_fields'] = [];
            array_push($return_data['many_fields'], $many_fields);
        }
        return $return_data;
    }
    // ****表关系***需要重写的方法**********结束***********************************

    /**
     * 对单条数据关系进行格式化--具体的可以重写
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $info  单条数据  --- 一维数组
     * @param array $temDataList  关系数据  --- 一维或二维数组 -- 主要操作这个数据到  $info 的特别业务数据
     *                              如果是二维数组：下标已经是他们关系字段的值，多个则用_分隔好的
     * @param array $infoHandleKeyArr 其它扩展参数，// 一维数组，单条 数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     * @return array  新增的字段 一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function infoRelationFormatExtend(Request $request, Controller $controller, &$info, &$temDataList, $infoHandleKeyArr, &$returnFields){
        // if(empty($info)) return $returnFields;
        // $returnFields[$tem_ubound_old] = $tem_ubound_old;

        // 判断企业是否已经报名
        if(in_array('judgeJoined', $infoHandleKeyArr)){
            $is_joined = 0;
            $is_joined_text = '未报名';
            if(!empty($temDataList)){
                $is_joined = 1;
                $is_joined_text = '报名成功';
            }
            $info['is_joined'] = $is_joined;
            $info['is_joined_text'] = $is_joined_text;
        }

        return $returnFields;
    }

    /**
     * 获得列表数据时，对查询结果进行导出操作--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function exportListData(Request $request, Controller $controller, &$data_list, $notLog = 0){
            $headArr = ['ability_code'=>'能力验证编号', 'company_name'=>'公司名称', 'city_name'=>'所在城市', 'addr'=>'公司地址'
                , 'ability_name'=>'报名项目', 'contacts'=>'报名联系人', 'mobile'=>'报名联系人手机', 'tel'=>'报名联系人电话'];
        $ability_id = CommonRequest::get($request,'ability_id');
        $info = CTAPIAbilitysBusiness::getInfoData($request, $controller, $ability_id);
        $ability_name = $info['ability_name'] ?? '';

        ImportExport::export('',$ability_name . '报名企业列表' . date('YmdHis'),$data_list,1, $headArr, 0, ['sheet_title' => 'sheet名称']);
    }
}
