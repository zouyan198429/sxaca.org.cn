<?php

namespace App\Business\Controller\API;

use App\Business\BaseBusiness;
use App\Models\BaseModel;
use App\Services\DB\CommonDB;
use App\Services\DBRelation\RelationDB;
use App\Services\Request\API\Sites\APIRunBuyRequest;
use App\Services\Excel\ImportExport;
use App\Services\Request\CommonRequest;
use App\Services\Response\Data\CommonAPIFromBusiness;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as Controller;

/**
 *
 */
class APIOperate extends BaseBusiness
{
    public static $database_model_dir_name = '';// 对应的数据库模型目录名称
    public static $model_name = '';// 中间层 App\Business\API 下面的表名称 API\RunBuy\CountSenderRegAPI
    public static $table_name = '';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

    // 具体的表，根据实际情况继承修改下面属性--与默认值不同时
    public static $primary_key = 'id';// 主键字段名 id --默认
    public static $orderBy = BaseModel::ORDER_BY;// ['id' => 'desc'];// 默认的排序字段数组 ['id' => 'desc']--默认 或 ['sort_num' => 'desc', 'id' => 'desc']

    // 根据数据模型名称，返回数据中间层对象
//    public static function getBusinessAPIObjByModelName($modelName, &$modelObj = null){
//        $className = "App\\Business\\API\\" . $modelName . 'Business';
//        if (! class_exists($className )) {
//            throws('参数[Model_name]不正确！');
//        }
//        $modelObj = new $className();
//        return $modelObj;
//    }

    // 实例化数据中间层对象
//    public static function requestGetObj(Request $request,&$modelObj = null){
//        if (! is_object($modelObj)) {
//              $modelName = static::$model_name;
////            $modelName = CommonRequest::get($request, 'Model_name');
////            Tool::judgeEmptyParams('Model_name', $modelName);
//
////            $className = "App\\Business\\DB\\RunBuy\\LrChinaCityDBBusiness" ;
////            if (! class_exists($className )) {
////                throws('参数[Model_name]不正确！');
////            }
////            $modelObj = new $className();
//            self::getBusinessAPIObjByModelName($modelName, $modelObj );
//        }
//        return $modelObj;
//    }

    // 实例化数据中间层对象
    public static function requestGetObj(Request $request, Controller $controller, &$modelObj = null){
        if (! is_object($modelObj)) {
//            $modelName = CommonRequest::get($request, 'Model_name');
//            Tool::judgeEmptyParams('Model_name', $modelName);
            $modelName = static::$model_name;

//            $className = "App\\Business\\DB\\RunBuy\\LrChinaCityDBBusiness" ;
//            if (! class_exists($className )) {
//                throws('参数[Model_name]不正确！');
//            }
//            $modelObj = new $className();
            CommonAPIFromBusiness::getBusinessObjByModelName($modelName, $modelObj );
        }
        return $modelObj;
    }
    /**
     * 获得列表数据--所有数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称 为空，则用对象的属性
     * @param string $queryParams 条件数组/json字符;
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
     *
     * @param string $relations 关系数组/json字符
     * @param int $oprateBit 操作类型位 1:获得所有的; 2 分页获取[同时有1和2，2优先]；4 返回分页html翻页代码 8 返回分页html翻页代码--a链接形式seo用
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
        $result = [
            'data_list'=>$resultDatas,//array(),//数据二维数组
            'total'=>$total,//总记录数 0:每次都会重新获取总数 ;$total :则>0总数据不会重新获取[除第一页]
            'page'=> $page,// 当前页
            'pagesize'=> $pagesize,// 每页显示的数量
            'totalPage'=> $totalPage,// 总页数
            //  'pageInfo' => showPage($totalPage,$page,$total,12,1),
        ];
     * @author zouyan(305463219@qq.com)
     */
    public static function getBaseListData(Request $request, Controller $controller, $model_name = '', $queryParams = '',$relations = '', $oprateBit = 2 + 4,  $notLog = 0){
        $company_id = $controller->company_id;
        // 获得翻页的三个关键参数
        /*
        翻页的三个关键参数
        [
            'page' => $page,// 当前页,如果不正确默认第一页
            'pagesize' => $pagesize,// 每页显示数量,取值1 -- 100 条之间,默认15条
            'total' => $total,// 总记录数,优化方案：传0传重新获取总数，如果传了，则不会再获取，而是用传的，减软数据库压力;=-5:只统计条件记录数量，不返回数据
            // 追加两个参数 - 需要时才用
            // 链接地址模板 http://www.***.com/list/{page} 主要是这个page 替换为具体的页数
            'url_model' => $url_model,
            // 链接地址模板 $url_model 中的页数标签 默认 {page}
            'page_tag' => $page_tag,
        ]
         */
        $page_config = $queryParams['page_config'] ?? [];
        if(isset($queryParams['page_config'])) unset($queryParams['page_config']);

        $pageParams = CommonRequest::getPageParams($request);

        $pageParams = array_merge($pageParams, $page_config);
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        $result = $modelObj::getBaseListData($company_id, $pageParams, $model_name, $queryParams, $relations, $oprateBit, $notLog);
        return $result;
    }

    /**
     * 删除单条数据--兼容批量删除
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称, 为空，则用对象的属性
     * @param int $notLog 是否需要登陆 0需要1不需要 2已经判断权限，不用判断权限
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function delAjaxBase(Request $request, Controller $controller, $model_name = '', $notLog = 0){

        $id = CommonRequest::get($request, 'id');
        $company_id = $controller->company_id;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);

        $resultDatas = $modelObj::delAjaxBase($company_id, $id, $model_name, $notLog);

        return ajaxDataArr(1, $resultDatas, '');
    }


    /**
     * 删除单条数据---总系统类表--兼容批量删除
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称 为空，则用对象的属性
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function delSysAjaxBase(Request $request, Controller $controller, $model_name = '', $notLog = 0){

        $id = CommonRequest::getInt($request, 'id');
        $company_id = $controller->company_id;

        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);

        $resultDatas = $modelObj::delSysAjaxBase($company_id, $id, $model_name, $notLog);

        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * 删除数据---根据条件
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称 为空，则用对象的属性
     * @param string $queryParams 条件数组/json字符
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function delRecordByQuery(Request $request, Controller $controller, $model_name = '', $queryParams = [], $notLog = 0){
        $company_id = $controller->company_id;

        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);

        $resultDatas = $modelObj::delByQuery($company_id, $model_name, $queryParams, $notLog);

        return $resultDatas;
    }

    /**
     * 根据id获得单条数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称  为空，则用对象的属性
     * @param int $id id
     * @param array $selectParams 查询字段参数--一维数组
     * @param json/array $relations 要查询的与其它表的关系
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoDataBase(Request $request, Controller $controller, $model_name = '', $id = 0, $selectParams = [], $relations = '', $notLog = 0){
        $company_id = $controller->company_id;
        // $relations = '';
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);

        return $modelObj::getInfoDataBase($company_id, $id, $model_name, $selectParams, $relations, $notLog);
    }

    /**
     * 根据model的条件获得一条详情记录 - 一维
     *
     * @param object $modelObj 当前模型对象
     * @param int $companyId 企业id
     * @param string $queryParams 条件数组/json字符
     * @param string $relations 关系数组/json字符
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByQuery(Request $request, Controller $controller, $modelName, $companyId = null,$queryParams='' ,$relations = '', $notLog = 0)
    {
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        $info = $modelObj::getInfoByQuery($modelName, $companyId, $queryParams, $relations, $notLog);

        if(isset($info['total_price'])) $info['total_price_format'] = Tool::formatMoney($info['total_price'], 2, '');
        if(isset($info['total_run_price'])) $info['total_run_price_format'] = Tool::formatMoney($info['total_run_price'], 2, '');
        if(isset($info['pay_run_amount'])) $info['pay_run_amount_format'] = Tool::formatMoney($info['pay_run_amount'], 2, '');

        return $info;
    }

    /**
     * 根据model的条件获得一条详情记录 - pagesize 1:返回一维数组,>1 返回二维数组  -- 推荐有这个按条件查询详情
     *
     * @param object $modelObj 当前模型对象   为空，则用对象的属性
     * @param int $companyId 企业id
     * @param int $pagesize 想获得的记录数量 1 , 2 。。 默认1
     * @param string $queryParams 条件数组/json字符
     * @param string $relations 关系数组/json字符
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoQuery(Request $request, Controller $controller, $modelName, $companyId = null, $pagesize = 1,$queryParams ='' ,$relations = '', $notLog = 0)
    {
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::getInfoQuery($modelName, $companyId, $pagesize, $queryParams, $relations, $notLog);
    }

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称 为空，则用对象的属性   为空，则用对象的属性
     * @param array $saveData 要保存或修改的数组
     * @param int $id id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceByIdBase(Request $request, Controller $controller, $model_name, $saveData, &$id, $notLog = 0){
        $company_id = $controller->company_id;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);

        return $modelObj::replaceByIdBase($company_id, $id, $saveData, $model_name, $notLog);
    }

    /**
     * 通过id同步修改关系接口
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称 为空，则用对象的属性   为空，则用对象的属性
     * @param array $syncParams 要保存或修改的数组
     * @param int $id id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function saveSyncById(Request $request, Controller $controller, $model_name, $syncParams, &$id, $notLog = 0){
        $company_id = $controller->company_id;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::saveSyncById($company_id, $id, $syncParams, $model_name, $notLog = 0);
    }

    /**
     * 通过条件修改记录接口
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称  为空，则用对象的属性   为空，则用对象的属性
     * @param array $saveData 要保存或修改的数组
     * @param array $queryParams 条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function ModifyByQueyCTL(Request $request, Controller $controller, $model_name, $saveData, $queryParams, $notLog = 0){
        $company_id = $controller->company_id;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::ModifyByQueyBSNS($company_id, $model_name, $saveData, $queryParams, $notLog);
    }

    /**
     * 查找记录,或创建新记录[没有找到] - $searchConditon +  $updateFields 的字段,
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称  为空，则用对象的属性   为空，则用对象的属性
     * @param array $searchConditon 查询字段[一维数组]
     * @param array $updateFields 表中还需要保存的记录 [一维数组] -- 新建表时会用
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array $mainObj 表对象[一维]
     * @author zouyan(305463219@qq.com)
     */
    public static function firstOrCreateCTL(Request $request, Controller $controller, $model_name, $searchConditon, $updateFields, $notLog = 0){
        $company_id = $controller->company_id;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::firstOrCreateBSNS($company_id, $model_name, $searchConditon, $updateFields, $notLog);
    }

    /**
     * ajax获得列表记录--所有满足条件记录[无翻页]
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称  为空，则用对象的属性   为空，则用对象的属性
     * @param string $queryParams 条件数组/json字符
     * @param string $relations 关系数组/json字符
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function ajaxGetQueryListCTL(Request $request, Controller $controller, $model_name, $queryParams, $relations, $notLog = 0){
        $company_id = $controller->company_id;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::ajaxGetQueryListBSNS($company_id, $model_name, $queryParams, $relations, $notLog);
    }


    /**
     * 批量新加-data只能返回成功true:失败:false
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称  为空，则用对象的属性   为空，则用对象的属性
     * @param array $saveData 要保存或修改的数组-二维数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function createBathCTL(Request $request, Controller $controller, $model_name, $saveData = [], $notLog = 0){
        $company_id = $controller->company_id;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::createBathBSNS($company_id, $model_name, $saveData, $notLog);
    }

    /**
     * 批量新加-data返回成功的id数组
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称  为空，则用对象的属性   为空，则用对象的属性
     * @param array $saveData 要保存或修改的数组
     * @param string $primaryKey 表的主键字段名称
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function createBathByPrimaryKeyCTL(Request $request, Controller $controller, $model_name, $saveData, $primaryKey = 'id', $notLog = 0){
        $company_id = $controller->company_id;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::createBathByPrimaryKeyBSNS($company_id, $model_name, $saveData, $primaryKey, $notLog);
    }

    /**
     * 自增自减接口,通过条件-data操作的行数
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称  为空，则用对象的属性   为空，则用对象的属性
     * @param string $incDecType 增减类型 inc 增 ;dec 减[默认]
     * @param string $incDecField 增减字段
     * @param string $incDecVal 增减值
     * @param array $saveData 要保存或修改的数组
     * @param string $queryParams 条件数组/json字符
     * @param string $queryParams 条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @param string $modifFields 修改的其它字段 -没有，则传空数组
     * @return  mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function incDecByQueyCTL(Request $request, Controller $controller, $model_name, $incDecType = 'dec', $incDecField = '', $incDecVal = 0, $saveData = [], $queryParams = '', $notLog = 0, $modifFields = []){
        $company_id = $controller->company_id;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::incDecByQueyBSNS($company_id, $model_name, $incDecType, $incDecField, $incDecVal, $saveData, $queryParams, $notLog, $modifFields);
    }

    /**
     * 自增自减接口,通过条件-data操作的行数
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称  为空，则用对象的属性   为空，则用对象的属性
     * @param array $saveData 要保存或修改的数组
     *   $saveData = [
     *       [
     *          'Model_name' => 'model名称',
     *          'primaryVal' => '主键字段值',
     *          'incDecType' => '增减类型 inc 增 ;dec 减[默认]',
     *          'incDecField' => '增减字段',
     *          'incDecVal' => '增减值',
     *          'modifFields' => '修改的其它字段 -没有，则传空数组',
     *      ],
     *  ];
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function bathIncDecByArrCTL(Request $request, Controller $controller, $model_name, $saveData = [], $notLog = 0){
        $company_id = $controller->company_id;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::bathIncDecByArrBSNS($company_id, $model_name, $saveData, $notLog);
    }

    /**
     * 通过id移除关系接口
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $model_name 模型名称  为空，则用对象的属性   为空，则用对象的属性
     * @param array $syncParams 要保存或修改的数组
     * @param int $id id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function detachById(Request $request, Controller $controller, $model_name, $id, $detachParams, $notLog = 0){
        $company_id = $controller->company_id;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::detachById($company_id, $model_name, $id, $detachParams, $notLog);
    }

    /**
     * 获得历史员工记录id, 可缓存
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @return  int 历史员工记录id
     * @author zouyan(305463219@qq.com)
     */
    public static function getStaffHistoryId(Request $request, Controller $controller){
        $company_id = $controller->company_id;
        $operate_staff_id = $controller->operate_staff_id;
        $cache_sel = $controller->cache_sel;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        $operate_staff_id_history = $modelObj::getStaffHistoryId($company_id, $operate_staff_id, $cache_sel);
        return $operate_staff_id_history;

    }

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $saveData 需要操作的数组 [一维或二维数组]
     * @return  mixed 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function addOprate(Request $request, Controller $controller, &$saveData){
        $company_id = $controller->company_id;
        $operate_staff_id = $controller->operate_staff_id;
        $cache_sel = $controller->cache_sel;
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        $saveData = $modelObj::addOprate($company_id, $operate_staff_id, $saveData, $cache_sel);
        return $saveData;
    }

    /**
     * 对比主表和历史表是否相同，相同：不更新版本号，不同：版本号+1
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $modelName 主表对象名称
     * @param mixed $primaryVal 主表对象主键值
     * @param string $historyObj 历史表对象名称
     * @param obj $HistoryTableName 历史表名字
     * @param array $historySearch 历史表查询字段[一维数组][一定要包含主表id的] +  版本号(不用传，自动会加上)  格式 ['字段1'=>'字段1的值','字段2'=>'字段2的值' ... ]
     * @param array $ignoreFields 忽略都有的字段中，忽略主表中的记录 [一维数组] 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]
     * @param int $forceIncVersion 如果需要主表版本号+1,是否更新主表 1 更新 ;0 不更新
     * @param int $companyId 企业id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  mixed 历史记录表id
     * @author zouyan(305463219@qq.com)
     */
    public static function compareHistoryOrUpdateVersion(Request $request, Controller $controller, $modelName, $primaryVal, $historyObj, $HistoryTableName, $historySearch, $ignoreFields = [], $forceIncVersion= 1, $companyId = null , $notLog = 0){
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::compareHistoryOrUpdateVersion($modelName, $primaryVal, $historyObj, $HistoryTableName, $historySearch, $ignoreFields, $forceIncVersion, $companyId, $notLog);
    }

    /**
     * 根据主表id，获得对应的历史表id
     *
     * @param string $modelName 主表对象名称
     * @param mixed $primaryVal 主表对象主键值
     * @param string $historyObj 历史表对象名称
     * @param obj $HistoryTableName 历史表名字
     * @param array $historySearch 历史表查询字段[一维数组][一定要包含主表id的] +  版本号(不用传，自动会加上) 格式 ['字段1'=>'字段1的值','字段2'=>'字段2的值' ... ]
     * @param array $ignoreFields 忽略都有的字段中，忽略主表中的记录 [一维数组] 格式 ['字段1','字段2' ... ]
     * @param int $companyId 企业id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 历史记录表id
     * @author zouyan(305463219@qq.com)
     */
    public static function getHistoryId(Request $request, Controller $controller, $modelName, $primaryVal, $historyObj, $HistoryTableName, $historySearch, $ignoreFields = [], $companyId = null , $notLog = 0){
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::getHistoryId( $modelName, $primaryVal, $historyObj, $HistoryTableName, $historySearch, $ignoreFields, $companyId , $notLog);
    }

    /**
     * 查找记录,或创建新记录[没有找到] - $searchConditon +  $updateFields 的字段,
     *
     * @param string $modelName 主表对象名称
     * @param array $searchConditon 查询字段[一维数组]
     * @param array $updateFields 表中还需要保存的记录 [一维数组] -- 新建表时会用
     * @param int $companyId 企业id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return array $mainObj 表对象[一维]
     * @author zouyan(305463219@qq.com)
     */
    public static function updateOrCreate(Request $request, Controller $controller, $modelName, $searchConditon, $updateFields, $companyId = null , $notLog = 0){
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::updateOrCreate($modelName, $searchConditon, $updateFields, $companyId, $notLog);
    }

    /**
     * 根据主健批量修改记录
     *
     * @param object $modelObj 当前模型对象
     * @param array $saveData 要保存或修改的数组
     * @param string $queryParams 条件数组/json字符
     * @param int $companyId 企业id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function saveBathById(Request $request, Controller $controller, $modelName, $saveData= [], $primaryKey = 'id', $companyId = null, $notLog = 0 )
    {
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::saveBathById($modelName, $saveData, $primaryKey, $companyId, $notLog);
    }

    /**
     * 通过id修改接口
     *
     * @param object $modelObj 当前模型对象
     * @param int $id id
     * @param int $companyId 企业id
     * @param array $saveData 要保存或修改的数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function saveByIdApi(Request $request, Controller $controller, $modelName, $id, $saveData, $companyId = null, $notLog = 0)
    {
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::saveByIdApi($modelName, $id, $saveData, $companyId, $notLog);
    }

    /**
     * 判断权限
     * @param array $infoData 记录数组 一维
     * @param array $judgeArr 需要判断的下标及值
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function judgePowerByObj(Request $request, Controller $controller, $infoData, $judgeArr = [] ){
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        $modelObj::judgePowerByObj($infoData, $judgeArr);
    }

    /**
     * 判断权限
     *
     * @param int $id id ,多个用,号分隔
     * @param array $judgeArr 需要判断的下标[字段名]及值 一维数组
     * @param string $model_name 模型名称   为空，则用对象的属性
     * @param int $companyId 企业id
     * @param json/array $relations 要查询的与其它表的关系
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return array 一维数组[单条] 二维数组[多条]
     * @author zouyan(305463219@qq.com)
     */
    public static function judgePower(Request $request, Controller $controller, $id, $judgeArr = [] , $model_name = '', $company_id = '', $relations = '', $notLog  = 0)
    {
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::judgePower($id, $judgeArr, $model_name, $company_id, $relations, $notLog);
    }

    /**
     * 根据条件，获得kv数据
     *
     * @param object $modelName 当前模型对象   为空，则用对象的属性
     * @param array $kvParams 查询的kv字段数据参数数组/json字符  ['key' => 'id字段', 'val' => 'name字段'] 或 [] 返回查询的数据
     * @param array $selectParams 查询字段参数数组/json字符 需要获得的字段数组   ['id', 'area_name'] ; 参数 $kv 不为空，则此参数可为空数组
     * @param array $queryParams 查询条件
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return array kv数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getKVCT(Request $request, Controller $controller, $modelName, $kvParams = '', $selectParams = '', $queryParams = '', $companyId = null , $notLog = 0)
    {
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::getKVBS($modelName, $kvParams, $selectParams, $queryParams, $companyId, $notLog);
    }

    /**
     * 获得数据模型属性
     *
     * @param object $modelObj 当前模型对象 Model的路径和名称 如 RunBuy\LrChinaCity   为空，则用对象的属性
     * @param string $attrName 属性名称[静态或动态]--注意静态时不要加$ ,与动态属性一样 如 attrTest
     * @param int $isStatic 是否静态属性 0：动态属性 1 静态属性
     * @param int $companyId 企业id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function getAttrCT(Request $request, Controller $controller, $modelName, $attrName = '', $isStatic = 0, $companyId = null , $notLog = 0)
    {
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::getAttrBS($modelName, $attrName, $isStatic, $companyId , $notLog);
    }

    /**
     * 调用数据模型方法
     *
     * @param object $modelObj 当前模型对象 Model的路径和名称 如 RunBuy\LrChinaCity   为空，则用对象的属性
     * @param string $methodName 方法名称   为空，则用对象的属性
     * @param array $params 参数数组 没有参数:[]  或 [第一个参数, 第二个参数,....];
     * @param int $companyId 企业id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function exeMethodCT(Request $request, Controller $controller, $modelName, $methodName = '', $params = [], $companyId = null , $notLog = 0){
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::exeMethodBS($modelName, $methodName, $params, $companyId , $notLog);
    }

    /**
     * 获得中间Business-DB层属性
     *
     * @param object $modelObj 当前模型对象 Model的路径和名称 如 RunBuy\LrChinaCity 为空，则用对象的属性
     * @param string $attrName 属性名称[静态或动态]--注意静态时不要加$ ,与动态属性一样 如 attrTest
     * @param int $isStatic 是否静态属性 0：动态属性 1 静态属性
     * @param int $companyId 企业id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function getBusinessDBAttrCT(Request $request, Controller $controller, $modelName, $attrName = '', $isStatic = 0, $companyId = null , $notLog = 0){
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::getBusinessDBAttrBS($modelName, $attrName, $isStatic, $companyId, $notLog);
    }

    /**
     * 调用中间Business-DB层方法
     *
     * @param object $modelObj 当前模型对象 Model的路径和名称 如 RunBuy\LrChinaCity  为空，则用对象的属性
     * @param string $methodName 方法名称
     * @param array $params 参数数组 没有参数:[]  或 [第一个参数, 第二个参数,....];
     * @param int $companyId 企业id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function exeDBBusinessMethodCT(Request $request, Controller $controller, $modelName, $methodName = '', $params = [], $companyId = null , $notLog = 0){
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::exeDBBusinessMethodBS($modelName, $methodName, $params, $companyId, $notLog);
    }

    /**
     * 获得中间Business层属性
     *
     * @param object $modelObj 当前模型对象 为空，则用对象的属性
     * @param string $attrName 属性名称[静态或动态]--注意静态时不要加$ ,与动态属性一样 如 attrTest
     * @param int $isStatic 是否静态属性 0：动态属性 1 静态属性
     * @param int $companyId 企业id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function getBusinessAttrCT(Request $request, Controller $controller, $modelName, $attrName = '', $isStatic = 0, $companyId = null , $notLog = 0){
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::getBusinessAttrBS($modelName, $attrName, $isStatic, $companyId, $notLog);
    }

    /**
     * 调用中间Business层方法
     *
     * @param object $modelObj 当前模型对象 为空，则用对象的属性
     * @param string $methodName 方法名称
     * @param array $params 参数数组 没有参数:[]  或 [第一个参数, 第二个参数,....];
     * @param int $companyId 企业id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @author zouyan(305463219@qq.com)
     */
    public static function exeBusinessMethodCT(Request $request, Controller $controller, $modelName, $methodName = '', $params = [], $companyId = null , $notLog = 0)
    {
        // 获得对象
        static::requestGetObj($request, $controller,$modelObj);
        return $modelObj::exeBusinessMethodBS($modelName, $methodName, $params, $companyId, $notLog);
    }

    // *******************其它公用方法************开始*************************************************************************************

    /**
     * 根据id返回主表详情及历史表 id信息
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @param string $relations 关系数组/json字符
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoHistoryId(Request $request, Controller $controller, &$id, $relations= [], $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        // 调用新加或修改接口
        $apiParams = [
            'company_id' => $company_id,
            'id' => $id,
            'operate_staff_id' => $user_id,
            'relations' => $relations,
        ];
        $infoHistory = static::exeDBBusinessMethodCT($request, $controller, '', 'getInfoHistoryId', $apiParams, $company_id, $notLog);
        return $infoHistory;
    }


    /**
     * 获得扩展配置信息
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $pageKey  配置关键字下标
     * @param string $funKey  功能关键字下标 -- 可为空:不返回功能配置，返回页面配置
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  array 配置数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getExtendParamsConfig(Request $request, Controller $controller, $pageKey = '', $funKey = '', $notLog = 0)
    {
        if (empty($pageKey)) return [];
        $extParams = [];// 具体配置内容
        $pageConfig = $extParams[$pageKey] ?? [];
        if(empty($funKey)) return $pageConfig;

        return $pageConfig[$funKey] ?? [];
    }
    // *******************其它公用方法************结束*************************************************************************************

    // ~~~~~~~~~~~子类[如有需要]需要重写的方法~~~~~~~~开始~~~~~~~~~~~~~~~~~~~

    // ~~~~~~~~~~~子类[如有需要]需要重写的方法~~~~~~~~结束~~~~~~~~~~~~~~~~~~~

}
