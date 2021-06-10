<?php
//能力验证单次结果
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

class CTAPIAbilityJoinItemsResultsBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\AbilityJoinItemsResultsAPI';
    public static $table_name = 'ability_join_items_results';// 表名称
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
            // 获得企业名称
            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['admin_type' => 'admin_type', 'staff_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_info'),
                    static::getUboundRelationExtendParams($extendParams, 'company_info')),
                static::getRelationSqlParams(['where' => [['admin_type', 2]]], $extendParams, 'company_info'), '', []),
            // 获得企业信息
            'company_info_data' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['admin_type' => 'admin_type', 'staff_id' => 'id']
                , 1
                , 1// 企业名称
                ,'',''
                ,
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_info_data'),
                    static::getUboundRelationExtendParams($extendParams, 'company_info_data')),
                static::getRelationSqlParams(['where' => [['admin_type', 2]]], $extendParams, 'company_info_data'), '', []),
            // 获得项目
            'ability_info' => CTAPIAbilitysBusiness::getTableRelationConfigInfo($request, $controller
                , ['ability_id' => 'id']
                , 1, 1 | 2
                ,'','',
                CTAPIAbilitysBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'ability_info'),
                    static::getUboundRelationExtendParams($extendParams, 'ability_info')),
                static::getRelationSqlParams([], $extendParams, 'ability_info'), '', []),
            // 下一级关系 报名项所属的项目 -- 的名称 1:1
            'ability_info_name' => CTAPIAbilitysBusiness::getTableRelationConfigInfo($request, $controller
                , ['ability_id' => 'id']
                , 1, 2// 项目名称  测试4
                ,'',''
                ,
                CTAPIAbilitysBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'ability_info_name'),
                    static::getUboundRelationExtendParams($extendParams, 'ability_info_name')),
                static::getRelationSqlParams([], $extendParams, 'ability_info_name'), '', []),
            // 需要验证数据项 1:n
            'project_submit_items_list' => CTAPIProjectSubmitItemsBusiness::getTableRelationConfigInfo($request, $controller
                , ['ability_id' => 'ability_id']
                , 2, 8
                ,'','',
                CTAPIProjectSubmitItemsBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'project_submit_items_list'),
                    static::getUboundRelationExtendParams($extendParams, 'project_submit_items_list')),
                static::getRelationSqlParams([], $extendParams, 'project_submit_items_list'), '', []),
            // 所用仪器 1：n
            'results_instrument_list' => CTAPIAbilityJoinItemsResultsInstrumentBusiness::getTableRelationConfigInfo($request, $controller
                , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'id' => 'result_id']
                , 2, 1
                ,'','',
                CTAPIAbilityJoinItemsResultsInstrumentBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'results_instrument_list'),
                    static::getUboundRelationExtendParams($extendParams, 'results_instrument_list')),
                static::getRelationSqlParams([], $extendParams, 'results_instrument_list'), '', []),
            // 检测标准物质 1：n
            'results_standard_list' => CTAPIAbilityJoinItemsResultsStandardBusiness::getTableRelationConfigInfo($request, $controller
                , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'id' => 'result_id']
                , 2, 1
                ,'','',
                CTAPIAbilityJoinItemsResultsStandardBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'results_standard_list'),
                    static::getUboundRelationExtendParams($extendParams, 'results_standard_list')),
                static::getRelationSqlParams([], $extendParams, 'results_standard_list'), '', []),
            // 检测方法依据 1：n
            'results_method_list' => CTAPIAbilityJoinItemsResultsMethodBusiness::getTableRelationConfigInfo($request, $controller
                , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'id' => 'result_id']
                , 2, 1
                ,'','',
                CTAPIAbilityJoinItemsResultsMethodBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'results_method_list'),
                    static::getUboundRelationExtendParams($extendParams, 'results_method_list')),
                static::getRelationSqlParams([], $extendParams, 'results_method_list'), '', []),
            // 下一级关系 每一项的取样具体数据  1:n
            'join_items_samples_list' => CTAPIAbilityJoinItemsSamplesBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'result_id']// , 'ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no'
                , 2, 1// 项目名称  测试4
                ,'',''
                ,CTAPIAbilityJoinItemsSamplesBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'join_items_samples_list'),
                    static::getUboundRelationExtendParams($extendParams, 'join_items_samples_list')),
                static::getRelationSqlParams(['orderBy' => ['serial_number'=>'asc', 'id'=>'desc']], $extendParams, 'join_items_samples_list'), '', []),
            // 登记样品 1：n
            'items_samples_list' => CTAPIAbilityJoinItemsSamplesBusiness::getTableRelationConfigInfo($request, $controller
                , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'id' => 'result_id']
                , 2, 1
                ,'','', [
                    // 提交的登记样品结果 1：n
                    'sample_result_list' => CTAPIAbilityJoinItemsSampleResultBusiness::getTableRelationConfigInfo($request, $controller
                        , ['ability_join_item_id' => 'ability_join_item_id', 'retry_no' => 'retry_no', 'result_id' => 'result_id', 'id' => 'sample_id']
                        , 2, 4
                        ,'','', [
                            // 样品结果对应的检验证数据名称 1：1
                            'project_submit_items' => CTAPIProjectSubmitItemsBusiness::getTableRelationConfigInfo($request, $controller
                                , ['submit_item_id' => 'id']
                                , 1, 4
                                ,'','', [], [], '', []),
                        ], [], '', []),
                ], [], '', []),
            // 企业上传的图片资料信息
            'resource_list' => CTAPIResourceBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'column_id']
                , 2, 0
                ,'','',
                CTAPIResourceBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'resource_list'),
                    static::getUboundRelationExtendParams($extendParams, 'resource_list')),
                static::getRelationSqlParams(['where' => [['column_type', 4]]], $extendParams, 'resource_list'), ''
                , ['extendConfig' => ['listHandleKeyArr' => ['format_resource'], 'infoHandleKeyArr' => ['resource_list']]]),
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

        if(($return_num & 4) == 4) {// 返回源数据--特别的可以参考这个配置
            $return_data['old_data'] = ['ubound_operate' => 1, 'ubound_name' => '', 'fields_arr' => [], 'ubound_keys' => ['ability_join_item_id', 'retry_no'], 'ubound_type' =>1];
        }

        if(($return_num & 8) == 8){// 二维 数组  ability_join_items_results => [id => 1, 'project_standard_id' => 2, 'project_standard_name' => '']
            $many_fields = [ 'ubound_name' => 'ability_join_items_results', 'fields_arr'=> Tool::arrEqualKeyVal(['id', 'ability_join_item_id' , 'retry_no', 'contacts', 'mobile', 'tel', 'ability_id', 'join_time'],true),'reset_ubound' => 0];
            if(!isset($return_data['many_fields'])) $return_data['many_fields'] = [];
            array_push($return_data['many_fields'], $many_fields);
        }
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

        $ability_join_item_id = CommonRequest::getInt($request, 'ability_join_item_id');
        if($ability_join_item_id > 0 )  array_push($queryParams['where'], ['ability_join_item_id', '=', $ability_join_item_id]);

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
        $headArr = ['ability_id'=>'检测项目ID', 'ability_name'=>'检测项目', 'retry_no_text'=>'测试次序', 'ability_code'=>'能力验证代码'];
        // 拼接样品检验结果下标
        $ability_name = '';
        $exportDataList = [];
        foreach($data_list as $k => $v){
            if($ability_name == '' ) $ability_name = ($v['ability_name'] ?? '') . ($v['retry_no_text'] ?? '');
            // 领取的样品列表[默认三个样品列]  样品编号   样品[数据项1]结果 样品[数据项2]结果
            $items_samples_list = $v['items_samples_list'] ?? [];
            if(!is_array($items_samples_list) || empty($items_samples_list))  $items_samples_list = [['id' => 0, 'sample_one' => '未领样品']];
            if(isset($v['items_samples_list'])) unset($v['items_samples_list']);
            // 需要结果的数据项
            $submit_items = $v['submit_items'] ?? [];// 格式 [[ 'id' => 26,  'tag_name' => '方法2'], ... ]
            if(isset($v['submit_items'])) unset($v['submit_items']);
            foreach($items_samples_list as $tem_sample_info){
                $exportInfo = $v;
                $sample_id = $tem_sample_info['id'];
                $sample_one = $tem_sample_info['sample_one'];
                $exportInfo['sample_one'] = $sample_one;
                $headArr['sample_one'] = '样品编号';
                foreach($submit_items as $submit_k => $submit_info){
                    $tag_id = $submit_info['id'];
                    $tag_name = $submit_info['tag_name'];
                    $result_info = $tem_sample_info['sample_result_list'][$sample_id . '_' . $tag_id] ?? [];

                    $headArr['sample_one_' . $submit_k] = '验证数据项【' . $tag_name .'】';
                    $exportInfo['sample_one_' . $submit_k] = $result_info['sample_result'] ?? ''; // 结果

                }
                array_push($exportDataList, $exportInfo);
            }
        }
        $headArr['result_status_text'] = '验证结果';
        ImportExport::export('',$ability_name . '检测数据' . date('YmdHis'),$exportDataList,1, $headArr, 0, ['sheet_title' => $ability_name . '检测数据']);
    }


    /**
     * 根据id判断记录结果
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array  $saveData [ 'result_status' =>  2满意、4有问题、8不满意   16满意【补测满意】 ]
     * @param int $id id
     * @param boolean $modifAddOprate 修改时是否加操作人，true:加;false:不加[默认]
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 记录id
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeResultById(Request $request, Controller $controller, $saveData, &$id, $modifAddOprate = false, $notLog = 0){
        // $tableLangConfig = static::getLangModelsDBConfig('',  1);
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
//        if(isset($saveData['goods_name']) && empty($saveData['goods_name'])  ){
//            throws('商品名称不能为空！');
//        }

        // 调用新加或修改接口
        $apiParams = [
            'saveData' => $saveData,
            'company_id' => $company_id,
            'id' => $id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => ($modifAddOprate == true) ? 1 : 0 ,// 0,
        ];
        $methodName = 'judgeResultById';
        static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);

        return $id;
    }

}
