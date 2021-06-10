<?php
//能力验证报名项-项目标准
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

class CTAPIAbilityJoinItemsStandardsBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\AbilityJoinItemsStandardsAPI';
    public static $table_name = 'ability_join_items_standards';// 表名称
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
//            'company_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
//                , ['admin_type' => 'admin_type', 'staff_id' => 'id']
//                , 1, 2
//                ,'','',
//                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
//                    static::getUboundRelation($relationArr, 'company_info'),
//                    static::getUboundRelationExtendParams($extendParams, 'company_info')),
//                static::getRelationSqlParams(['where' => [['admin_type', 2]]], $extendParams, 'company_info'), '', []),
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
            // $return_data['old_data'] = ['ubound_operate' => 1, 'ubound_name' => '', 'fields_arr' => [], 'ubound_keys' => [], 'ubound_type' =>1];
            $return_data['old_data'] = ['ubound_operate' => 1, 'ubound_name' => '','fields_arr' => [], 'ubound_keys' => ['project_standard_id'], 'ubound_type' =>1];
        }

        if(($return_num & 2) == 2){// 标准id 数组  join_item_standard_ids => [0,25]
            $one_field = ['key' => 'project_standard_id', 'return_type' => 1, 'ubound_name' => 'join_item_standard_ids', 'split' => ','];
            if(!isset($return_data['one_field'])) $return_data['one_field'] = [];
            array_push($return_data['one_field'], $one_field);
        }

        if(($return_num & 4) == 4){// 二维 数组  ability_join_items_standards => [id => 1, 'project_standard_id' => 2, 'project_standard_name' => '']
            $many_fields = [ 'ubound_name' => 'ability_join_items_standards', 'fields_arr'=> Tool::arrEqualKeyVal(['id', 'project_standard_id' , 'project_standard_name'],true),'reset_ubound' => 0];
            if(!isset($return_data['many_fields'])) $return_data['many_fields'] = [];
            array_push($return_data['many_fields'], $many_fields);
        }

        if(($return_num & 8) == 8){// 给上一级返回 id 、 tag_name 的二维数组 . 如： project_standards = [[ 'id' => 26,  'tag_name' => '方法2'], ... ]
            $many_fields = [ 'ubound_name' => 'join_standards_tag', 'fields_arr'=> ['id' => 'id', 'tag_name' => 'project_standard_name'],'reset_ubound' => 2];
            if(!isset($return_data['many_fields'])) $return_data['many_fields'] = [];
            array_push($return_data['many_fields'], $many_fields);
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
//        $isNeedHandle = false;// 是否真的需要遍历处理数据 false:不需要：true:需要 ；只要有一个需要处理就标记
//
//        //        if(!empty($data_list) ){
//        // 项目标准
//        $standardDataList = [];// 项目标准id 为下标 二维数组
//        $standardKVList = [];// 项目标准id => 企业名称 的键值对
//        if(in_array('project_standards', $handleKeyArr)){
//            $standardIdsArr = array_values(array_filter(array_column($data_list,'project_standard_id')));// 资源id数组，并去掉值为0的
//            // 查询条件
////            $standardList = [];
////            if(!empty($standardIdsArr)){
////                // 获得企业信息
////                $standardQueryParams = [
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
////                Tool::appendParamQuery($standardQueryParams, $standardIdsArr, 'id', [0, '0', ''], ',', false);
////                $standardList = CTAPIProjectStandardsBusiness::getBaseListData($request, $controller, '', $standardQueryParams,[], 1,  1)['data_list'] ?? [];
////            }
//
//            $extParams = [];
//            $standardList =  CTAPIProjectStandardsBusiness::getFVFormatList( $request,  $controller, 1, 1,  ['id' => $standardIdsArr], false,[], $extParams);
//            if(!empty($standardList)){
//                $standardDataList = Tool::arrUnderReset($standardList, 'id', 1);
//                $standardKVList = Tool::formatArrKeyVal($standardList, 'id', 'name');
//            }
//            if(!$isNeedHandle && !empty($standardDataList)) $isNeedHandle = true;
//        }
//
//
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
//            // 项目标准
//            if(in_array('project_standards', $handleKeyArr)){
//                $data_list[$k]['standard_info'] = $standardDataList[$v['project_standard_id']] ?? '';
//                if($v['project_standard_id'] != 0){
//                    // $data_list[$k]['standard_name'] = $standardKVList[$v['project_standard_id']] ?? '';
//                    $data_list[$k]['project_standard_name'] = $standardKVList[$v['project_standard_id']] ?? '';
//                }
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


        $ability_join_item_id = CommonRequest::getInt($request, 'ability_join_item_id');
        if($ability_join_item_id > 0 )  array_push($queryParams['where'], ['ability_join_item_id', '=', $ability_join_item_id]);

        $project_standard_id = CommonRequest::getInt($request, 'project_standard_id');
        if($project_standard_id > 0 )  array_push($queryParams['where'], ['project_standard_id', '=', $project_standard_id]);

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

        if(in_array('mergeZeroName', $handleKeyArr)){
             foreach($data_list as $k => $v){
                 $project_standard_id = $v['project_standard_id'];
                 if($project_standard_id > 0 && isset($v['project_standard_id_name']) ){
                     $data_list[$k]['project_standard_name'] = $v['project_standard_id_name'];
                 }
             }
        }

        // 重写结束
        return $returnFields;
    }

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

//        if(in_array('mergeZeroName', $infoHandleKeyArr)){
//            // pr($temDataList);
//        }

        return $returnFields;
    }

}
