<?php
// 点播课程视频目录
namespace App\Business\Controller\API\QualityControl;

use App\Models\QualityControl\VodVideo;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPIVodVideoBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\VodVideoAPI';
    public static $table_name = 'vod_video';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    public static $orderBy = VodVideo::ORDER_BY;// ['vod_id' => 'desc', 'sort_num' => 'desc', 'id' => 'asc'];// 默认的排序字段数组 ['id' => 'desc']--默认 或 ['sort_num' => 'desc', 'id' => 'desc']


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
            // 获得封面图
            'resource_list' => CTAPIResourceBusiness::getTableRelationConfigInfo($request, $controller
                , ['resource_id' => 'id']
                , 2, 0
                ,'','',
                CTAPIResourceBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'resource_list'),
                    static::getUboundRelationExtendParams($extendParams, 'resource_list')),
                static::getRelationSqlParams([], $extendParams, 'resource_list'), '', ['extendConfig' => ['listHandleKeyArr' => ['format_resource']]]),// , 'infoHandleKeyArr' => ['resource_list']

            // 获得视频
            'resource_list_video' => CTAPIResourceBusiness::getTableRelationConfigInfo($request, $controller
                , ['resource_id_video' => 'id']
                , 2, 0
                ,'','',
                CTAPIResourceBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'resource_list_video'),
                    static::getUboundRelationExtendParams($extendParams, 'resource_list_video')),
                static::getRelationSqlParams([], $extendParams, 'resource_list_video'), '', ['extendConfig' => ['listHandleKeyArr' => ['format_resource']]]),// , 'infoHandleKeyArr' => ['resource_list']

            // 上传的资料信息--附件资料
            'resource_list_courseware' => CTAPIResourceBusiness::getTableRelationConfigInfo($request, $controller
                , ['id' => 'column_id']
                , 2, 0
                ,'','',
                CTAPIResourceBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'resource_list_courseware'),
                    static::getUboundRelationExtendParams($extendParams, 'resource_list_courseware')),
                static::getRelationSqlParams(['where' => [['column_type', 65536]]], $extendParams, 'resource_list_courseware'), ''
                , ['extendConfig' => ['listHandleKeyArr' => ['format_resource']]]),// , 'infoHandleKeyArr' => ['resource_list']
            // 获得课程名称
            'vod_name' => CTAPIVodsBusiness::getTableRelationConfigInfo($request, $controller
                , ['vod_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIVodsBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'vod_name'),
                    static::getUboundRelationExtendParams($extendParams, 'vod_name')),
                static::getRelationSqlParams([], $extendParams, 'vod_name'), '', []),
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

        if(($return_num & 2) == 2){// 给上一级返回名称 video_name 下标
            $one_field = ['key' => 'video_name', 'return_type' => 2, 'ubound_name' => 'video_name', 'split' => '、'];// 获得名称
            if(!isset($return_data['one_field'])) $return_data['one_field'] = [];
            array_push($return_data['one_field'], $one_field);
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

         $is_video = CommonRequest::get($request, 'is_video');
         if(strlen($is_video) > 0  && !in_array($is_video, [0, '-1']))  Tool::appendCondition($queryParams, 'is_video',  $is_video . '=' . $is_video, '&');

        $video_type = CommonRequest::get($request, 'video_type');
        if(strlen($video_type) > 0  && !in_array($video_type, [0, '-1']))  Tool::appendCondition($queryParams, 'video_type',  $video_type . '=' . $video_type, '&');

        $status_online = CommonRequest::get($request, 'status_online');
        if(strlen($status_online) > 0  && !in_array($status_online, [0, '-1']))  Tool::appendCondition($queryParams, 'status_online',  $status_online . '=' . $status_online, '&');

        $vod_id = CommonRequest::get($request, 'vod_id');
        if(strlen($vod_id) > 0 && !in_array($vod_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $vod_id, 'vod_id', [0, '0', ''], ',', false);

         $parent_video_id = CommonRequest::get($request, 'parent_video_id');
         if(strlen($parent_video_id) > 0 && !in_array($parent_video_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $parent_video_id, 'parent_video_id', [0, '0', ''], ',', false);

        $level_no = CommonRequest::get($request, 'level_no');
        if(strlen($level_no) > 0 && !in_array($level_no, [0, '-1']))  Tool::appendParamQuery($queryParams, $level_no, 'level_no', [0, '0', ''], ',', false);

        $resource_id = CommonRequest::get($request, 'resource_id');
        if(strlen($resource_id) > 0 && !in_array($resource_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $resource_id, 'resource_id', [0, '0', ''], ',', false);

        $resource_id_video = CommonRequest::get($request, 'resource_id_video');
        if(strlen($resource_id_video) > 0 && !in_array($resource_id_video, [0, '-1']))  Tool::appendParamQuery($queryParams, $resource_id_video, 'resource_id_video', [0, '0', ''], ',', false);

        $resource_id_courseware = CommonRequest::get($request, 'resource_id_courseware');
        if(strlen($resource_id_courseware) > 0 && !in_array($resource_id_courseware, [0, '-1']))  Tool::appendParamQuery($queryParams, $resource_id_courseware, 'resource_id_courseware', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

    /**
     * 根据课程id信息，获得课程及课程支付配置信息
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $courseId 所属课程id
     * @param int $level  当前的层级  层级1,2,3,4,5... 默认 1
     * @param int $id  记录id 0;读所有的；>0 :排除的id
     * @param int $status_online 上架状态(1正常(上架中)  2下架)  0所有的
     * @return array  以课程id为下标的二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getDirList(Request $request, Controller $controller, $courseId = 0, $level = 1, $id = 0, $status_online = 0){
        $fieldValParams = [
            'is_video' => 1
        ];
        if(is_numeric($courseId) && $courseId > 0){
            $fieldValParams['vod_id'] = $courseId;
        }
        if(is_numeric($status_online) && $status_online > 0){
            $fieldValParams['status_online'] = $status_online;
        }
        $handleKeyConfigArr = [];
        $extParams = [
            'useQueryParams' => false,
//            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> static::getRelationConfigs($request, $controller, $handleKeyConfigArr, []),
//            // 'infoHandleKeyArr' => ['resetPayMethod']
//            'listHandleKeyArr' => ['initPayMethodText', 'priceIntToFloat'],
            'sqlParams' => [
                'where' => [
                    ['level_no', '>=' , $level],
                 ],
                'orderBy' => ['level_no' => 'asc', 'sort_num' => 'desc', 'id' => 'asc'],// static::$orderBy,
            ]
        ];
        $dataList = static::getFVFormatList( $request,  $controller, 1, 1
            , $fieldValParams, false, [], $extParams);
        $levelDataList = Tool::getFormatLevelList($dataList, '|&nbsp;&nbsp;&nbsp;&nbsp;', '|__', $id,
            0, 'parent_video_id', $level, 'id', 'ids', 'level_no', 'video_name', '&nbsp;&nbsp;&nbsp;&nbsp;|', '__');

        return $levelDataList;
    }


    /**
     * 获得企业数据验证
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $reDataArr 可以传到前端的参数
     * @param string $url_path url地址的路径目录 如：jigou/list/
     * @param int $vod_id 所属点播课程 id
     * @param int $video_type 是否推荐1非推荐2推荐
     * @param int $pagesize 每页显示 的数量
     * @param int $page 当前页号
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array $keyArr 关键字数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getVodsList(Request $request, Controller $controller, &$reDataArr, $url_path = 'jigou/list/', $vod_id = 0, $video_type = 0, $pagesize = 20, $page = 1, $notLog = 0){

        $field = CommonRequest::get($request, 'field');
        $keyword = CommonRequest::get($request, 'keyword');
//        $company_grade = CommonRequest::get($request, 'company_grade');
        if(is_numeric($pagesize) && $pagesize >= 100) $pagesize = 100;// 限止最多每面100条记录
        if(is_numeric($pagesize) && $pagesize <= 0) $pagesize = 20;
        $pathParamStr = $vod_id . '_' . $video_type . '_' . $pagesize . '_{page}';// . $page;
        if($field != '' && $keyword != '') $pathParamStr .= '?field=' . $field . '&keyword=' . $keyword;

        // 加上会员类型参数
//        if(!empty($company_grade)) $pathParamStr .= ((strpos($pathParamStr, '?') === false) ? '?' : '&') . 'company_grade=' . $company_grade;

        $appParams = [
            'vod_type_id' => $vod_id,
            'video_type' => $video_type,
            'pagesize' => $pagesize,
            'page' => $page,
            // 'url_model' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/web/certificate/company/' . $pathParamStr,
            // 'url_model' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/jigou/list/' . $pathParamStr,
            'url_model' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/'. $url_path . $pathParamStr,
            'is_video' => 2,
//            'is_perfect' => 2,
            'status_online' => 1,
//            'account_status' => 1
        ];
        static::mergeRequest($request, $controller, $appParams);
        $reDataArr = array_merge($reDataArr, $appParams);
        $keyArr = [];
        $reDataArr['pagesize'] = $pagesize;
        $reDataArr['page'] = $page;
        $reDataArr['field'] = $field;
        $reDataArr['keyword'] = $keyword;

        // 获得点播课程名称
        $vod_name = '';
        if(is_numeric($vod_id) && $vod_id > 0){
            $vodInfo = CTAPIVodsBusiness::getInfoData($request, $controller, $vod_id, [], '', []);
            $vod_name = $vodInfo['vod_name'] ?? '';
            array_push($keyArr, $vod_name);
        }
        $reDataArr['vod_id'] = $vod_id;
        $reDataArr['vod_name'] = $vod_name;

        // 视频类型1上传视频；2网络视频地址 名称
        if(is_numeric($video_type) && $video_type > 0){
            $videoTypeArr = VodVideo::$videoTypeArr;
            $video_type_name = $videoTypeArr[$video_type] ?? '';
            if(!empty($video_type_name)) array_push($keyArr, $video_type_name);
        }

        // 获得会员等级
//        $company_grade_name = '';
//        if(!empty($company_grade)){
//            $companyGradeArr = Staff::$companyGradeArr;
//            $company_grade_name = $companyGradeArr[$company_grade] ?? '';
//            if(!empty($company_grade_name)) array_push($keyArr, $company_grade_name);
//        }else{
//            $company_grade_name = '所有';
//        }
//        $reDataArr['company_grade_name'] = $company_grade_name;

        if($field != '' && $keyword != '') array_push($keyArr, $keyword);

        $extParamsCompany = [
//            'sqlParams'=> [
//                'orderBy' => ['id' => 'asc']
//            ]
        ];
        $extParams = array_merge($extParamsCompany,[
            // , 'vod_content' => ''
            // , 'invoice_template_name' => '', 'invoice_project_template_name' => ''
            'relationFormatConfigs'=> static::getRelationConfigs($request, $controller, ['resource_list' => '', 'resource_list_video' => '', 'resource_list_courseware' => '', 'vod_name' => ''], []),
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            // 'relationFormatConfigs'=> CTAPIStaffBusiness::getRelationConfigs($request, $controller, ['industry_info', 'city_info'], []),// , 'extend_info'
        ]);
        $data_arr = static::getList($request, $controller, 2 + 8, [], [], $extParams)['result'] ?? [];
        $reDataArr['data_list'] = $data_arr['data_list'] ?? [];
        $reDataArr['pageInfoLink'] = $data_arr['pageInfoLink'] ?? '';
        return $keyArr;
    }
}
