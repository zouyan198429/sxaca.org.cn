<?php

namespace App\Business\Controller\API;

use App\Business\Controller\API\QualityControl\CTAPISmsModuleBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsModuleParamsBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsModuleParamsCommonBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Notifications\SMSSendNotification;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Request\CommonRequest;
use App\Services\Response\Data\CommonAPIFromBusiness;
use App\Services\SMS\SendSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Notification;

class BasicCTAPIBusiness extends APIOperate
{
    public static $database_model_dir_name = '';// 对应的数据库模型目录名称
    public static $model_name = '';// 中间层 App\Business\API 下面的表名称 API\RunBuy\CountSenderRegAPI
    public static $table_name = '';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

    /**
     * $model_name 转换为其它格式  API\QualityControl\CTAPIStaffBusiness =》 QualityControl\{CTAPI}Staff
     *  如调用：CTAPIStaffBusiness::modelNameFormat($request, $this);
     * @param string $preStr 表名前面加的关键字
     * @return string
     * @author zouyan(305463219@qq.com)
     */
    public static function modelNameFormat(Request $request, Controller $controller, $preStr = 'CTAPI'){
        // API\QualityControl\StaffAPI
        // API\QualityControl\CTAPIStaffBusiness
        $model_name = static::$model_name;
        $needArr = [];
        $temArr = explode('\\', $model_name);
        $arrCount = count($temArr);
        foreach($temArr as $k => $v){
            if($k <= 0 ) continue;
            // 最后一个
            if($k == $arrCount -1){
                // 去掉最后的API
                if(substr($v,-3) == 'API'){
                    $v = $preStr . substr($v,0,-3);
                }
            }
            array_push($needArr, $v);
        }
        $needStr = implode('\\', $needArr);// QualityControl\CTAPIStaff
        return $needStr;
    }

    /**
     * 修改 Request的值
     *
     * @param array $params 需要修改的键值数组 ['foo' => 'bar', ....]
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function mergeRequest(Request $request, Controller $controller, $params = [])
    {
        // 合并输入，如果有相同的key，用户输入的值会被替换掉，否则追加到 input
         $request->merge($params);

        // 替换所有输入
        // $request->replace($params);
    }

    /**
     * 删除 Request的值
     *
     * @param array $params 需要修改的键值数组 ['foo', 'bar', ....]
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function removeRequest(Request $request, Controller $controller, $params = [])
    {
        foreach($params as $key){
            unset($request[$key]);
        }
    }


    /**
     * 生成单号
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int  $orderType 要保存或修改的数组 1 订单号 2 退款订单 3 支付跑腿费  4 追加跑腿费 5 冲值  6 提现 7 压金或保证金 8 邀请码  9 API接口日志
     * @return  int
     * @author zouyan(305463219@qq.com)
     */
    public static function createSn(Request $request, Controller $controller, $orderType = 1){
        $company_id = $controller->company_id;
        $user_id = $controller->user_id ?? '';
        $namespace = '';
        $prefix = $orderType;
        $midFix = '';
        $backfix = '';
        $length = 6;
        $expireNums = [];
        $needNum = 0;
        $dataFormat = '';
        switch ($orderType)
        {
            case 1:// 订单
                $userIdBack = str_pad(substr($user_id, -2), 2, '0', STR_PAD_LEFT);// 用户id后两位
                $midFix = $userIdBack;
                $namespace = 'order' . $userIdBack;
                $length = 4;
                $needNum = 1 + 2 + 8;
//                $expireNums = [
//                  [1000,1100,365 * 24 * 60 * 60]  // 缓存的秒数365 * 24 * 60 * 60
//                ];
                break;
            case 2:// 2 退款订单
            case 3:// 3 支付跑腿费
            case 4:// 4 追加跑腿费
            case 5:// 5 冲值
            case 6:// 6 提现
            case 7:// 7 压金或保证金
                $userIdBack = str_pad(substr($user_id, -2), 2, '0', STR_PAD_LEFT);// 用户id后两位
                $midFix = $userIdBack;
                $namespace = 'orderRefund' . $userIdBack;
                $length = 2;// 总共一秒一万
                $needNum = 4 + 8;
                $dataFormat = 'ymdHis';

//                $expireNums = [
//                  [1000,1100,365 * 24 * 60 * 60]  // 缓存的秒数365 * 24 * 60 * 60
//                ];
                break;
            case 8:// 8 邀请码
                $prefix = '';
                $midFix = '';
                $length = 2;// 总共一秒一万
                $needNum = 1 + 2 + 8;
                $dataFormat = 's';
                break;
            case 9:// 9 API接口日志
                // $prefix = '';
                $userIdBack = str_pad(substr($user_id, -2), 2, '0', STR_PAD_LEFT);// 用户id后两位
                $midFix = $userIdBack;
                $namespace = 'API' . $userIdBack;
                $length = 6;
                $needNum = 1 + 2 + 8;
                $dataFormat = 's';
                break;

            default:
        }
        $fixParams = [
            'prefix' => $prefix,// 前缀[1-2位] 可填;可写业务编号等
            'midFix' => $midFix,// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
            'backfix' => $backfix,// 后缀[1-2位] 可填;备用
            'expireNums' => $expireNums,// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
            'needNum' => $needNum,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
            'dataFormat' => $dataFormat, // needNum 值为 4时的日期格式  'YmdHis'
        ];
        return Tool::makeOrder($namespace , $fixParams, $length);
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~通用方法~~~~~~~~如果有特殊的不同，可以自己重写相关方法~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    // ~~~~~~~~~~~~~~~~~列表开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


    /**
     * 根据字段=》值数组；获得数据[格式化后的数据]
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $operate_type 操作为型  1 所有[默认] all 二维 2 ：指定返回数量 limit_num 1：一维 ，>1 二维 4：只返回一条 one_num 一维 ， 8：获得数量值
     * @param int $page_size 2时返回的数量
     * @param array $fieldValParams
     *  $fieldValParams = [
     *      'ability_join_id' => [// 格式一
     *          'vals' => "字段值[可以是字符'多个逗号分隔'或一维数组] ",// -- 此种格式，必须指定此下标【值下标】
     *          'excludeVals' => "过滤掉的值 默认[''];// [0, '0', '']",
     *          'valsSeparator' => ',' 如果是多值字符串，多个值的分隔符;默认逗号 ,
     *          'hasInIsMerge=>  false 如果In条件有值时  true:合并；false:用新值--覆盖 --默认
     *      ],// 格式二
     *      'id' =>  "字段值[可以是字符'多个逗号分隔'或一维数组]"
     *   ];
     * @param boolean $fieldEmptyQuery 如果参数字段值都为空时，是否还查询数据 true:查询 ；false:不查
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *        'useQueryParams' => '是否用来拼接查询条件，true:用[默认];false：不用'
     *        'sqlParams' => [// 其它sql条件[拼接/覆盖式],下面是常用的，其它的也可以
     *            // '如果有值，则替换where' --拼接
     *           'where' => [// -- 可填 如 默认条件 'type_id' => 5  'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']
     *               ['type_id', 5],
     *           ],
     *           'select' => '如果有值，则替换select'--覆盖
     *           'orderBy' => '如果有值，则替换orderBy'--覆盖
     *           'whereIn' => '如果有值，则替换whereIn' --拼接
     *           'whereNotIn' => '如果有值，则替换whereNotIn' --拼接
     *           'whereBetween' => '如果有值，则替换whereBetween' --拼接
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween' --拼接
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *       'relationFormatConfigs'=> [],// 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
     *       'listHandleKeyArr'=> [],// 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
     *       'infoHandleKeyArr'=> [],// 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ],
     *       // 可以有下标--翻页相关的配置，没有则可以用 request中的---这里的配置优先
     *       // 是参数  $operate_type = 2:专用参数
     *      'page_config' => [
     *         'page' => $page,// 当前页,如果不正确默认第一页；
     *         'pagesize' => $pagesize,// 每页显示数量,取值1 -- 100 条之间,默认15条---注意：这个参数不用，以前面的参数 $page_size 为准
     *         'total' => $total,// 总记录数,优化方案：传0传重新获取总数，如果传了，则不会再获取，而是用传的，减软数据库压力;=-5:只统计条件记录数量，不返回数据
     *         // 追加两个参数 - 需要时才用
     *         // 链接地址模板 http://www.***.com/list/{page} 主要是这个page 替换为具体的页数
     *         'url_model' => $url_model,
     *         // 链接地址模板 $url_model 中的页数标签 默认 {page}
     *         'page_tag' => $page_tag,
     *      ]
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据 一维或二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getFVFormatList(Request $request, Controller $controller, $operate_type = 1, $page_size = 1, $fieldValParams = [], $fieldEmptyQuery = false, $relations = '', $extParams = [], $notLog = 0)
    {
        $dataArr = [];
        // $isEmpeyVals = true;//  查询字段值是否都为空; true:都为空，false:有值
        $hasValidVal = false;// 是否有拼接值 true: 有；false:没有
        // 获得信息
        $queryParams = [
            'where' => [
                // ['type_id', 5],
                //                //['mobile', $keyword],
            ],
            //            'select' => [
            //                'id','company_id','position_name','sort_num'
            //                //,'operate_staff_id','operate_staff_id_history'
            //                ,'created_at'
            //            ],
            // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
        ];
        foreach($fieldValParams as $field => $valConfig){
            $excludeVals = [''];// [0, '0', ''];
            $fieldVals = [];
            // 是数组
            if(is_array($valConfig) && isset($valConfig['vals'])){
                if(isset($valConfig['excludeVals']) && is_array($valConfig['excludeVals'])){
                    $excludeVals = $valConfig['excludeVals'];
                }
                $fieldVals = $valConfig['vals'] ;
            }else{
                $fieldVals = $valConfig;
            }
            $valsSeparator = $valConfig['valsSeparator'] ?? ',';
            $hasInIsMerge = $valConfig['hasInIsMerge'] ?? false;
//            if(!empty($excludeVals))  Tool::formatOneArrVals($fieldVals, $excludeVals, $valsSeparator);
//            if( ( (is_string($fieldVals) || is_numeric($fieldVals)) && strlen($fieldVals) > 0) || (is_array($fieldVals) && !empty($fieldVals)) ) $isEmpeyVals = false;
            if(Tool::appendParamQuery($queryParams, $fieldVals, $field, $excludeVals, $valsSeparator, $hasInIsMerge)){
                $hasValidVal = true;
            }
        }
        if(!isset($extParams['useQueryParams'])) $extParams['useQueryParams'] = false;
//        $extParams = [
//            'handleKeyArr' => ['ability', 'joinItemsStandards', 'projectStandards'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//        ];
        // 对数据进行拼接处理
        if(isset($extParams['sqlParams'])){
            $sqlParams = $extParams['sqlParams'] ?? [];
            foreach($sqlParams as $tKey => $tVals){
                if(isset($queryParams[$tKey]) && in_array($tKey, ['where',  'whereIn', 'whereNotIn', 'whereBetween', 'whereNotBetween'])){// 'select', 'orderBy',
                    $queryParams[$tKey] = array_merge($queryParams[$tKey], $tVals);
                }else{
                    $queryParams[$tKey] = $tVals;
                }

            }
            unset($extParams['sqlParams']);
        }

        // 可以有下标--翻页相关的配置，没有则可以用 request中的---这里的配置优先
        // 是参数  $operate_type = 2:专用参数
        $page_config = $extParams['page_config'] ?? [];
        if(isset($extParams['page_config'])){
            unset($extParams['page_config']);
        }
        if($operate_type == 2){
            $page_config['pagesize'] = $page_size;
            $queryParams['page_config'] = $page_config;
        }

        // 查询字段有值  或  查询字段无值  但是  指定 强制查询时
        // if(!$isEmpeyVals || ($isEmpeyVals && $fieldEmptyQuery)){
        if($hasValidVal || (!$hasValidVal && $fieldEmptyQuery)){
            switch ($operate_type)
            {
                case 1:
                    $dataArr = static::getList($request, $controller, 1, $queryParams, $relations, $extParams, $notLog)['result']['data_list'] ?? [];
                    break;
                case 8:// 获得数量值
                    $queryParams['count'] = 0;
                    $dataArr = static::getList($request, $controller, 1, $queryParams, $relations, $extParams, $notLog)['result']['total'] ?? 0;
                    break;
                case 2://  1：一维 ，>1 二维
                    $company_id = $controller->company_id;
//                     $dataArr = static::getLimitDataQuery($request, $controller, $company_id, $page_size, $queryParams, $relations, $extParams, $notLog);
                    // $queryParams['limit'] = $page_size;
                    $dataArr = static::getList($request, $controller, 2, $queryParams, $relations, $extParams, $notLog)['result']['data_list'] ?? [];
                    if($page_size == 1) $dataArr = $dataArr[0] ?? [];
                    break;
                case 4:
                    $company_id = $controller->company_id;
                    $dataArr = static::getInfoDataByQuery($request, $controller, $company_id, $queryParams, $relations, $extParams, $notLog);
                    break;
                default:
//                    $str = md5($str);
                    break;
            }
        }
        if($operate_type == 8 && !is_numeric($dataArr)) $dataArr = 0;
        return $dataArr;
    }

    /**
     * 获得列表数据--根据ids
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string / array $ids  查询的id ,多个用逗号分隔, 或数组【一维】
     * @param array $extParams 其它扩展参数 -- 其它条件或排序，放这个数组中。
     *    $extParams = [
     *        'useQueryParams' => '是否用来拼接查询条件，true:用[默认];false：不用'
     *        'sqlParams' => [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
     *           'where' => '如果有值，则替换where'
     *           'select' => '如果有值，则替换select'
     *           'orderBy' => '如果有值，则替换orderBy'
     *           'whereIn' => '如果有值，则替换whereIn'
     *           'whereNotIn' => '如果有值，则替换whereNotIn'
     *           'whereBetween' => '如果有值，则替换whereBetween'
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween'
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *       'relationFormatConfigs'=> [],// 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
     *       'listHandleKeyArr'=> [],// 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
     *       'infoHandleKeyArr'=> [],// 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ]
     *   ];
     * @param mixed $relations 关系
     * @param string $idFieldName id的字段名称 默认 id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getListByIds(Request $request, Controller $controller, $ids = '', $extParams = [], $relations = '', $idFieldName = 'id', $notLog = 0){
        if(empty($ids)) return [];
        if(is_array($ids))  $ids = implode(',', $ids);
        $queryParams = [
            'where' => [
                //    ['company_id', $company_id],
                // ['operate_staff_id', $user_id],
            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//                //,'operate_staff_id','operate_staff_history_id'
//                ,'created_at'
//            ],
//            'orderBy' => ['sort_num'=>'desc','id'=>'desc'],
//            'orderBy' => ['id'=>'desc'],
        ];// 查询条件参数

        if (!empty($ids)) {
            if (strpos($ids, ',') === false) { // 单条
//                array_push($queryParams['where'], [$idFieldName, $ids]);
                if(!isset($extParams['sqlParams']['where'])) $extParams['sqlParams']['where'] = [];
                array_push($extParams['sqlParams']['where'], [$idFieldName, $ids]);
            } else {
                $idArr = array_values(array_unique(explode(',', $ids)));// 去重，重按数字下标
//                $queryParams['whereIn'][$idFieldName] = Tool::arrClsEmpty($idArr);
//                $queryParams['whereIn'][$idFieldName] = Tool::arrClsEmpty($idArr);
                $extParams['sqlParams']['whereIn'][$idFieldName] = Tool::arrClsEmpty($idArr);
            }
        }
        // 没有传，则用false
        if(!isset($extParams['useQueryParams']))  $extParams['useQueryParams'] = false;
        $result = static::getList($request, $controller, 1 + 0, $queryParams, $relations, $extParams, $notLog);
        $data_list = $result['result']['data_list'] ?? [];
        return $data_list;
    }


    /**
     * 获得列表数据--所有数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $oprateBit 操作类型位 1:获得所有的; 2 分页获取[同时有1和2，2优先]；4 返回分页html翻页代码 8 返回分页html翻页代码--a链接形式seo用
     * @param string $queryParams 条件数组/json字符
     *   可以有下标--翻页相关的配置，没有则可以用 request中的---这里的配置优先
     *  'page_config' => [
     *       'page' => $page,// 当前页,如果不正确默认第一页
     *      'pagesize' => $pagesize,// 每页显示数量,取值1 -- 100 条之间,默认15条
     *      'total' => $total,// 总记录数,优化方案：传0传重新获取总数，如果传了，则不会再获取，而是用传的，减软数据库压力;=-5:只统计条件记录数量，不返回数据
     *      // 追加两个参数 - 需要时才用
     *      // 链接地址模板 http://www.***.com/list/{page} 主要是这个page 替换为具体的页数
     *      'url_model' => $url_model,
     *      // 链接地址模板 $url_model 中的页数标签 默认 {page}
     *      'page_tag' => $page_tag,
     *  ]
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *        'useQueryParams' => '是否用来拼接查询条件，true:用[默认];false：不用'
     *        'sqlParams' => [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
     *           'where' => '如果有值，则替换where'
     *           'select' => '如果有值，则替换select'
     *           'orderBy' => '如果有值，则替换orderBy'
     *           'whereIn' => '如果有值，则替换whereIn'
     *           'whereNotIn' => '如果有值，则替换whereNotIn'
     *           'whereBetween' => '如果有值，则替换whereBetween'
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween'
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *       'relationFormatConfigs'=> [],// 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
     *       'listHandleKeyArr'=> [],// 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
     *       'infoHandleKeyArr'=> [],// 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
     *       'finalHandleKeyArr'=> [],// 一维数组，对列表二维数数据【批量】需要处理的标记--处理完关系后的数据格式化， 重写 handleFinalDataFormat 方法实现
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ]
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getList(Request $request, Controller $controller, $oprateBit = 2 + 4, $queryParams = [], $relations = '', $extParams = [], $notLog = 0){
        $company_id = $controller->company_id;

        // 获得数据
        $defaultQueryParams = [
            'where' => [
//                ['company_id', $company_id],
//                //['mobile', $keyword],
            ],
//            'select' => [
//                'id','company_id','position_name','sort_num'
//                //,'operate_staff_id','operate_staff_id_history'
//                ,'created_at'
//            ],
            'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
        ];
        // 修改默认查询条件
        static::listDefaultQuery($request,  $controller, $defaultQueryParams, $notLog);
        // 查询条件参数
        if(empty($queryParams)){
            $queryParams = $defaultQueryParams;
        }
        $isExport = 0;
        $useSearchParams = $extParams['useQueryParams'] ?? true;// 是否用来拼接查询条件，true:用[默认];false：不用
        // 其它sql条件[覆盖式]
        $sqlParams = $extParams['sqlParams'] ?? [];
        $sqlKeys = array_keys($sqlParams);
        foreach($sqlKeys as $tKey){
            // if(isset($sqlParams[$tKey]) && !empty($sqlParams[$tKey]))  $queryParams[$tKey] = $sqlParams[$tKey];
            if(isset($sqlParams[$tKey]) )  $queryParams[$tKey] = $sqlParams[$tKey];
        }

        if($useSearchParams) {
            // $params = static::formatListParams($request, $controller, $queryParams);
//            $province_id = CommonRequest::getInt($request, 'province_id');
//            if($province_id > 0 )  array_push($queryParams['where'], ['city_ids', 'like', '' . $province_id . ',%']);

//            $is_active = CommonRequest::get($request, 'is_active');
//            if(is_numeric($is_active) )  array_push($queryParams['where'], ['is_active', '=', $is_active]);

            // 参数拼接
            static::joinListParams($request, $controller,$queryParams, $notLog);

            $ids = CommonRequest::get($request, 'ids');// 多个用逗号分隔,
            if (!empty($ids)) {
                if (strpos($ids, ',') === false) { // 单条
                    // array_push($queryParams['where'], ['id', $ids]);
                    array_push($queryParams['where'], [static::$primary_key, $ids]);
                } else {
                    // $queryParams['whereIn']['id'] = explode(',', $ids);
                    $queryParams['whereIn'][static::$primary_key] = explode(',', $ids);
                }
            }

            $isExport = CommonRequest::getInt($request, 'is_export'); // 是否导出 0非导出 ；1导出数据
            if ($isExport == 1) $oprateBit = 1;
        }
        // $relations = ['CompanyInfo'];// 关系
        // $relations = '';//['CompanyInfo'];// 关系
        $result = static::getBaseListData($request, $controller, '', $queryParams, $relations , $oprateBit, $notLog);

        // 格式化数据
        $data_list = $result['data_list'] ?? [];
        RelationDB::resolvingRelationData($data_list, $relations);// 根据关系设置，格式化数据

        // 数据通过自定义方法格式化
        // 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
        $handleKeyArr = $extParams['handleKeyArr'] ?? [];
        if(!empty($handleKeyArr)) static::handleData($request, $controller, $data_list, $handleKeyArr);

        // 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
        $listHandleKeyArr = $extParams['listHandleKeyArr'] ?? [];
        $returnFields = [];
        if(!empty($listHandleKeyArr)){
            // 如果是一维数组，则转为二维数组
            $isMulti = Tool::isMultiArr($data_list, true);
            $main_list = [];
            static::handleRelationDataFormat($request, $controller, $main_list, $data_list, $listHandleKeyArr, $returnFields);
            if(!$isMulti) $data_list = $data_list[0] ?? [];

        }

        // 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
        $infoHandleKeyArr = $extParams['infoHandleKeyArr'] ?? [];
        if(!empty($infoHandleKeyArr)){
            $isMulti = Tool::isMultiArr($data_list, true);
            $temRelationDataList = [];
            foreach($data_list as $k => $v_info){
                static::infoRelationFormatExtend($request, $controller, $data_list[$k], $temRelationDataList, $infoHandleKeyArr, $returnFields);
            }
            if(!$isMulti) $data_list = $data_list[0] ?? [];
        }

        // 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
        $relationFormatConfigs = $extParams['relationFormatConfigs'] ?? [];
        if(!empty($relationFormatConfigs)) static::formatRelationList($request, $controller, $data_list, $relationFormatConfigs);

        // 处理完关系后的数据格式化
        $finalHandleKeyArr = $extParams['finalHandleKeyArr'] ?? [];
        // $returnFields = [];
        if(!empty($finalHandleKeyArr))  static::handleFinalData($request, $controller, $data_list, $finalHandleKeyArr,$returnFields);

        // 对查询结果进行for循环处理
        static::forFormatListData($request, $controller, $data_list, $notLog);
//        foreach($data_list as $k => $v){
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($data_list, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]
        $result['data_list'] = $data_list;
        // 导出功能
        if($isExport == 1){
            // 导出操作
            static::exportListData($request, $controller, $data_list, $notLog);
//            $headArr = ['work_num'=>'工号', 'department_name'=>'部门'];
//            ImportExport::export('','excel文件名称',$data_list,1, $headArr, 0, ['sheet_title' => 'sheet名称']);
            die;
        }elseif($isExport == 2){// 发送短信
            $countryCode = 86;// 国家码
            $send_type = 2;// 发送类型【1系统发送、2手动发送】
            static::commonSmsRequest( $request,  $controller, $data_list, $countryCode, $send_type, $notLog);
            $result = true;
        }
        // 非导出功能
        return ajaxDataArr(1, $result, '');
    }

    /**
     * 通用请求模板发送短信
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 需要发送短信的记录 一维或二维数组 -- 测试时可以传空数组
     * @param string $countryCode 国家码 86
     * @param int $send_type 发送类型【1系统发送、2手动发送】
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function commonSmsRequest(Request $request, Controller $controller, $data_list, $countryCode = 86, $send_type = 1, $notLog = 0){

        $sms_operate_type = CommonRequest::getInt($request, 'sms_operate_type');// 操作类型 1 发送短信  ; 2测试发送短信
        $sms_template_id = CommonRequest::getInt($request, 'sms_template_id');// 发短信的模板id
        $sms_mobile = CommonRequest::get($request, 'sms_mobile');// 测试短信时，接收短信的手机号--正常下不用，只有在测试发送时用到

        $sendMobileField = CommonRequest::get($request, 'sms_mobile_field');// 'mobile';// 发送手机号字段
        if(empty($sendMobileField)) $sendMobileField = 'mobile';

        $inputParamsArr = [];// 手动输入的参数值  ['参数代码' => '参数值'] -- 可为空数组：没有手动输入参数
        $input_param_code = CommonRequest::get($request, 'sms_param_code');// 参数 sms_param_code[] 或 ,,,
        Tool::valToArrVal($input_param_code, ',');// 不是数组，则转为数组
        $sms_param_type = CommonRequest::get($request, 'sms_param_type');// 参数 sms_param_type[] 或 ,,,  参数类型1日期时间、2固定值、4手动输入-发送时、8字段匹配
        Tool::valToArrVal($sms_param_type, ',');// 不是数组，则转为数组
        $input_param_val = CommonRequest::get($request, 'sms_param_val');// 参数 sms_param_val[] 或 ,,,
        Tool::valToArrVal($input_param_val, ',');// 不是数组，则转为数组
        foreach($input_param_code as $k => $tem_input_param_code){
            $tem_sms_param_type = $sms_param_type[$k] ?? 0;
            if($tem_sms_param_type != 4) continue;
            $inputParamsArr[$tem_input_param_code] = $input_param_val[$k] ?? '';
        }

        // $countryCode = 86;// 国家码
        // $send_type = 2;// 发送类型【1系统发送、2手动发送】

        // if(isset($data_list[0])) $data_list[0]['mobile'] = '15829686962'; // TODO
        // if(isset($data_list[1])) $data_list[1]['mobile'] = '15686165567'; // TODO

        if($sms_operate_type == 2){// 是测试
            $data_list = [];
            $data_list[$sendMobileField] = $sms_mobile;
            foreach($input_param_code as $k => $tem_input_param_code){
                $tem_sms_param_type = $sms_param_type[$k] ?? 0;
                if($tem_sms_param_type != 8) continue;
                $data_list[$tem_input_param_code] = $input_param_val[$k] ?? '';
            }

        }

        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        // 调用新加或修改接口
        $apiParams = [
//            'organize_id' => $organize_id,
//            'admin_type' => $admin_type,
            'sms_template_id' => $sms_template_id,
            'data_list' => $data_list,
            'inputParamsArr' => $inputParamsArr,
            'sendMobileField' => $sendMobileField,
            'countryCode' => $countryCode,
            'send_type' => $send_type,
             'company_id' => $company_id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 1,
        ];
        $methodName = 'sendSms';
//        if(isset($saveData['mini_openid']))  $methodName = 'replaceByIdWX';
        CTAPISmsTemplateBusiness::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);
        // static::sendSms($request, $controller, $sms_template_id, $data_list, $inputParamsArr, $sendMobileField, $countryCode, $send_type, $notLog);

    }

    /**
     * 模板发送短信
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $sms_template_id 选择发短信的模板id
     * @param array $data_list 需要发送短信的记录 一维或二维数组
     * @param array $inputParamsArr 手动输入的参数值  ['参数代码' => '参数值'] -- 可为空数组：没有手动输入参数
     * @param string $sendMobileField 发送手机号字段
     * @param string $countryCode 国家码 86
     * @param int $send_type 发送类型【1系统发送、2手动发送】
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
//    public static function sendSms(Request $request, Controller $controller, $sms_template_id, $data_list, $inputParamsArr = [], $sendMobileField = 'mobile', $countryCode = 86, $send_type = 1, $notLog = 0){
//
//        // 手动输入的参数值
//        // $inputParamsArr = [];// ['参数代码' => '参数值']
//        // 发送手机号字段
//        // $sendMobileField = 'mobile';
//        if(empty($sendMobileField)) return true;
//
//        // 如果是一维数组,则转为二维数组
//        $isMulti = Tool::isMultiArr($data_list, true);
//        // 去掉不是手机号的记录
//        foreach($data_list as $k => $info){
//            if(!isset($info[$sendMobileField])) throws('发送手机号字段指定有误，请重新选择！');
//            $temMobile = $info[$sendMobileField] ?? '';
//            if(empty($temMobile)){
//                unset($data_list[$k]);
//                continue;
//            }
//            // 判断手机号
//            $valiDateParam = [
//                ["var_name" => "mobile" ,"input" => $temMobile, "require"=>"true", "validator"=>"mobile", "message"=>'手机号格式有误！'],
//            ];
//            $errMsgArr = Tool::dataValid($valiDateParam, 2);
//            if(is_array($errMsgArr) && isset($errMsgArr['errMsg']) && !empty($errMsgArr['errMsg'])){
//                $recordErrText = implode('<br/>', $errMsgArr['errMsg']);
//                unset($data_list[$k]);
//                continue;
//            }
//        }
//        if(empty($data_list)) return true;
//
//        // 选择发短信的模板id
//        // $sms_template_id = 1;
//        $templateInfo = CTAPISmsTemplateBusiness::getInfoData($request, $controller, $sms_template_id, [], '', [], $notLog);
//        if(empty($templateInfo)) throws('短信模板【' . $sms_template_id . '】记录不存在！');
//        $template_name = $templateInfo['template_name'] ?? '';
//        if($templateInfo['open_status'] != 1) throws('短信模板【' . $template_name . '】非启用状态，不可发送短信！');
//        $template_content = $templateInfo['template_content'] ?? '';
//        $template_type = $templateInfo['template_type'] ?? 0;// 模板类型【1腾讯云SMS、2阿里云短信】
//        $template_code = $templateInfo['template_code'] ?? '';// 模板ID【第三方】
//        $sign_name = $templateInfo['sign_name'] ?? '';// 签名名称【第三方】
//        $template_key = 'sms_params';// $templateInfo['template_key'] ?? '';// 模板关键字【唯一】---这里手动指定，因为用户指定的值可能会有中文的情况
//        $smsType = $template_key;
//        $configCodeArr = [
//            'SignName' => $sign_name,
//            'TemplateCode' => $template_code,
//        ];
//
//        // 获得发短信的模块id
//        $module_id = $templateInfo['module_id'] ?? 0;
//        $moduleInfo = CTAPISmsModuleBusiness::getInfoData($request, $controller, $module_id, [], '', [], $notLog);
//        if(empty($moduleInfo)) throws('短信模块【' . $module_id . '】记录不存在！');
//        $module_name = $moduleInfo['module_name'] ?? '';
//        if($moduleInfo['open_status'] != 1) throws('短信模块【' . $module_name . '】非启用状态，不可发送短信！');
//
//        // 获得模板内容参数
//        $paramsArr = Tool::getLabelArr($template_content, '{', '}');
//
//        // $countryCode = 86;
//        $shuffle = false;// true;
//        // 短信配置相关的信息
//        $smsConfig = config('easysms');
//        $configs = $smsConfig['gateways'] ?? [];
//        $smsConfigList = [
//            'aliyun' => [
//                'access_key_id' => $configs['aliyun']['access_key_id'],
//                'access_key_secret' => $configs['aliyun']['access_key_secret'],
//                'sign_name' => $configs['aliyun']['sign_name'],//  签名名称
//                'regionId' => $configs['aliyun']['regionId'],// 地域和可用区 https://help.aliyun.com/document_detail/40654.html?spm=a2c6h.13066369.0.0.54a120f89HVXHt
//                // 尊敬的用户，您的验证码${code}，请在3分钟内使用，工作人员不会索取，请勿泄漏。
//                // 参数必须是 [a-zA-Z0-9]
////                'verification_code_params' => [// 验证码相关参数
////                    'SignName' => env('ALIYUN_SMS_VERIFICATION_SIGN_NAME', ''),// 值为空或没有此下标，会自动使用上层的sign_name值。 短信签名名称。请在控制台签名管理页面签名名称一列查看。
////                    'TemplateCode' => env('ALIYUN_SMS_VERIFICATION_TEMPLATE_CODE', ''),// 短信模板ID。请在控制台模板管理页面模板CODE一列查看。
////
////                ],
////                'template_params' => [// 短信模板替换参数
////                    'verification_code_params' => ['code'],// 验证码模板 参数必须是 [a-zA-Z0-9]
////                ]
//            ],
//            'qcloud'   => [// 短信内容使用 content。
//                'sdk_app_id' => $configs['qcloud']['sdk_app_id'], // SDK APP ID '腾讯云短信平台sdk_app_id'
//                'app_key'    => $configs['qcloud']['app_key'], // APP KEY '腾讯云短信平台app_key'
//                'secret_id' => $configs['qcloud']['secret_id'], // 通过接口访问时的 SecretId 密钥
//                'secret_key' => $configs['qcloud']['secret_key'], // 通过接口访问时的 SecretKey 密钥
//                'sign_name'  => $configs['qcloud']['sign_name'],// '可以不填写', // 对应的是短信签名中的内容（非id） '腾讯云短信平太签名'  (此处可设置为空，默认签名)
//                /***
//                 *
//                 *
//                 *  # 请选择大区 https://console.cloud.tencent.com/api/explorer?Product=sms&Version=2019-07-11&Action=SendSms&SignVersion=
//                 *  # ap-beijing 华北地区(北京)
//                 *  # ap-chengdu 西南地区(成都)
//                 *  # ap-chongqing 西南地区(重庆)
//                 *  # ap-guangzhou 华南地区(广州)
//                 *  # ap-guangzhou-open 华南地区(广州Open)
//                 *  # ap-hongkong 港澳台地区(中国香港)
//                 *  # ap-seoul 亚太地区(首尔)
//                 *  # ap-shanghai 华东地区(上海)
//                 *  #
//                 *  # ap-singapore 东南亚地区(新加坡)
//                 *  # eu-frankfurt 欧洲地区(法兰克福)
//                 *  # na-siliconvalley 美国西部(硅谷)
//                 *  # na-toronto 北美地区(多伦多)
//                 *  # ap-mumbai 亚太地区(孟买)
//                 *  # na-ashburn 美国东部(弗吉尼亚)
//                 *  # ap-bangkok 亚太地区(曼谷)
//                 *  # eu-moscow 欧洲地区(莫斯科)
//                 *  # ap-tokyo 亚太地区(东京)
//                 *  # 金融区
//                 *  # ap-shanghai-fsi 华东地区(上海金融)
//                 *  # ap-shenzhen-fsi 华南地区(深圳金融)
//                 *
//                 */
//                'regionId' => $configs['qcloud']['regionId'],// 地域和可用区
//                // ID 468796  --- 作废，因为第一个参数不能传中文。所以不用了
//                // 尊敬的用户：您的{1}验证码{2}，请在{3}分钟内使用，工作人员不会索取，请勿泄漏。
//                // 1: operateType 操作类型 如：注册--用不了  ； 2： code 如：验证码 2456  ； 3 ：有效时间(单位分钟) validMinute 如 3
//
//                // ID 470052
//                // 内容 尊敬的用户：您的{1}验证码{2}，请在{3}分钟内使用，工作人员不会索取，请勿泄漏。
//                // 1： code 如：验证码 2456  ； 2 ：有效时间(单位分钟) validMinute 如 3
////                'verification_code_params' => [// 验证码相关参数
////                    'SignName' => env('QCLOUD_SMS_VERIFICATION_SIGN_NAME', ''),// 值为空或没有此下标，会自动使用上层的sign_name值。 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请
////                    'TemplateCode' => env('QCLOUD_SMS_VERIFICATION_TEMPLATE_CODE', ''),// 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
////                ],
////                'template_params' => [// 短信模板替换参数
////                    'verification_code_params' => ['code', 'validMinute'],// 'operateType', 验证码模板--注意验证码模板变量参数只能是<=6的数字，不能是中文及字母。
////                ]
//            ],
//        ];
//        // pr($smsConfigList);
//        // 默认可用的发送网关
////        [
////            // 'yunpian',//云片
////            // 'aliyun',// 阿里云短信
////            'qcloud', //腾讯云
////        ]
//        $gateways = [];// ['qcloud'];//  $smsConfig['default']['gateways'] ?? [];
//        if($template_type == 1 ){// 1腾讯云SMS
//            array_push($gateways, 'qcloud');
//            $smsConfigList['qcloud'][$smsType] = $configCodeArr;
//            $smsConfigList['qcloud']['template_params'][$smsType] = $paramsArr;
//        }
//        if($template_type == 2 ){// 2阿里云短信
//            array_push($gateways, 'aliyun');
//            $smsConfigList['aliyun'][$smsType] = $configCodeArr;
//            $smsConfigList['aliyun']['template_params'][$smsType] = $paramsArr;
//        }
//        if(empty($paramsArr)){// 没有参数，直接发送短信
//            $mobileArr = Tool::getArrFields($data_list, $sendMobileField);
//            SendSMS::sendSmsCommonBath($send_type, $templateInfo, $smsConfigList, $gateways, $template_content, [], $mobileArr, $countryCode, $smsType, $shuffle);
//            return true;
//        }
//
//        $needParamsList = [];// 需要替换的参数
//        // 获得短信模块参数
//        $smsModuleParamsList = CTAPISmsModuleParamsBusiness::getFVFormatList( $request,  $controller, 1, 1
//            , ['module_id' => $module_id], false, [], ['sqlParams' => ['orderBy' =>['sort_num' => 'desc', 'id' => 'desc']]], $notLog);
//        $smsModuleParamsFormatList = Tool::arrUnderReset($smsModuleParamsList, 'param_code', 1, '_');// 转为参数代码为下标的数组
//        //$needParamsList = Tool::getArrFormatFields($smsModuleParamsFormatList, $paramsArr, false);// 获得指定下标的参数
//        $temArr = $smsModuleParamsFormatList;
//        $needParamsList = Tool::formatArrByKeys($temArr, $paramsArr, false);// 获得指定下标的参数
//
//        $commonParamsArr = array_diff($paramsArr, array_keys($needParamsList));
//
//        // 获得所有的常用参数
//        if(!empty($commonParamsArr)){
//            $smsCommonParamsList = CTAPISmsModuleParamsCommonBusiness::getFVFormatList( $request,  $controller, 1, 1
//                , [], true, [], ['sqlParams' => ['orderBy' =>['sort_num' => 'desc', 'id' => 'desc']]], $notLog);
//
//            $smsCommonParamsFormatList = Tool::arrUnderReset($smsCommonParamsList, 'param_code', 1, '_');// 转为参数代码为下标的数组
//            // $needCommonParamsList = Tool::getArrFormatFields($smsCommonParamsFormatList, $commonParamsArr, false);// 获得指定下标的参数
//            $temCommonArr = $smsCommonParamsFormatList;
//            $needCommonParamsList = Tool::formatArrByKeys($temCommonArr, $commonParamsArr, false);// 获得指定下标的参数
//
//            $lessParamsArr = array_diff($commonParamsArr, array_keys($needCommonParamsList));
//            if(!empty($lessParamsArr)){
//                throws('参数【' . implode('、', $lessParamsArr) . '】未配置！');
//            }
//            $needParamsList = array_merge($needParamsList, $needCommonParamsList);
//        }
//
//        // 对参数进行处理
//        $publicDataParams = [];// 所有的参数值，字段的默认给空--可以占顺序
//        $nowDateTime = date('Y-m-d H:i:s');
//        // $hasFieldParams = false;// 是否有字段记录匹配参数 ； true:有--需要一条记录一条记录替换； false：没有--可以批量发送
//        $fieldParamsArr = [];// 字段记录匹配参数数组
//        foreach($paramsArr as $keyName){
//            $paramConfigInfo = $needParamsList[$keyName] ?? [];
//            if(empty($paramConfigInfo)) throws('参数【' . $keyName . '】配置不能为空！');
//            $temParamName = $paramConfigInfo['param_name'];
//            $temParamType = $paramConfigInfo['param_type'];
//            $temDateTimeFormat = $paramConfigInfo['date_time_format'];
//            $temFixedVal = $paramConfigInfo['fixed_val'];
//            $temParamVal = '';
//            switch($temParamType){// 参数类型1日期时间、2固定值、4手动输入-发送时、8字段匹配
//                case 1:// 1日期时间
//                    if(!empty($temDateTimeFormat)) $temParamVal = judgeDate($nowDateTime, $temDateTimeFormat);
//                    break;
//                case 2:// 2固定值
//                    $temParamVal = $temFixedVal;
//                    break;
//                case 4:// 4手动输入-发送时
//                    $temParamVal = $inputParamsArr[$keyName] ?? '';
//                    break;
//                case 8:// 8字段匹配
//                    $temParamVal = '';
//                    // $hasFieldParams = true;
//                    array_push($fieldParamsArr, $keyName);
//                    break;
//                default:
//                    break;
//            }
//            $publicDataParams[$keyName] = $temParamVal;
//        }
//
//        if(empty($fieldParamsArr)){// 可以 批量发送  !$hasFieldParams
//            $mobileArr = Tool::getArrFields($data_list, $sendMobileField);
//            // 替换共有的参数
//            if(!empty($publicDataParams)) Tool::strReplaceKV($template_content, $publicDataParams, '{', '}');
//            SendSMS::sendSmsCommonBath($send_type, $templateInfo, $smsConfigList, $gateways, $template_content, $publicDataParams, $mobileArr, $countryCode, $smsType, $shuffle);
//            return true;
//        }
//
//        // 有第条记录单独的参数
//        foreach($data_list as $k => $tInfo){
//            $temParamsArr = $publicDataParams;
//            $sendTemplateContent = $template_content;
//            $temFieldValArr = Tool::getArrFormatFields($tInfo, $fieldParamsArr, true);// 获得指定下标的参数
//            $temParamsArr = array_merge($temParamsArr, $temFieldValArr);
//            // 替换共有的参数
//            if(!empty($temParamsArr)) Tool::strReplaceKV($sendTemplateContent, $temParamsArr, '{', '}');
//
//            $temMobileArr = [];
//            $temMobile = $tInfo[$sendMobileField] ?? '';
//            if(!is_array($temMobile) && !empty($temMobile)) $temMobileArr = explode(',', $temMobile);
//            SendSMS::sendSmsCommonBath($send_type, $templateInfo, $smsConfigList, $gateways, $sendTemplateContent, $temParamsArr, $temMobileArr, $countryCode, $smsType, $shuffle);
//
//        }
//        return true;
//    }

    /**
     * 根据参数的名称，获得参数传入值，并加入查询条件中。
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param string $paramName 参数的名称
     * @param string $fieldName 查询的字段名--表中的
     * @param boolean $paramIsNum 参数的值是一个，且是数字类型  true:数字；false:非数字--默认
     * @param array $excludeVals 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  ['']
     * @param string $valsSeparator 如果是多值字符串，多个值的分隔符;默认逗号 ,
     * @param boolean $hasInIsMerge 如果In条件有值时  true:合并；false:用新值--覆盖 --默认
     * @param array $paramVals 最终的有效值-- 一维数组
     * @return  boolean true:有拼查询  false:无
     * @author zouyan(305463219@qq.com)
     */
    public static function joinParamQuery(Request $request, Controller $controller, &$queryParams, $paramName = '', $fieldName = '', $paramIsNum = false, $excludeVals = [0, '0', ''], $valsSeparator = ',', $hasInIsMerge = false, &$paramVals = null){
        $paramVals = $paramIsNum ? CommonRequest::getInt($request,$paramName) : trim(CommonRequest::get($request,$paramName));// 多个用逗号分隔,

        return Tool::appendParamQuery($queryParams, $paramVals, $fieldName, $excludeVals, $valsSeparator, $hasInIsMerge);
        // 如果想自己用，可以用出下的形式
//        $class_id = CommonRequest::get($request, 'class_id');
//        if(is_numeric($class_id) && $class_id > 0 )  array_push($queryParams['where'], ['class_id', '=', $class_id]);
    }

    /**
     * 根据参数的名称，获得参数传入值，并加入查询条件中。
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param array $paramConfigs 可能的参数配置  -- 二维数组
     * @return  boolean true:  false:无
     * @author zouyan(305463219@qq.com)
     */
    public static function joinParamQueryByArr(Request $request, Controller $controller, &$queryParams, $paramConfigs = []){

//        $paramConfigs = [
//            [
//                // 必有下标
//            'paramName' => 'class_id', // 参数的名称 -- 必填
//           'fieldName' => 'class_id', // 查询的字段名--表中的 -- 必填
//                // 可有下标
//           'paramIsNum' => false,// 参数的值是一个，且是数字类型  true:数字；false:非数字--默认 -- 选填
//           'excludeVals' => [0, '0', ''],// 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  [''] -- 选填
//           'valsSeparator' => ',',// 如果是多值字符串，多个值的分隔符;默认逗号 , -- 选填
//           'hasInIsMerge' => false,// 如果In条件有值时  true:合并；false:用新值--覆盖 --默认 -- 选填
//           ]
//       ];

        if(empty($paramConfigs)) return false;
        foreach($paramConfigs as $k => $paramConfig){
            $paramName = $paramConfig['paramName'] ?? '';
            $fieldName = $paramConfig['fieldName'] ?? '';
            if(empty($paramName) || empty($fieldName)) continue;

            $paramIsNum = $paramConfig['paramIsNum'] ?? false;
            $excludeVals = $paramConfig['excludeVals'] ?? [0, '0', ''];
            $valsSeparator = $paramConfig['valsSeparator'] ?? ',';
            $hasInIsMerge = $paramConfig['hasInIsMerge'] ?? false;
            $paramVals = '';
            static::joinParamQuery($request, $controller, $queryParams, $paramName, $fieldName, $paramIsNum, $excludeVals, $valsSeparator, $hasInIsMerge, $paramVals);
            $paramConfigs[$k]['paramVals'] = $paramVals;
        }
        return true;
    }

    /**
     * 获得列表数据时，查询条件的Like参数拼接--有特殊的需要自己重写此方法--每个字类都有此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function joinListParamsLike(Request $request, Controller $controller, &$queryParams, $notLog = 0){

        // 公用通用查询
        $company_id = CommonRequest::get($request, 'company_id');
        if(strlen($company_id) > 0 && !in_array($company_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $company_id, 'company_id', [0, '0', ''], ',', false);

        $staff_id = CommonRequest::get($request, 'staff_id');
        if(strlen($staff_id) > 0 && !in_array($staff_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $staff_id, 'staff_id', [0, '0', ''], ',', false);

        $staff_id_history = CommonRequest::get($request, 'staff_id_history');
        if(strlen($staff_id_history) > 0 && !in_array($staff_id_history, [0, '-1']))  Tool::appendParamQuery($queryParams, $staff_id_history, 'staff_id_history', [0, '0', ''], ',', false);

        $operate_staff_id = CommonRequest::get($request, 'operate_staff_id');
        if(strlen($operate_staff_id) > 0 && !in_array($operate_staff_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $operate_staff_id, 'operate_staff_id', [0, '0', ''], ',', false);

        $operate_staff_id = CommonRequest::get($request, 'operate_staff_id');
        if(strlen($operate_staff_id) > 0 && !in_array($operate_staff_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $operate_staff_id, 'operate_staff_id', [0, '0', ''], ',', false);

        $operate_staff_id_history = CommonRequest::get($request, 'operate_staff_id_history');
        if(strlen($operate_staff_id_history) > 0 && !in_array($operate_staff_id_history, [0, '-1']))  Tool::appendParamQuery($queryParams, $operate_staff_id_history, 'operate_staff_id_history', [0, '0', ''], ',', false);

        $version_num = CommonRequest::get($request, 'version_num');
        if(strlen($version_num) > 0 && !in_array($version_num, [0, '-1']))  Tool::appendParamQuery($queryParams, $version_num, 'version_num', [0, '0', ''], ',', false);

        $version_history_id = CommonRequest::get($request, 'version_history_id');
        if(strlen($version_history_id) > 0 && !in_array($version_history_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $version_history_id, 'version_history_id', [0, '0', ''], ',', false);

        $version_num_history = CommonRequest::get($request, 'version_num_history');
        if(strlen($version_num_history) > 0 && !in_array($version_num_history, [0, '-1']))  Tool::appendParamQuery($queryParams, $version_num_history, 'version_num_history', [0, '0', ''], ',', false);

        // 有可能关键字不用like查询，所以单独写，每一个子类都写此代码
        $field = CommonRequest::get($request, 'field');
        $keyWord = CommonRequest::get($request, 'keyword');
        if (!empty($field) && !empty($keyWord)) {
            if(!isset($queryParams['where'])) $queryParams['where'] = [];
            array_push($queryParams['where'], [$field, 'like', '%' . $keyWord . '%']);
        }

    }

    /**
     * 获得列表数据时，查询条件的默认的查询条件[比如可以修改select、where、orderby等]--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function listDefaultQuery(Request $request, Controller $controller, &$queryParams, $notLog = 0){
//        $select = [];
//        if(!empty($select)) $queryParams['select'] = $select;
//        或
//        $queryParams['select'] = [];
    }

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

        // TODO 自己的参数查询拼接在这里-- 注意：多个id 的查询默认就已经有了，参数是 ids  多个用逗号分隔

        // 方式一  --- 自己拼接
        // $type_id = CommonRequest::get($request, 'type_id');
        // if(is_numeric($type_id) )  Tool::appendCondition($queryParams, 'type_id',  $type_id);// array_push($queryParams['where'], ['type_id', '=', $type_id]);

        // 方式二 --- 单个拼接--封装
        // static::joinParamQuery($request, $controller, $queryParams, 'class_id', 'class_id', true, [0, '0', ''], ',', false);

        // 方式三 ---  批量拼接 -- 封装

//        $paramConfigs = [
//            [
//                'paramName' => 'class_id', // 参数的名称 -- 必填
//                'fieldName' => 'class_id', // 查询的字段名--表中的 -- 必填
//                'paramIsNum' => false,// 参数的值是一个，且是数字类型  true:数字；false:非数字--默认 -- 选填
//                'excludeVals' => [0, '0', ''],// 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  [''] -- 选填
//                'valsSeparator' => ',',// 如果是多值字符串，多个值的分隔符;默认逗号 , -- 选填
//                'hasInIsMerge' => false,// 如果In条件有值时  true:合并；false:用新值--覆盖 --默认 -- 选填
//            ],
//        ];
//        static::joinParamQueryByArr($request, $controller, $queryParams, $paramConfigs);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

    /**
     * 获得列表数据时，对查询结果进行for循环处理--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function forFormatListData(Request $request, Controller $controller, &$data_list, $notLog = 0){
//        foreach($data_list as $k => $v){
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
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
//            $headArr = ['work_num'=>'工号', 'department_name'=>'部门'];
//            ImportExport::export('','excel文件名称',$data_list,1, $headArr, 0, ['sheet_title' => 'sheet名称']);
    }

    /**
     * 数据通过自定义方法格式化【处理完关系后的数据格式化】---如果有格式化，肯定会重写里面的 handleFinalDataFormat 方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 需要格式化的数据---一维/二维数组
     * @param array $finalHandleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     * @return  array
     * @author zouyan(305463219@qq.com)
     */
    public static function handleFinalData(Request $request, Controller $controller, &$data_list, $finalHandleKeyArr, &$returnFields = []){
        if(empty($finalHandleKeyArr) || !is_array($finalHandleKeyArr)) return $data_list;
        if(empty($data_list) || (!is_array($data_list) && !is_object($data_list))) return $data_list;

        $data_list = Tool::objectToArray($data_list);

        // 如果是一维数组，则转为二维数组
        $isMulti = Tool::isMultiArr($data_list, true);

        // 对数据的具体格式化操作
        static::handleFinalDataFormat($request, $controller, $data_list, $finalHandleKeyArr, $returnFields, $isMulti);

        if(!$isMulti) $data_list = $data_list[0] ?? [];
        return $data_list;
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
        return $data_list;
    }

    /**
     * 数据通过自定义方法格式化---如果有格式化，肯定会重写里面的handleDataFormat 方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 需要格式化的数据---一维/二维数组
     * @param array $handleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @return  boolean true
     * @author zouyan(305463219@qq.com)
     */
    public static function handleData(Request $request, Controller $controller, &$data_list, $handleKeyArr){
        if(empty($handleKeyArr) || !is_array($handleKeyArr)) return true;
        if(empty($data_list) || (!is_array($data_list) && !is_object($data_list))) return true;

        $data_list = Tool::objectToArray($data_list);

        // 如果是一维数组，则转为二维数组
        $isMulti = Tool::isMultiArr($data_list, true);

        // 对数据的具体格式化操作
        static::handleDataFormat($request, $controller, $data_list, $handleKeyArr, $isMulti);

        if(!$isMulti) $data_list = $data_list[0] ?? [];
        return true;
    }

    /**
     * 格式化数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 需要格式化的数据---二维数组(如果是一维数组，是转成二维数组后的数据)
     * @param array $handleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @param boolean 原数据类型 true:二维[默认];false:一维  ----没有用了，到不到
     * @return  boolean true
     * @author zouyan(305463219@qq.com)
     */
    public static function handleDataFormat(Request $request, Controller $controller, &$data_list, $handleKeyArr, $isMulti = true){

        // 重写开始

        $isNeedHandle = false;// 是否真的需要遍历处理数据 false:不需要：true:需要 ；只要有一个需要处理就标记

        /**
         *
        $typeKVArr = [];
        $contentKVArr = [];
        $resourceDataArr = [];
        //        if(!empty($data_list) ){
        // 获得分类名称
        if(in_array('templateType', $handleKeyArr)){
        $typeIdArr = array_values(array_filter(array_column($data_list,'type_id')));// 资源id数组，并去掉值为0的
        // kv键值对
        if(!empty($typeIdArr)) $typeKVArr = Tool::formatArrKeyVal(CTAPITemplateTypeBusiness::getListByIds($request, $controller, $typeIdArr), 'id', 'type_name');
        if(!$isNeedHandle && !empty($typeKVArr)) $isNeedHandle = true;
        }

        // 获得内容 templateContent
        if(in_array('templateContent', $handleKeyArr)){
        $idsArr = array_values(array_filter(array_column($data_list,'id')));// 资源id数组，并去掉值为0的
        // kv键值对
        if(!empty($idsArr)) $contentKVArr = Tool::formatArrKeyVal(CTAPITemplateContentBusiness::getListByIds($request, $controller, $idsArr, [], [], 'template_id'), 'template_id', 'template_content');
        if(!$isNeedHandle && !empty($contentKVArr)) $isNeedHandle = true;
        }

        // 处理图片

        if(in_array('siteResources', $handleKeyArr)){
        $resourceIdArr = array_values(array_filter(array_column($data_list,'resource_id')));// 资源id数组，并去掉值为0的
        if(!empty($resourceIdArr)) $resourceDataArr = Tool::arrUnderReset(CTAPIResourceBusiness::getResourceByIds($request, $controller, $resourceIdArr), 'id', 2);// getListByIds($request, $controller, implode(',', $resourceIdArr));
        if(!$isNeedHandle && !empty($resourceDataArr)) $isNeedHandle = true;
        }

        //        }
         *
         */
        // 改为不返回，好让数据下面没有数据时，有一个空对象，方便前端或其它应用处理数据
        // if(!$isNeedHandle){// 不处理，直接返回 // if(!$isMulti) $data_list = $data_list[0] ?? [];
        //    return true;
        // }

        /**
         *
        foreach($data_list as $k => $v){
        //            // 公司名称
        //            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
        //            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);

        // 分类名称
        if(in_array('templateType', $handleKeyArr)){
        $data_list[$k]['type_name'] = $typeKVArr[$v['type_id']] ?? '';
        }

        // 获得内容
        if(in_array('templateContent', $handleKeyArr)){
        $data_list[$k]['content'] = $contentKVArr[$v['id']] ?? '';
        }

        // 资源url
        if(in_array('siteResources', $handleKeyArr)){
        // $resource_list = [];
        $resource_list = $resourceDataArr[$v['resource_id']] ?? [];
        if(isset($v['site_resources'])){
        Tool::resourceUrl($v, 2);
        $resource_list = Tool::formatResource($v['site_resources'], 2);
        unset($data_list[$k]['site_resources']);
        }
        $data_list[$k]['resource_list'] = $resource_list;
        }
        }
         *
         */

        // 重写结束
        return true;
    }

    // ~~~~~~~~~~~~~~~~~详情开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    /**
     * 根据id获得单条数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id id
     * @param array $selectParams 查询字段参数--一维数组
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *       'relationFormatConfigs'=> [],// 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
     *       'listHandleKeyArr'=> [],// 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
     *       'infoHandleKeyArr'=> [],// 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
     *       'finalHandleKeyArr'=> [],// 一维数组，对列表二维数数据【批量】需要处理的标记--处理完关系后的数据格式化， 重写 handleFinalDataFormat 方法实现
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoData(Request $request, Controller $controller, $id, $selectParams = [], $relations = '', $extParams = [], $notLog = 0){
        $company_id = $controller->company_id;
        // $relations = '';
        // $resultDatas = APIDogToolsRequest::getinfoApi(self::$model_name, '', $relations, $company_id , $id);
        $info = static::getInfoDataBase($request, $controller,'', $id, $selectParams, $relations, $notLog);
        RelationDB::resolvingRelationData($info, $relations);// 根据关系设置，格式化数据
        // 判断权限
//        $judgeData = [
//            // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        static::judgePowerByObj($request, $controller, $info, $judgeData );

        // 数据通过自定义方法格式化
        // 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
        $handleKeyArr = $extParams['handleKeyArr'] ?? [];
        if(!empty($handleKeyArr)) static::handleData($request, $controller, $info, $handleKeyArr);

        // 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
        $listHandleKeyArr = $extParams['listHandleKeyArr'] ?? [];
        $returnFields = [];
        if(!empty($listHandleKeyArr)){
            // 如果是一维数组，则转为二维数组
            $isMulti = Tool::isMultiArr($info, true);
            $main_list = [];
            static::handleRelationDataFormat($request, $controller, $main_list, $info, $listHandleKeyArr, $returnFields);
            if(!$isMulti) $info = $info[0] ?? [];

        }
        // 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
        $infoHandleKeyArr = $extParams['infoHandleKeyArr'] ?? [];
        if(!empty($infoHandleKeyArr)){
            $isMulti = Tool::isMultiArr($info, true);
            $temRelationDataList = [];
            foreach($info as $k => $v_info){
                static::infoRelationFormatExtend($request, $controller, $info[$k], $temRelationDataList, $infoHandleKeyArr, $returnFields);
            }
            if(!$isMulti) $info = $info[0] ?? [];
        }

        // 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
        $relationFormatConfigs = $extParams['relationFormatConfigs'] ?? [];
        if(!empty($relationFormatConfigs)) static::formatRelationList($request, $controller, $info, $relationFormatConfigs);

        // 处理完关系后的数据格式化
        $finalHandleKeyArr = $extParams['finalHandleKeyArr'] ?? [];
        // $returnFields = [];
        if(!empty($finalHandleKeyArr))  static::handleFinalData($request, $controller, $info, $finalHandleKeyArr,$returnFields);

        // 格式化数据
        static::formatInfoData($request, $controller,$info, $notLog);

        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($info, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]
        return $info;
    }

    /**
     * 获得详情数据时，对查询结果进行格式化操作--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $info 详情数据 --- 一维数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function formatInfoData(Request $request, Controller $controller, &$info, $notLog = 0){

    }

    /**
     * 根据条件获得一条详情记录 - 一维
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $company_id 企业id
     * @param array $queryParams 条件数组/json字符
     *   $queryParams = [
     *       'where' => [
     *           ['order_type', '=', 1],
     *           // ['staff_id', '=', $user_id],
     *           ['order_no', '=', $order_no],
     *           // ['id', '&' , '16=16'],
     *           // ['company_id', $company_id],
     *           // ['admin_type',self::$admin_type],
     *       ],
     *       // 'whereIn' => [
     *           //   'id' => $subjectHistoryIds,
     *       //],
     *       'select' => ['id', 'status'],
     *       // 'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
     *   ];
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *       'relationFormatConfigs'=> [],// 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
     *       'listHandleKeyArr'=> [],// 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
     *       'infoHandleKeyArr'=> [],// 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoDataByQuery(Request $request, Controller $controller, $company_id, $queryParams = [], $relations = '', $extParams = [], $notLog = 0){
        // $company_id = $controller->company_id;
        // $relations = '';
        // $resultDatas = APIDogToolsRequest::getinfoApi(self::$model_name, '', $relations, $company_id , $id);
        $info = static::getInfoByQuery($request, $controller,'', $company_id, $queryParams, $relations, $notLog);
        RelationDB::resolvingRelationData($info, $relations);// 根据关系设置，格式化数据
        // 判断权限
//        $judgeData = [
//            // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        static::judgePowerByObj($request, $controller, $info, $judgeData );

        // 数据通过自定义方法格式化
        // 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
        $handleKeyArr = $extParams['handleKeyArr'] ?? [];
        if(!empty($handleKeyArr)) static::handleData($request, $controller, $info, $handleKeyArr);

        // 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
        $listHandleKeyArr = $extParams['listHandleKeyArr'] ?? [];
        $returnFields = [];
        if(!empty($listHandleKeyArr)){
            // 如果是一维数组，则转为二维数组
            $isMulti = Tool::isMultiArr($info, true);
            $main_list = [];
            static::handleRelationDataFormat($request, $controller,$main_list, $info, $listHandleKeyArr, $returnFields);
            if(!$isMulti) $info = $info[0] ?? [];

        }
        // 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
        $infoHandleKeyArr = $extParams['infoHandleKeyArr'] ?? [];
        if(!empty($infoHandleKeyArr)){
            $isMulti = Tool::isMultiArr($info, true);
            $temRelationDataList = [];
            foreach($info as $k => $v_info){
                static::infoRelationFormatExtend($request, $controller, $info[$k], $temRelationDataList, $infoHandleKeyArr, $returnFields);
            }
            if(!$isMulti) $info = $info[0] ?? [];
        }

        // 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
        $relationFormatConfigs = $extParams['relationFormatConfigs'] ?? [];
        if(!empty($relationFormatConfigs)) static::formatRelationList($request, $controller, $info, $relationFormatConfigs);


        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($info, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]
        return $info;
    }


    /**
     * 根据条件获得一条详情记录 - pagesize 1:返回一维数组,>1 返回二维数组  -- 推荐有这个按条件查询详情
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $company_id 企业id
     * @param int $pagesize 想获得的记录数量 1 , 2 。。 默认1
     * @param array $queryParams 条件数组/json字符
     *   $queryParams = [
     *       'where' => [
     *           ['order_type', '=', 1],
     *           // ['staff_id', '=', $user_id],
     *           ['order_no', '=', $order_no],
     *           // ['id', '&' , '16=16'],
     *           // ['company_id', $company_id],
     *           // ['admin_type',self::$admin_type],
     *       ],
     *       // 'whereIn' => [
     *           //   'id' => $subjectHistoryIds,
     *       //],
     *       'select' => ['id', 'status'],
     *       // 'orderBy' => ['is_default'=>'desc', 'id'=>'desc'],
     *   ];
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *       'relationFormatConfigs'=> [],// 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
     *       'listHandleKeyArr'=> [],// 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
     *       'infoHandleKeyArr'=> [],// 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getLimitDataQuery(Request $request, Controller $controller, $company_id, $pagesize = 1, $queryParams = [], $relations = '', $extParams = [], $notLog = 0){
        // $company_id = $controller->company_id;
        // $relations = '';
        $infoList = static::getInfoQuery($request, $controller,'', $company_id, $pagesize, $queryParams, $relations, $notLog);
        RelationDB::resolvingRelationData($infoList, $relations);// 根据关系设置，格式化数据
        // 判断权限
//        $judgeData = [
//            // 'company_id' => $company_id,
//            'id' => $company_id,
//        ];
//        static::judgePowerByObj($request, $controller, $infoList, $judgeData );

        // 数据通过自定义方法格式化
        // 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
        $handleKeyArr = $extParams['handleKeyArr'] ?? [];
        if(!empty($handleKeyArr)) static::handleData($request, $controller, $infoList, $handleKeyArr);

        // 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
        $listHandleKeyArr = $extParams['listHandleKeyArr'] ?? [];
        $returnFields = [];
        if(!empty($listHandleKeyArr)){
            // 如果是一维数组，则转为二维数组
            $isMulti = Tool::isMultiArr($infoList, true);
            $main_list = [];
            static::handleRelationDataFormat($request, $controller, $main_list, $infoList, $listHandleKeyArr, $returnFields);
            if(!$isMulti) $infoList = $infoList[0] ?? [];

        }
        // 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
        $infoHandleKeyArr = $extParams['infoHandleKeyArr'] ?? [];
        if(!empty($infoHandleKeyArr)){
            $isMulti = Tool::isMultiArr($infoList, true);
            $temRelationDataList = [];
            foreach($infoList as $k => $v_info){
                static::infoRelationFormatExtend($request, $controller, $infoList[$k], $temRelationDataList, $infoHandleKeyArr, $returnFields);
            }
            if(!$isMulti) $infoList = $infoList[0] ?? [];
        }

        // 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
        $relationFormatConfigs = $extParams['relationFormatConfigs'] ?? [];
        if(!empty($relationFormatConfigs)) static::formatRelationList($request, $controller, $infoList, $relationFormatConfigs);

        $temFormatData = $extParams['formatDataUbound'] ?? [];// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
        Tool::formatArrUboundDo($infoList, $temFormatData);//格式化数据[取指下下标、排除指定下标、修改下标名称]

        return $infoList;
    }

    /**
     * 格式化列表查询条件-暂不用
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $queryParams 条件数组/json字符
     * @return  array 参数数组 一维数据
     * @author zouyan(305463219@qq.com)
     */
//    public static function formatListParams(Request $request, Controller $controller, &$queryParams = []){
//        $params = [];
//        $title = CommonRequest::get($request, 'title');
//        if(!empty($title)){
//            $params['title'] = $title;
//            array_push($queryParams['where'],['title', 'like' , '%' . $title . '%']);
//        }
//
//        $ids = CommonRequest::get($request, 'ids');// 多个用逗号分隔,
//        if (!empty($ids)) {
//            $params['ids'] = $ids;
//            if (strpos($ids, ',') === false) { // 单条
//                array_push($queryParams['where'],['id', $ids]);
//            }else{
//                $queryParams['whereIn']['id'] = explode(',',$ids);
//                $params['idArr'] = explode(',',$ids);
//            }
//        }
//        return $params;
//    }

    /**
     * 获得当前记录前/后**条数据--二维数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id 当前记录id
     * @param int $nearType 类型 1:前**条[默认]；2后**条 ; 4 最新几条;8 有count下标则是查询数量, 返回的数组中total 就是真实的数量
     * @param int $limit 数量 **条
     * @param int $offset 偏移数量
     * @param string $queryParams 条件数组/json字符
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *       'relationFormatConfigs'=> [],// 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
     *       'listHandleKeyArr'=> [],// 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
     *       'infoHandleKeyArr'=> [],// 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
     *   ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据 - 二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getNearList(Request $request, Controller $controller, $id = 0, $nearType = 1, $limit = 1, $offset = 0, $queryParams = [], $relations = '', $extParams = [], $notLog = 0)
    {
        $company_id = $controller->company_id;
        // 前**条[默认]
        $defaultQueryParams = [
            'where' => [
                //  ['company_id', $company_id],
//                ['id', '>', $id],
            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//                //,'operate_staff_id','operate_staff_id_history'
//                ,'created_at'
//            ],
            'orderBy' => static::$orderBy,// ['sort_num'=>'desc','id'=>'desc'],
//            'orderBy' => ['id'=>'asc'],
            'limit' => $limit,
            'offset' => $offset,
            // 'count'=>'0'
        ];
        // 默认的查询条件[比如可以修改select、where、orderby等]-
        static::nearDefaultQuery($request, $controller, $defaultQueryParams, $notLog);

        if(($nearType & 1) == 1){// 前**条
            // $defaultQueryParams['orderBy'] = ['id'=>'asc'];
            $defaultQueryParams['orderBy'] = [static::$primary_key=>'asc'];
            // array_push($defaultQueryParams['where'],['id', '>', $id]);
            array_push($defaultQueryParams['where'],[static::$primary_key, '>', $id]);
        }

        if(($nearType & 2) == 2){// 后*条
            // array_push($defaultQueryParams['where'],['id', '<', $id]);
            array_push($defaultQueryParams['where'],[static::$primary_key, '<', $id]);
            // $defaultQueryParams['orderBy'] = ['id'=>'desc'];
            $defaultQueryParams['orderBy'] = [static::$primary_key=>'desc'];
        }

        if(($nearType & 4) == 4){// 4 最新几条
            // $defaultQueryParams['orderBy'] = ['id'=>'desc'];
            $defaultQueryParams['orderBy'] = [static::$primary_key=>'desc'];
        }

        if(($nearType & 8) == 8){// 8 有count下标则是查询数量, 返回的数组中total 就是真实的数量
            $defaultQueryParams['count'] = 0;
        }

        if(empty($queryParams)){
            $queryParams = $defaultQueryParams;
        }
//        $temFormatData = [
//            'formatDataUbound' => $extParams['formatDataUbound'] ?? [],// 格式化数据 具体参数使用说明，请参阅 Tool::formatArrUbound 方法
//        ];
//        if(isset($extParams['handleKeyArr'])) $temFormatData['handleKeyArr'] = $extParams['handleKeyArr'] ?? [];

        $result = static::getList($request, $controller, 1 + 0, $queryParams, $relations, $extParams, $notLog);// , $temFormatData, $notLog);
        // 格式化数据
        $data_list = $result['result']['data_list'] ?? [];
//        RelationDB::resolvingRelationData($data_list, $relations);// 根据关系设置，格式化数据 -- 已经在getList方法中处理过
        if($nearType == 1) $data_list = array_reverse($data_list); // 相反;
//        foreach($data_list as $k => $v){
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
//        $result['result']['data_list'] = $data_list;
        return $data_list;
    }

    /**
     * 获得当前记录前/后**条数据-数据时，查询条件的默认的查询条件[比如可以修改select、where、orderby等]--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function nearDefaultQuery(Request $request, Controller $controller, &$queryParams, $notLog = 0){
//        $select = [];
//        if(!empty($select)) $queryParams['select'] = $select;
//        或
//        $queryParams['select'] = [];
    }

    /**
     * 导入模版
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list 初始数据  -- 二维数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function importTemplate(Request $request, Controller $controller, $data_list = [], $notLog = 0)
    {
        static::importTemplateExcel($request, $controller, $data_list, $notLog);
        die;
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
//        $data_list = [];
//        $headArr = ['work_num'=>'工号', 'department_name'=>'部门'];
//        ImportExport::export('','员工导入模版',$data_list,1, $headArr, 0, ['sheet_title' => '员工导入模版']);
    }


    /**
     * 删除单条数据--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function delAjax(Request $request, Controller $controller, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
//        $id = CommonRequest::getInt($request, 'id');
//        // 调用删除接口
//        $apiParams = [
//            'company_id' => $company_id,
//            'id' => $id,
//            'operate_staff_id' => $user_id,
//            'modifAddOprate' => 1,
//             'extendParams' => []
//        ];
//        static::exeDBBusinessMethodCT($request, $controller, '',  'delById', $apiParams, $company_id, $notLog);
//        return ajaxDataArr(1, $id, '');
         return static::delAjaxBase($request, $controller, '', $notLog);

    }

    /**
     * 删除单条数据--2企业4个人 数据删除
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param array $extendParams 其它参数--扩展用参数
     * @param string $methodName 删除的方法名 为空：默认delById
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function delCustomizeAjax(Request $request, Controller $controller, $organize_id = 0, $extendParams = [], $methodName = 'delById', $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        $id = CommonRequest::get($request, 'id');
        if(is_array($id)) $id = implode(',', $id);
        // 调用删除接口
        $apiParams = [
            'company_id' => $company_id,
            'id' => $id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 1,
            'extendParams' => array_merge(['organize_id' => $organize_id], $extendParams)
        ];
        if(empty($methodName)) $methodName = 'delById';
        static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);
        return ajaxDataArr(1, $id, '');
        // return static::delAjaxBase($request, $controller, '', $notLog);

    }


    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $saveData 要保存或修改的数组
     * @param int $id id
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'judgeDataKey' => '',// 数据验证的下标 add: 添加；modify:修改; replace:新加或修改等
     *   ];
     * @param boolean $modifAddOprate 修改时是否加操作人，true:加;false:不加[默认]
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    /*
     *
    public static function replaceById(Request $request, Controller $controller, $saveData, &$id, $extParams = [], $modifAddOprate = false, $notLog = 0){
        // $tableLangConfig = static::getLangModelsDBConfig('',  1);
        $company_id = $controller->company_id;
        // 验证数据
        $judgeType = ($id > 0) ? 4 : 2;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
        // $mustFields = [];//
        $judgeDataKey = $extParams['judgeDataKey'] ?? '';
        static::judgeDataThrowsErr($judgeType, $saveData, $mustFields, $judgeDataKey, 1, "<br/>", ['request' => $request, 'controller' => $controller , 'id' => $id]);

        if($id > 0){
            // 判断权限
//            $judgeData = [
//                'company_id' => $company_id,
//            ];
//            $relations = '';
//            static::judgePower($request, $controller, $id, $judgeData, '', $company_id, $relations, $notLog);
            if($modifAddOprate) static::addOprate($request, $controller, $saveData);

        }else {// 新加;要加入的特别字段
            $addNewData = [
                //  'company_id' => $company_id,
            ];
            $saveData = array_merge($saveData, $addNewData);
            // 加入操作人员信息
            static::addOprate($request, $controller, $saveData);
        }
        // 新加或修改
        return static::replaceByIdBase($request, $controller, '', $saveData, $id, $notLog);
    }
     *
     */

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $saveData 要保存或修改的数组
     * @param int $id id
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'judgeDataKey' => '',// 数据验证的下标 add: 添加；modify:修改; replace:新加或修改等
     *   ];
     * @param boolean $modifAddOprate 修改时是否加操作人，true:加;false:不加[默认]
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById(Request $request, Controller $controller, $saveData, &$id, $extParams = [], $modifAddOprate = false, $notLog = 0){
        // $tableLangConfig = static::getLangModelsDBConfig('',  1);
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
//        if(isset($saveData['goods_name']) && empty($saveData['goods_name'])  ){
//            throws('商品名称不能为空！');
//        }
        // 特殊的验证

        // 验证数据
        $judgeType = ($id > 0) ? 4 : 2;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
        // $mustFields = [];
        //if(!empty($judgeDataKey)){
        //    $errMsgs = static::specialJudgeKey($mustFields, $saveData, $judgeDataKey, ['request' => $request, 'controller' => $controller , 'id' => $id]);
        //    if(!empty($errMsgs)) throws(implode('<br/>', $errMsgs));
        //}
        // static::judgeDBDataThrowErr($judgeType,$saveData, $mustFields, 1);
        $judgeDataKey = $extParams['judgeDataKey'] ?? '';
        static::judgeDataThrowsErr($judgeType, $saveData, $mustFields, $judgeDataKey, 1, "<br/>", ['request' => $request, 'controller' => $controller , 'id' => $id]);

        // 调用新加或修改接口
        $apiParams = [
            'saveData' => $saveData,
            'company_id' => $company_id,
            'id' => $id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => ($modifAddOprate == true) ? 1 : 0 ,// 0,
        ];
        $methodName = $extParams['methodName'] ?? 'replaceById';
        $id = static::exeDBBusinessMethodCT($request, $controller, '',  $methodName, $apiParams, $company_id, $notLog);
        // 操作成功后，可进行一些操作
        static::replaceByIdAPISucess($request, $controller, $apiParams, $id, $judgeType);

        return $id;
//        $isModify = false;
//        if($id > 0){
//            $isModify = true;
//            // 判断权限
////            $judgeData = [
////                'company_id' => $company_id,
////            ];
////            $relations = '';
////            static::judgePower($request, $controller, $id, $judgeData, '', $company_id, $relations, $notLog);
//            if($modifAddOprate) static::addOprate($request, $controller, $saveData);
//
//        }else {// 新加;要加入的特别字段
//            $addNewData = [
//               // 'company_id' => $company_id,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//            // 加入操作人员信息
//            static::addOprate($request, $controller, $saveData);
//        }
//        // 新加或修改
//        $result =  static::replaceByIdBase($request, $controller, '', $saveData, $id, $notLog);
//        if($isModify){
//            // 判断版本号是否要+1
//            $historySearch = [
//                //  'company_id' => $company_id,
//                'goods_id' => $id,
//            ];
//            static::compareHistoryOrUpdateVersion($request, $controller, '' , $id, ShopGoodsHistoryAPIBusiness::$model_name
//                , 'shop_goods_history', $historySearch, ['goods_id'], 1, $company_id);
//        }
//        return $result;
    }

    /**
     * replaceById 方法操作成功后可执行的一些操作----具体的表，如有需要，请重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $apiParams 请求的原数据
     * @param int $id id 成功后返回的id
     * @param array $judgeType 2 新建数据 ；4 修改数据
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceByIdAPISucess(Request $request, Controller $controller, $apiParams = [], $id = 0, $judgeType = 2){

    }

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

                // $id = $extParams['id'] ?? 0;
                $id = $extParams[static::$primary_key] ?? 0;
                if($id > 0){

                }

                break;
            default:
                break;
        }
        return $errMsgs;
    }

    // ***********导入***开始************************************************************
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
        // 参数
        $requestData = [
            'company_id' => $company_id,
            'staff_id' =>  $controller->user_id,
            'admin_type' =>  $controller->admin_type,//self::$admin_type,
            'save_data' => $saveData,
        ];
        $url = config('public.apiUrl') . config('apiUrl.apiPath.staffImport');
        // 生成带参数的测试get请求
        // $requestTesUrl = splicQuestAPI($url , $requestData);
        return HttpRequest::HttpRequestApi($url, $requestData, [], 'POST');
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
            '县区' => 'department',
            '归属营业厅或片区' => 'group',
            '姓名或渠道名称' => 'channel',
            //'姓名' => 'real_name',
            '工号' => 'work_num',
            '职务' => 'position',
            '手机号' => 'mobile',
            '性别' => 'sex',
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

    // ***********导入***结束************************************************************

    // ***********获得kv***开始************************************************************
    // 根据父id,获得子数据kv数组
    public static function getCityByPid(Request $request, Controller $controller, $parent_id = 0, $notLog = 0){
        $company_id = $controller->company_id;
        $kvParams = ['key' => 'id', 'val' => 'type_name'];
        $queryParams = [
            'where' => [
                // ['id', '&' , '16=16'],
                    ['parent_id', '=', $parent_id],
                //['mobile', $keyword],
                //['admin_type',self::$admin_type],
            ],
//            'whereIn' => [
//                'id' => $cityPids,
//            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//            ],
            'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],
        ];
        return static::getKVCT( $request,  $controller, '', $kvParams, [], $queryParams, $company_id, $notLog);
    }

    // 根据父id,获得子数据kv数组
    /**
     * 数据kv数组
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $kvParams 键值对 ，为空：默认为['key' => 'id', 'val' => 'type_name']
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *        'sqlParams' => [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
     *           'where' => '如果有值，则替换where'
     *           'select' => '如果有值，则替换select'
     *           'orderBy' => '如果有值，则替换orderBy'
     *           'whereIn' => '如果有值，则替换whereIn'
     *           'whereNotIn' => '如果有值，则替换whereNotIn'
     *           'whereBetween' => '如果有值，则替换whereBetween'
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween'
     *       ],
     *       'pagesize' => 20,// 每页显示的数量--可无此下标
     *       'page' = 1,// 当前页号--可无此下标
     *   ];
     * @param array $orderBy 排序，默认为static::$orderBy，
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 键值对一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getListKV(Request $request, Controller $controller, $kvParams = [], $extParams = [], $orderBy = [], $notLog = 0){
        $company_id = $controller->company_id;
        // $kvParams = ['key' => 'id', 'val' => 'type_name'];
        if(empty($kvParams)) $kvParams = ['key' => 'id', 'val' => 'type_name'];
        if(empty($orderBy)) $orderBy = static::$orderBy;
        $queryParams = [
            'where' => [
                // ['id', '&' , '16=16'],
                // ['parent_id', '=', $parent_id],
                //['mobile', $keyword],
                //['admin_type',self::$admin_type],
            ],
//            'whereIn' => [
//                'id' => $cityPids,
//            ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//            ],
            'orderBy' => $orderBy,// static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],
        ];

        if(isset($extParams['pagesize'])){
            $pagesize = $extParams['pagesize'] ?? 20;
            $page = $extParams['page'] ?? 1;
            // 偏移量
            $offset = ($page-1) * $pagesize;

            $limitParams = [
                'limit' => $pagesize,
                // 'take' => $pagesize,
                'offset' => $offset,
                // 'skip' => $offset,
            ];
            $queryParams = array_merge($queryParams, $limitParams);
        }

        // 其它sql条件[覆盖式]
        $sqlParams = $extParams['sqlParams'] ?? [];
        $sqlKeys = array_keys($sqlParams);
        foreach($sqlKeys as $tKey){
            // if(isset($sqlParams[$tKey]) && !empty($sqlParams[$tKey]))  $queryParams[$tKey] = $sqlParams[$tKey];
            if(isset($sqlParams[$tKey]) )  $queryParams[$tKey] = $sqlParams[$tKey];
        }
        return static::getKVCT( $request,  $controller, '', $kvParams, [], $queryParams, $company_id, $notLog);
    }
    // ***********获得kv***结束************************************************************

    // ***********通过组织条件获得kv***开始************************************************************
    /**
     * 获得列表数据--所有数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $pid 当前父id
     * @param int $oprateBit 操作类型位 1:获得所有的; 2 分页获取[同时有1和2，2优先]；4 返回分页html翻页代码 8 返回分页html翻页代码--a链接形式seo用
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据[一维的键=>值数组]
     * @author zouyan(305463219@qq.com)
     */
    public static function getChildListKeyVal(Request $request, Controller $controller, $pid, $oprateBit = 2 + 4, $notLog = 0){
        $parentData = static::getChildList($request, $controller, $pid, $oprateBit, $notLog);
        $department_list = $parentData['result']['data_list'] ?? [];
        return Tool::formatArrKeyVal($department_list, 'id', 'city_name');
    }
    /**
     * 获得列表数据--所有数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $pid 当前父id
     * @param int $oprateBit 操作类型位 1:获得所有的; 2 分页获取[同时有1和2，2优先]；4 返回分页html翻页代码 8 返回分页html翻页代码--a链接形式seo用
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getChildList(Request $request, Controller $controller, $pid, $oprateBit = 2 + 4, $notLog = 0){
        $company_id = $controller->company_id;

        // 获得数据
        $queryParams = [
            'where' => [
//                ['company_id', $company_id],
                ['parent_id', $pid],
            ],
            'select' => [
                'id','city_name','sort_num'
                //,'operate_staff_id','operate_staff_history_id'
            ],
            'orderBy' => static::$orderBy,// ['sort_num'=>'desc','id'=>'asc'],
        ];// 查询条件参数
        // $relations = ['CompanyInfo'];// 关系
        $relations = '';//['CompanyInfo'];// 关系
        $result = static::getBaseListData($request, $controller, '', $queryParams, $relations , $oprateBit, $notLog);
        // 格式化数据
        $data_list = $result['data_list'] ?? [];
        RelationDB::resolvingRelationData($data_list, $relations);// 根据关系设置，格式化数据
//        foreach($data_list as $k => $v){
//            // 公司名称
//            $data_list[$k]['company_name'] = $v['company_info']['company_name'] ?? '';
//            if(isset($data_list[$k]['company_info'])) unset($data_list[$k]['company_info']);
//        }
        $result['data_list'] = $data_list;
        return ajaxDataArr(1, $result, '');
    }
    // ***********通过组织条件获得kv***结束************************************************************

    // ***********相关表数据获取配置及数据的格式化***开始************************************************************

    /**
     * 表数据关系处理
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $data_list  一维或二维数据 当前需要处理的
     * @param string $funKey  功能关键字下标 -- 可为空:不返回功能配置，返回页面配置
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return array   新增的字段 一维数组 新加入的字段['字段名1' => '字段名1' ]
     * @author zouyan(305463219@qq.com)
     */
    public static function formatRelationList(Request $request, Controller $controller, &$data_list = [], $relationConfig = []){

        /**
         *
        $relationConfig = [
            'relation_key' => [// 下标为定义的关键字
                // 获得数据相关的
                'toClass' => 'App\Business\Controller\API\QualityControl\CTAPIStaffBusiness',// 对应的类--必填
                'toObjFormatListMethod' => '',//  定义关系表数据列表静态方法名 【用这个比较好】-- 可填 ，参数 $request, $controller , &$data_list[多条条主记录] , &$toDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
                'toObjFormatInfoMethod' => '',//  定义关系表数据详情静态方法名 【一般不用这个--存在重复处理】-- 可填 ，可以提前对数据进行格式化处理--特别处理 参数 $request, $controller , &$info[单条主记录] , &$temDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
                'extParams' => [// 可填
                    'sqlParams' => [
                        'where' => [// -- 可填 如 默认条件 'type_id' => 5  'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']
                            ['type_id', 5],
                        ],
                    ],
                    'handleKeyArr' => [],// 可填 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
                    'formatDataUbound' => [// 可填  格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
                        'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
                        'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
                        'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
                    ]
                ],
                'fieldRelations' => [// 字段对应 1 个或多个字段--必填
                    'staff_id' => 'id'// 原表的字段 =》 对应表的字段
                ],
                // 特别注明：数据格式化的步聚是： 1、listHandleKeyArr  对列表数据进行特别处理 ；2 return_data 关系配置设置的方式处理、；3、infoHandleKeyArr 对单条记录特别处理的关键字
                'listHandleKeyArr' => [],// 对列表数据进行特别处理的关键字  - 维数组，, 具体的业务逻辑需要自己重写方法 handleRelationDataFormat
                'infoHandleKeyArr' => [],// 对单条记录的 关系数据 特别处理的关键字 一维数组, 具体的业务逻辑需要自己重写方法 infoRelationFormatExtend
                'fieldEmptyQuery' => false, // 如果参数字段值都为空时，是否还查询数据 true:查询 ；false:不查[默认]-- 选填
                'relations' => [],// 关系--选填-- 一般不用了
                // 下面是对数据进行解析的处理
                'relationType' => 2,// 1：1:1 还是 2： 1:n 的关系 [默认]
                'return_data' => [// 对数据进行格式化
                    'old_data' => [// --只能一维数组 原数据的处理及说明
                        'ubound_operate' => 2,// 原数据的处理1 保存原数据及下标-如果下级有新字段，会自动更新;2不保存原数据[默认]---是否用新的下标由下面的 'ubound_name' 决定
                        // 第一次缩小范围，需要的字段  -- 要获取的下标数组 -维 [ '新下标名' => '原下标名' ]  ---为空，则按原来的返回
                        // 如果新下标和原下标相同，则可以用这个方法去转  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile']), true )
                        'ubound_name' => 'city',// 新数据的下标--可为空，则不返回,最好不要和原数据下标相同，如果相同，则原数据会把新数据覆盖
                        'fields_arr' => [],// 需要获得的字段；
                        'ubound_keys' => [],// 如果是结果是二维数组，下标要改为指定的字段值，下标[多个值_分隔]  ---这个是字段的一维数组
                        'ubound_type' =>1, 数组字段为下标时，按字段值以应的下标的 数组是一维还是二维 1一维数组【默认】; 2二维数组
                    ],
                    // 一/二维数组 键值对 可为空或没有此下标：不需要 Tool::formatArrKeyVal($areaCityList, $kv['key'], $kv['val'])
                    'k_v' => ['key' => 'id', 'val' => 'person_name', 'ubound_name' => '下标名称'],
                    // 一/二维数组 只要其中的某一个字段：
                    'one_field' => ['key' => 'id', 'return_type' => "返回类型1原数据['字段值'][一维返回一维数组，二维返回一维数组];2按分隔符分隔的字符", 'ubound_name' => '下标名称', 'split' => '、'],
                    一/二维数组 -- 只针对关系是 1:1的 即 关系数据是一维数组的情况--目的是平移指定字段到上一层
                  如果新下标和原下标相同，则可以用这个方法去转  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile']), true )
                    'fields_merge' => [ '新下标名' => '原下标名' ] 一维 或 [[ '新下标名' => '原下标名' ], ...] 二维数组
                    // 一/二维数组 获得指定的多个字段值
                   'many_fields' =>[ 'ubound_name' => '', 'fields_arr'=> [ '新下标名' => '原下标名' ],'reset_ubound' => 2;// 是否重新排序下标 1：重新０.．． ,'ubound_keys' => ['说明：看上面old_data的同字段说明'], 'ubound_type' =>1],ubound_type说明：看上面old_data的同字段说明
                  ],

                'relationConfig' => [// 下一个关系

                ]
            ]
        ];
         *
         *
         */
        $returnFields =[];
        if(empty($data_list) || empty($relationConfig)) return $returnFields;

        // 如果是一维数组，则转为二维数组
        $isMulti = Tool::isMultiArr($data_list, true);

        $isNeedHandle = false;// 是否真的需要遍历处理数据 false:不需要：true:需要 ；只要有一个需要处理就标记

        $dataFieldVals = [];// 缓存的字段及值数组-- 单个字段的 = 》 值【1或多个】

        $relationDataList = [];// 关系获取到的数据数组 下标为: 关系配置的key

        foreach($relationConfig as $k => $relationInfo){

            $toObjClass = $relationInfo['toClass'];// 对应的类
            // 定义关系表数据列表静态方法名 【用这个比较好】 -- 可填 ，可以提前对数据进行格式化处理--特别处理 参数 $request, $controller , &$data_list[多条条主记录] , &$toDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
            $toObjFormatListMethod = $relationInfo['toObjFormatListMethod'] ?? '';// 自定义的对关系表数据处理的静态方法名
            $extParams = $relationInfo['extParams'] ?? [];// 其它扩展参数
            // 1：1:1 还是 2： 1:n 的关系 [默认]
            $relationType = $relationInfo['relationType'] ?? 2;
            $fieldRelations = $relationInfo['fieldRelations'] ?? [];// 字段对应 1 个或多个字段   原表的字段 =》 对应表的字段
            if(empty($fieldRelations)) continue;
            $fieldEmptyQuery = $relationInfo['fieldEmptyQuery'] ?? false;// 如果参数字段值都为空时，是否还查询数据 true:查询 ；false:不查[默认]
            $relations = $relationInfo['relations'] ?? [];// 关系
            $listHandleKeyArr = $relationInfo['listHandleKeyArr'] ?? [];// 对列表数据进行特别处理的关键字  - 维数组，, 具体的业务逻辑需要自己重写方法 handleRelationDataFormat
            $relationChildConfig = $relationInfo['relationConfig'] ?? [];// 下一级关系
            $fieldValParams = [];
            foreach($fieldRelations as $f_field => $t_field){
                if(isset($dataFieldVals[$f_field])) {
                    $f_field_vals = $dataFieldVals[$f_field];
                }else{
                    $f_field_vals = array_values(array_unique(array_column($data_list, $f_field)));// 值数组 一维 [1,2,3]
                    $dataFieldVals[$f_field] = $f_field_vals;
                }

                $fieldValParams[$t_field] = $f_field_vals;// ['id' => [1,2,3]]
            }
            // 字段处理
            $needFields = array_values($fieldRelations);
            // 如果存在下级关系
            if(!empty($relationChildConfig)){
                foreach($relationChildConfig as $t_k => $t_childConfig){
                    $childFieldRelations = $t_childConfig['fieldRelations'];// 字段对应 1 个或多个字段   原表的字段 =》 对应表的字段
                    if(!empty($childFieldRelations)) $needFields = array_values(array_unique(array_merge($needFields, array_keys($childFieldRelations))));
                }
            }

            $includeUboundArr = $relationInfo['extParams']['formatDataUbound']['includeUboundArr'] ?? [];
            if(!empty($includeUboundArr)){// 加入需要用到的字段
                $includeUboundArr = array_merge($includeUboundArr, Tool::arrEqualKeyVal($needFields));
                $relationInfo['extParams']['formatDataUbound']['includeUboundArr'] = $includeUboundArr;
            }
            $exceptUboundArr = $relationInfo['extParams']['formatDataUbound']['exceptUboundArr'] ?? [];
            if(!empty($exceptUboundArr)){// 去掉排除的字段
                $exceptUboundArr = array_values(array_diff($exceptUboundArr, $needFields));
                $relationInfo['extParams']['formatDataUbound']['exceptUboundArr'] = $exceptUboundArr;
            }

//            if($toObjClass == 'App\Business\Controller\API\QualityControl\CTAPIAbilityJoinItemsResultsBusiness'){
////                // pr($extParams);
////                pr($fieldValParams);
////            }
            $toDataList =  $toObjClass::getFVFormatList( $request,  $controller, 1, 1, $fieldValParams, $fieldEmptyQuery, $relations, $extParams);
            if(!$isNeedHandle && !empty($toDataList)) $isNeedHandle = true;
            if(!empty($toDataList)){
                if(!empty($relationChildConfig)){
                    $temNextAddFields = static::formatRelationList($request, $controller, $toDataList, $relationChildConfig);
                    if(!empty($temNextAddFields)){// 下一级增加了的字段
                        $tem_fields_arr = $relationInfo['return_data']['old_data']['fields_arr'] ?? [];
                        $tem_append_fields = Tool::arrEqualKeyVal($temNextAddFields);
                        if(!empty($tem_fields_arr)) $relationConfig[$k]['return_data']['old_data']['fields_arr']= array_merge($tem_fields_arr, $tem_append_fields);// $temNextAddFields );
                        // fields_merge
                        $tem_fields_merge = $relationInfo['return_data']['fields_merge'] ?? [];
                        if(!empty($tem_fields_merge)){
                            $relationInfo['return_data']['fields_merge'] = Tool::arrAppendKeys($tem_fields_merge, $tem_append_fields);
                        }
                        // many_fields
                        $tem_many_fields = $relationInfo['return_data']['many_fields'] ?? [];
                        if(!empty($tem_many_fields)){
                            // 不是二维数组，则转为二维数组
                            Tool::isMultiArr($tem_many_fields, true);
                            foreach($tem_many_fields as $tem_mf_key => $tem_mf_info){
                                $tem_fields_arr = $tem_mf_info['fields_arr'] ?? [];//   -维[ '新下标名' => '原下标名' ]
                                if(!empty($tem_fields_arr)) $tem_many_fields[$tem_mf_key]['fields_arr'] = Tool::arrAppendKeys($tem_fields_arr, $tem_append_fields);
                            }
                            $relationInfo['return_data']['many_fields'] = $tem_many_fields;
                        }
                    }
                }
                // toObjFormatListMethod 【一般不用这个--存在重复处理】 定义静态方法名，可以提前对数据进行格式化处理--特别处理
                //          参数 $request, $controller , &$data_list[多条条主记录] , &$toDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
                if(!empty($toObjFormatListMethod)) $toObjClass::{$toObjFormatListMethod}($request, $controller, $data_list, $toDataList, $returnFields);
                // 当前类对数据列表进行特别处理【自定义】

                // 如果是一维数组，则转为二维数组
                $temIsMulti = Tool::isMultiArr($toDataList, true);
                $toObjClass::handleRelationDataFormat($request, $controller, $data_list, $toDataList, $listHandleKeyArr, $returnFields);
                if(!$temIsMulti) $toDataList = $toDataList[0] ?? [];
                // 对数据进行格式化-- 变为 一维或二维数组--数组下标为关系字段值，多个用_分隔
                $relationDataList[$k] = Tool::arrUnderReset($toDataList, array_values($fieldRelations), $relationType, '_');
            }

//        if(!empty($projectStandardsList)) $projectStandardsArr = Tool::arrUnderReset($projectStandardsList, 'ability_id', 2);
//        if(!$isNeedHandle && !empty($projectStandardsArr)) $isNeedHandle = true;
        }


        // 对数据进行格式化操作
        // 改为不返回，好让数据下面没有数据时，有一个空对象，方便前端或其它应用处理数据
        // if(!$isNeedHandle){// 不处理，直接返回 // if(!$isMulti) $data_list = $data_list[0] ?? [];
        //    return true;
        // }

        foreach($data_list as $k => $v){
           static::formatRelationInfo($request, $controller, $data_list[$k], $relationConfig, $relationDataList, $returnFields);
        }

        if(!$isMulti) $data_list = $data_list[0] ?? [];
        return $returnFields;
    }

    /**
     * 对数据进行格式化
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $info  单条数据  --- 一维数组
     * @param array $relationConfigArr  配置信息 --  return_data为空，则按 下标key返回
     *
     * @param array $relationDataList  配置需要格式化的相关数据  一维或二维数组    下标为配置 $relationConfigArr 对应的key   key => 它的值为一维或二维数组
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     * @return array  新增的字段 一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function formatRelationInfo(Request $request, Controller $controller, &$info, $relationConfigArr, $relationDataList, &$returnFields){
        /**
         *
        $relationConfigArr = [
            'relation_key' => [// 下标为定义的关键字
                // 获得数据相关的
                'toClass' => 'App\Business\Controller\API\QualityControl\CTAPIStaffBusiness',// 对应的类--必填
                'toObjFormatListMethod' => '',//  定义关系表数据列表静态方法名 【用这个比较好】-- 可填 ，参数 $request, $controller , &$data_list[多条条主记录] , &$toDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
                'toObjFormatInfoMethod' => '',//  定义关系表数据详情静态方法名 【一般不用这个--存在重复处理】-- 可填 ，可以提前对数据进行格式化处理--特别处理 参数 $request, $controller , &$info[单条主记录] , &$temDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
                'extParams' => [// 可填
                    'sqlParams' => [
                        'where' => [// -- 可填 如 默认条件 'type_id' => 5  'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']
                            ['type_id', 5],
                        ],
                    ],
                    'handleKeyArr' => [],// 可填 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
                    'formatDataUbound' => [// 可填  格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
                        'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
                        'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
                        'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
                    ]
                ],
                'fieldRelations' => [// 字段对应 1 个或多个字段--必填
                    'staff_id' => 'id'// 原表的字段 =》 对应表的字段
                ],
                // 特别注明：格式化的数据是 toClass 对象的数据；数据格式化的步聚是： 1、listHandleKeyArr  对列表数据进行特别处理 ；2 return_data 关系配置设置的方式处理、；3、infoHandleKeyArr 对单条记录特别处理的关键字
                'listHandleKeyArr' => [],// 对列表数据进行特别处理的关键字  - 维数组，, 具体的业务逻辑需要自己重写方法 handleRelationDataFormat
                'infoHandleKeyArr' => [],// 对单条记录 关系数据 特别处理的关键字 一维数组, 具体的业务逻辑需要自己重写方法 infoRelationFormatExtend
                'fieldEmptyQuery' => false, // 如果参数字段值都为空时，是否还查询数据 true:查询 ；false:不查[默认]-- 选填
                'relations' => [],// 关系--选填-- 一般不用了
                // 下面是对数据进行解析的处理
                'relationType' => 2,// 1：1:1 还是 2： 1:n 的关系 [默认]
                'return_data' => [// 对数据进行格式化
                    'old_data' => [// --只能一维数组 原数据的处理及说明
                        'ubound_operate' => 2,// 原数据的处理1 保存原数据及下标-如果下级有新字段，会自动更新;2不保存原数据[默认]---是否用新的下标由下面的 'ubound_name' 决定
                        // 第一次缩小范围，需要的字段  -- 要获取的下标数组 -维 [ '新下标名' => '原下标名' ]  ---为空，则按原来的返回
                        // 如果新下标和原下标相同，则可以用这个方法去转  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile']), true )
                        'ubound_name' => 'city',// 新数据的下标--可为空，则不返回,最好不要和原数据下标相同，如果相同，则原数据会把新数据覆盖
                        'fields_arr' => [],// 需要获得的字段；
                       'ubound_keys' => [],// 如果是结果是二维数组，下标要改为指定的字段值，下标[多个值_分隔]  ---这个是字段的一维数组
                       'ubound_type' =>1, 数组字段为下标时，按字段值以应的下标的 数组是一维还是二维 1一维数组【默认】; 2二维数组
                    ],
                    // 一/二维数组 键值对 可为空或没有此下标：不需要 Tool::formatArrKeyVal($areaCityList, $kv['key'], $kv['val'])
                    'k_v' => ['key' => 'id', 'val' => 'person_name', 'ubound_name' => '下标名称'],
                    // 一/二维数组 只要其中的某一个字段：
                    'one_field' => ['key' => 'id', 'return_type' => "返回类型1原数据['字段值'][一维返回一维数组，二维返回一维数组];2按分隔符分隔的字符", 'ubound_name' => '下标名称', 'split' => '、'],
                    一/二维数组 -- 只针对关系是 1:1的 即 关系数据是一维数组的情况--目的是平移指定字段到上一层
                    如果新下标和原下标相同，则可以用这个方法去转  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile']), true )
                     'fields_merge' => [ '新下标名' => '原下标名' ] 一维 或 [[ '新下标名' => '原下标名' ], ...] 二维数组
                    // 一/二维数组 获得指定的多个字段值
                   'many_fields' =>[ 'ubound_name' => '', 'fields_arr'=> [ '新下标名' => '原下标名' ],'reset_ubound' => 2;// 是否重新排序下标 1：重新０.．．  ,'ubound_keys' => ['说明：看上面old_data的同字段说明'], 'ubound_type' =>1],ubound_type说明：看上面old_data的同字段说明
                ],

                'relationConfig' => [// 下一个关系

                ]
            ]
        ];
         *
         */

        if(empty($relationConfigArr)) return $returnFields;//  || empty($relationDataList)
        if(empty($info)) return $returnFields;
        // $relationKeys = array_keys($relationConfigArr);
        foreach($relationConfigArr as $k => $relationInfo){
            $temDataList = $relationDataList[$k] ?? [];// 获得的关系数据 -- 一维、二维数组

            $toObjClass = $relationInfo['toClass'];// 对应的类
            // 定义静态方法名 -- 可填 ，可以提前对数据进行格式化处理--特别处理 参数 $request, $controller , &$info[单条主记录] , &$temDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
            $toObjFormatInfoMethod = $relationInfo['toObjFormatInfoMethod'] ?? '';// 自定义的对关系表数据处理的静态方法名

            $infoHandleKeyArr = $relationInfo['infoHandleKeyArr'] ?? [];// 对单条记录特别处理的关键字 一维数组, 具体的业务逻辑需要自己重写方法 infoRelationFormatExtend

            $return_data = $relationInfo['return_data'] ?? [];// 数据处理配置
            // 1：1:1 还是 2： 1:n 的关系 [默认]
            // $relationType = $relationInfo['relationType'] ?? 2;

            $fieldRelations = $relationInfo['fieldRelations'];// 字段对应 1 个或多个字段   原表的字段 =》 对应表的字段

            $relationfieldKeys = array_keys($fieldRelations);// 主记录的关系字段数组 --- 一维数组

            // 获得主字段的值
            if(empty($relationfieldKeys)) continue;
            $temDataList = $temDataList[implode('_', Tool::getArrFormatFields($info, $relationfieldKeys, true))] ?? [];
            // if(empty($temDataList)) continue;
            // toObjFormatInfoMethod 【一般不用这个--存在重复处理】 定义静态方法名，可以提前对数据进行格式化处理--特别处理
            //          参数 $request, $controller , &$info[单条主记录] , &$temDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
            if(!empty($toObjFormatInfoMethod)) $toObjClass::{$toObjFormatInfoMethod}($request, $controller, $info, $temDataList, $returnFields);
            // 单条记录 单个配置 、数据的格式化
            Tool::formatConfigRelationInfo($info, $temDataList, $k, $return_data, $returnFields);
            // 自已类的格式化处理
            $toObjClass::infoRelationFormatExtend($request, $controller, $info, $temDataList, $infoHandleKeyArr, $returnFields);

        }

        return $returnFields;
    }

    // ***********各表自己***特殊的数据处理方法******需要重写*************开始**************************************
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

//        if(in_array('mergeZeroName', $handleKeyArr)){
//          // ...对 $data_list做特殊处理
//        }

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

//        if(in_array('company', $infoHandleKeyArr)){
//
//        }

        return $returnFields;
    }
    // ***********各表自己***特殊的数据处理方法******需要重写*************结束**************************************

    // ~~~~~~~~~~配置相关的~~~~~~~~~~

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
//                    static::getUboundRelation($relationArr, 'company_info')
//                    // ['class_name' => '', 'staff_info' => ''] -- 新格式  或 ['class_name', 'staff_info'] -- 老格式
//                    ,
//                    static::getUboundRelationExtendParams($extendParams, 'company_info'))
//
////                [
////                    // 获得班级名称
////                    'class_name' => CTAPICourseClassBusiness::getTableRelationConfigInfo($request, $controller
////                        , ['class_id' => 'id']
////                        , 1, 2
////                        ,'','', [], [], '', []),
////                    // 获得用户信息
////                    'staff_info' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
////                        , ['staff_id' => 'id']
////                        , 1, 256
////                        ,'','', [], [], '', []),
////                ]
//                ,
//                ['where' => [['admin_type', 2]]], '', []),
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

    // 配置基类
    /**
     * 获得主键的关系配置信息【单个配置】 调用 具体的类::此方法 --- 通过自己的类调就可以自动获得当前的类名
     *  --- 有下标， =》 配置数组
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $primaryKey 配置的下标
     * @param array $fieldRelations 字段关系数组 ['admin_type' => 'admin_type','staff_id' => 'id']
     * [// 字段对应 1 个或多个字段--必填
     *       'admin_type' => 'admin_type',
     *      'staff_id' => 'id'// 原表的字段 =》 对应表的字段
     *  ]
     * @param int $relationType 1：1:1 还是 2： 1:n 的关系 [默认]
     * @param array $return_data // 对数据进行格式化 --  为空，则按  $primaryKey 为下标返回数据  ；具体格式参见 Tool::formatConfigRelationInfo 参数说明
     *                                   也可以参见：formatRelationList 方法的参数说明
     * @param string $toObjFormatListMethod 定义关系表数据列表静态方法名 【用这个比较好】-- 可填 ，参数 $request, $controller , &$data_list[多条条主记录] , &$toDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
     * @param string $toObjFormatInfoMethod  定义关系表数据详情静态方法名 【一般不用这个--存在重复处理】-- 可填 ，可以提前对数据进行格式化处理--特别处理 参数 $request, $controller , &$info[单条主记录] , &$temDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
     * @param array $childRelationConfig  下一级关系
     * @param array $sqlParams  其它 sql条件，主要用  where : [ 'where' => ['admin_type', 2],] //  [// -- 可填 如 默认条件 'type_id' => 5  'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']

     *        [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
     *           'where' => '如果有值，则替换where'
     *           'select' => '如果有值，则替换select'
     *           'orderBy' => '如果有值，则替换orderBy'
     *           'whereIn' => '如果有值，则替换whereIn'
     *           'whereNotIn' => '如果有值，则替换whereNotIn'
     *           'whereBetween' => '如果有值，则替换whereBetween'
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween'
     *       ],
     * @param string $toClass 可为空，通过自己的类调就可以自动获得当前的类名
     * @param array $extendParams  扩展参数---可能会用--优先级最高
     * [
     *   'extendConfig' => [],// 扩展配置--具体有哪些参数可参见 formatRelationList 方法的配置下标
     *                          如 ：['listHandleKeyArr' => [..],'infoHandleKeyArr' => [],]
     * ]
     * @return  array 表关系配置信息
     * @author zouyan(305463219@qq.com)
     */
    public static function getPrimaryRelationConfigs(Request $request, Controller $controller, $primaryKey, $fieldRelations, $relationType = 2, $return_data = [], $toObjFormatListMethod = '', $toObjFormatInfoMethod = '', $childRelationConfig = [], $sqlParams = [], $toClass = '', $extendParams = []){

        $user_info = $controller->user_info;
        $user_id = $controller->user_id;
        $user_type = $controller->user_type;

        if(empty($toClass)) $toClass = static::$record_class;
        $primaryRelationConfig = [];
        $primaryRelationConfig[$primaryKey] = static::getPrimaryRelationConfigVal($request, $controller, $fieldRelations, $relationType, $return_data, $toObjFormatListMethod, $toObjFormatInfoMethod, $childRelationConfig, $sqlParams, $toClass, $extendParams);
        return $primaryRelationConfig;
    }

    /**
     * 获得主键的关系配置信息【单个配置】 调用 具体的类::此方法 --- 通过自己的类调就可以自动获得当前的类名 --- 没有下标，只是配置数组
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $fieldRelations 字段关系数组 ['admin_type' => 'admin_type','staff_id' => 'id']
     * [// 字段对应 1 个或多个字段--必填
     *       'admin_type' => 'admin_type',
     *      'staff_id' => 'id'// 原表的字段 =》 对应表的字段
     *  ]
     * @param int $relationType 1：1:1 还是 2： 1:n 的关系 [默认]
     * @param array $return_data // 对数据进行格式化 --  为空，则按  $primaryKey 为下标返回数据  ；具体格式参见 Tool::formatConfigRelationInfo 参数说明
     *                                   也可以参见：formatRelationList 方法的参数说明
     * @param string $toObjFormatListMethod 定义关系表数据列表静态方法名 【用这个比较好】-- 可填 ，参数 $request, $controller , &$data_list[多条条主记录] , &$toDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
     * @param string $toObjFormatInfoMethod  定义关系表数据详情静态方法名 【一般不用这个--存在重复处理】-- 可填 ，可以提前对数据进行格式化处理--特别处理 参数 $request, $controller , &$info[单条主记录] , &$temDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
     * @param array $childRelationConfig  下一级关系
     * @param array $sqlParams  其它 sql条件，主要用  where : [ 'where' => ['admin_type', 2],] //  [// -- 可填 如 默认条件 'type_id' => 5  'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']

     *        [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
     *           'where' => '如果有值，则替换where'
     *           'select' => '如果有值，则替换select'
     *           'orderBy' => '如果有值，则替换orderBy'
     *           'whereIn' => '如果有值，则替换whereIn'
     *           'whereNotIn' => '如果有值，则替换whereNotIn'
     *           'whereBetween' => '如果有值，则替换whereBetween'
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween'
     *       ],
     * @param string $toClass 可为空，通过自己的类调就可以自动获得当前的类名
     * @param array $extendParams  扩展参数---可能会用--优先级最高
     * [
     *   'extendConfig' => [],// 扩展配置--具体有哪些参数可参见 formatRelationList 方法的配置下标
     *                          如 ：['listHandleKeyArr' => [..],'infoHandleKeyArr' => [],]
     * ]
     * @return  array 表关系配置信息
     * @author zouyan(305463219@qq.com)
     */
    public static function getPrimaryRelationConfigVal(Request $request, Controller $controller, $fieldRelations, $relationType = 2, $return_data = []
        , $toObjFormatListMethod = '', $toObjFormatInfoMethod = '', $childRelationConfig = [], $sqlParams = []
        , $toClass = '', $extendParams = [])
    {
        $user_info = $controller->user_info;
        $user_id = $controller->user_id;
        $user_type = $controller->user_type;

        if(empty($toClass)) $toClass = static::$record_class;
        $extendConfig = $extendParams['extendConfig'] ?? [];

        $returnConfig = [// 获得相关的  企业名称 company_name
            'toClass' => $toClass,// 'App\Business\Controller\API\QualityControl\CTAPIStaffBusiness',
            'toObjFormatListMethod' => $toObjFormatListMethod,//  定义关系表数据列表静态方法名 【用这个比较好】-- 可填 ，参数 $request, $controller , &$data_list[多条条主记录] , &$toDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
            'toObjFormatInfoMethod' => $toObjFormatInfoMethod,//  定义关系表数据详情静态方法名 【一般不用这个--存在重复处理】-- 可填 ，可以提前对数据进行格式化处理--特别处理 参数 $request, $controller , &$info[单条主记录] , &$temDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
            // 'extParams' => ['sqlParams' => $sqlParams],
//            [// 可填
//                'sqlParams' => [
//                    'where' => [// -- 可填 如 默认条件 'type_id' => 5  'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']
//                        ['admin_type', 2],
//                    ],
//                ],
//            ],
            'fieldRelations' => $fieldRelations,
//            [// 字段对应 1 个或多个字段--必填
//                'admin_type' => 'admin_type',
//                'staff_id' => 'id'// 原表的字段 =》 对应表的字段
//            ],
            'relationType' => $relationType,// 1,// 1：1:1 还是 2： 1:n 的关系 [默认]
            'return_data' => $return_data,
//            [// 对数据进行格式化
//                // 一/二维数组 只要其中的某一个字段：
//                'one_field' =>['key' => 'company_name', 'return_type' => 2, 'ubound_name' => 'company_name', 'split' => '、'],
//            ],
            'relationConfig' => $childRelationConfig// 下一个关系
        ];
        if(!empty($sqlParams)) $returnConfig['extParams']['sqlParams'] = $sqlParams;
        return (empty($extendConfig)) ? $returnConfig : array_merge($returnConfig, $extendConfig);
    }

    /**
     * 获得主键的关系配置信息【单个配置】 调用 具体的类::此方法 --- 通过自己的类调就可以自动获得当前的类名 --- 没有下标，只是配置数组
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $fieldRelations 字段关系数组 ['admin_type' => 'admin_type','staff_id' => 'id']
     * [// 字段对应 1 个或多个字段--必填
     *       'admin_type' => 'admin_type',
     *      'staff_id' => 'id'// 原表的字段 =》 对应表的字段
     *  ]
     * @param int $relationType 1：1:1 还是 2： 1:n 的关系 [默认]
     * @param int $return_num 指定要获得的关系数据类型格式化后的数据 编号 1[占用：原数据] 2 4 8..
     * @param string $toObjFormatListMethod 定义关系表数据列表静态方法名 【用这个比较好】-- 可填 ，参数 $request, $controller , &$data_list[多条条主记录] , &$toDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
     * @param string $toObjFormatInfoMethod  定义关系表数据详情静态方法名 【一般不用这个--存在重复处理】-- 可填 ，可以提前对数据进行格式化处理--特别处理 参数 $request, $controller , &$info[单条主记录] , &$temDataList[关系表记录-一维或二维], &$returnFields[在主记录中新生成的下标]
     * @param array $childRelationConfig  下一级关系
     * @param array $sqlParams  其它 sql条件，主要用  where : [ 'where' => ['admin_type', 2],] //  [// -- 可填 如 默认条件 'type_id' => 5  'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']

     *        [// 其它sql条件[覆盖式],下面是常用的，其它的也可以
     *           'where' => '如果有值，则替换where'
     *           'select' => '如果有值，则替换select'
     *           'orderBy' => '如果有值，则替换orderBy'
     *           'whereIn' => '如果有值，则替换whereIn'
     *           'whereNotIn' => '如果有值，则替换whereNotIn'
     *           'whereBetween' => '如果有值，则替换whereBetween'
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween'
     *       ],
     * @param string $toClass 可为空，通过自己的类调就可以自动获得当前的类名
     * @param array $extendParams  扩展参数---可能会用--优先级最高
     * [
     *   'extendConfig' => [],// 扩展配置--具体有哪些参数可参见 formatRelationList 方法的配置下标
     *                          如 ：['listHandleKeyArr' => [..],'infoHandleKeyArr' => [],]
     * ]
     * @return  array 表关系配置信息
     * @author zouyan(305463219@qq.com)
     */
    public static function getTableRelationConfigInfo(Request $request, Controller $controller, $fieldRelations, $relationType = 2
        , $return_num = 0, $toObjFormatListMethod = '', $toObjFormatInfoMethod = '', $childRelationConfig = [], $sqlParams = []
        , $toClass = '', $extendParams = []){

        // ['one_field' =>['key' => 'company_name', 'return_type' => 2, 'ubound_name' => 'company_name', 'split' => '、'],]
        $return_data = static::getRelationConfigReturnData($request, $controller, $return_num); // 对数据进行格式化 --  为空，则按  $primaryKey 为下标返回数据  ；具体格式参见 Tool::formatConfigRelationInfo 参数说明  也可以参见：formatRelationList 方法的参数说明
        // $sqlParams = ['where' => [['admin_type', 2]]];

        return static::getPrimaryRelationConfigVal($request, $controller, $fieldRelations
            , $relationType
            , $return_data
            ,$toObjFormatListMethod, $toObjFormatInfoMethod
            ,$childRelationConfig, $sqlParams, $toClass, $extendParams);
    }


    // ***********相关表数据获取配置及数据的格式化***结束************************************************************
}
