<?php
// 所属企业检验检测机构资质认定证书附表
namespace App\Business\DB\QualityControl;

use App\Models\QualityControl\CertificateSchedule;
use App\Services\DB\CommonDB;
use App\Services\File\DownFile;
use App\Services\Tool;
use App\Services\Upload\UploadFile;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class CertificateScheduleDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CertificateSchedule';
    public static $table_name = 'certificate_schedule';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = ['certificate_schedule_id'];


    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::getHistoryId($mainDBObj, $mainId, CertificateScheduleHistoryDBBusiness::$model_name
            , CertificateScheduleHistoryDBBusiness::$table_name, $historyDBObj, ['certificate_schedule_id' => $mainId], static::$ignoreFields);
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
            'certificate_schedule_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, CertificateScheduleHistoryDBBusiness::$model_name
            , CertificateScheduleHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, static::$ignoreFields, $forceIncVersion);
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


        $certificate_info = [];
        $certificate_company_info = [];// 企业表保存
        // CMA证书号
//        $certificate_no = '';
//        $has_certificate_no = false;// 是否有 false:没有 ； true:有
//        if(Tool::getInfoUboundVal($saveData, 'certificate_no', $has_certificate_no, $certificate_no, 0)){
//
//            $certificate_info = [
//                'company_id' => $saveData['company_id'],
//                'certificate_no' => $certificate_no,
//                'ratify_date' => $saveData['ratify_date'] ?? '',
//                'valid_date' => $saveData['valid_date'] ?? '',
//                'addr' => $saveData['addr'] ?? '',
//            ];
//            $certificate_company_info = [
//                'id' => $saveData['company_id'],
//                'company_certificate_no' => $certificate_no,
//                'ratify_date' => $saveData['ratify_date'] ?? '',
//                'valid_date' => $saveData['valid_date'] ?? '',
//                'laboratory_addr' => $saveData['addr'] ?? '',
//            ];
//        }
        // 修改时 需要强制更新员工数量
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
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history, &$modelObj, &$isModify
            , &$certificate_info, &$certificate_company_info
            , &$forceCompanyNum, &$force_company_num, &$companyNumIds, &$isBatchOperate, &$isBatchOperateVal ){


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
            // 有证书
//            if($has_certificate_no){
//                $certificate_info = array_merge($certificate_info, ['operate_staff_id' => $operate_staff_id, 'operate_staff_id_history' => $operate_staff_id_history]);
//                $certificate_company_info = array_merge($certificate_company_info, ['operate_staff_id' => $operate_staff_id, 'operate_staff_id_history' => $operate_staff_id_history]);
//                // $certificate_id = CertificateDBBusiness::replaceById($certificate_info, $company_id, $operate_id, $operate_staff_id, $modifAddOprate);
//
//                $certificateObj = null ;
//                $searchConditon = [
//                    'company_id' => $certificate_info['company_id'],
//                    // 'certificate_no' => $certificate_info['certificate_no'],// 一个企业只能有一个证书，所以去掉这个字段
//                ];
//                CertificateDBBusiness::updateOrCreate($certificateObj, $searchConditon, $certificate_info);
//                $saveData['certificate_id'] = $certificateObj->id;// $certificate_id;
//
//                // 更新企业表信息
//                $companyObj = null ;
////                $searchConditon = [
////                    'id' => $certificate_company_info['id'],
////                    // 'certificate_no' => $certificate_info['certificate_no'],// 一个企业只能有一个证书，所以去掉这个字段
////                ];
////                StaffDBBusiness::updateOrCreate($companyObj, $searchConditon, $certificate_company_info);
//                StaffDBBusiness::saveById($certificate_company_info, $certificate_company_info['id'],$companyObj);
//
//            }
            if(isset($saveData['category_name'])){
                $tem_category_name = trim($saveData['category_name']);
                if(strlen($tem_category_name) > 100) $tem_category_name = mb_substr($tem_category_name,0,100,'utf-8');
                $saveData['category_name_id'] = CertificateNamesDBBusiness::getNameId($tem_category_name, $operate_staff_id, $operate_staff_id_history);
            }
            if(isset($saveData['project_name'])){
                $tem_project_name = trim($saveData['project_name']);
                if(strlen($tem_project_name) > 100) $tem_project_name = mb_substr($tem_project_name,0,100,'utf-8');
                $saveData['project_name_id'] = CertificateNamesDBBusiness::getNameId($tem_project_name, $operate_staff_id, $operate_staff_id_history);
            }
            if(isset($saveData['three_name'])){
                $tem_three_name = trim($saveData['three_name']);
                if(strlen($tem_three_name) > 100) $tem_three_name = mb_substr($tem_three_name,0,100,'utf-8');
                $saveData['three_name_id'] = CertificateNamesDBBusiness::getNameId($tem_three_name, $operate_staff_id, $operate_staff_id_history);
            }
            if(isset($saveData['four_name'])){
                $tem_four_name = trim($saveData['four_name']);
                if(strlen($tem_four_name) > 100) $tem_four_name = mb_substr($tem_four_name,0,100,'utf-8');
                $saveData['four_name_id'] = CertificateNamesDBBusiness::getNameId($tem_four_name, $operate_staff_id, $operate_staff_id_history);
            }
            if(isset($saveData['param_name'])){
                $tem_param_name = trim($saveData['param_name']);
                if(strlen($tem_param_name) > 100) $tem_param_name = mb_substr($tem_param_name,0,100,'utf-8');
                $saveData['param_name_id'] = CertificateNamesDBBusiness::getNameId($tem_param_name, $operate_staff_id, $operate_staff_id_history);
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
                $resultDatas = static::getInfo($id);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                $chagneFields = static::compareHistory($id, 1);
                // 数据有改变 或者 版本号=0 [针对中途加此功能的表]
                if( ($ownProperty & 32) == 32 && (!empty($chagneFields) || (isset($resultDatas['version_num']) && $resultDatas['version_num'] == 0) )){
                    static::getIdHistory($id);// 立即写入到历史记录中
                }
            }
            // 新加时 且 实时记录历史
            if(!$isModify && ($ownProperty & 32) == 32 ){
                static::getIdHistory($id);// 立即写入到历史记录中
            }

            // 如果是新加，则记录注册记录
            if(!$isModify){
                // 如果是新加，所需要更新企业能力范围数量
                // 注意，如果是批量操作，不在这里处理，在批量的业务地方再处理此功能
                if(!$isBatchOperate && is_numeric($resultDatas['company_id']) && $resultDatas['company_id'] > 0){
                    StaffDBBusiness::updateCertificateScheduleNum($resultDatas['company_id']);
                }
            }else if($forceCompanyNum && !empty($companyNumIds)){// 修改时 需要强制更新企业能力范围数量
                StaffDBBusiness::updateCertificateScheduleNum($companyNumIds);
            }
        });
        // ************************************************
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }

    /**
     * 根据企业id,获得企业的能力范围数
     *
     * @param int  $company_id 企业id
     * @return  mixed 能力范围数
     * @author zouyan(305463219@qq.com)
     */
    public static function getCertificateScheduleCount($company_id = 0){
        // 更新班级老师人数
//        $queryParams = [
//            'where' => [
//                ['company_id', $company_id],
//                ['admin_type', 4],
////                ['open_status', 2],
////                ['account_status',1],
//            ],
//            'count' => 0,
//            // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//        ];
//        $queryParams = Tool::getParamQuery(['company_id' => $company_id], [], []);
//        $queryParams['count'] = 0;
//        $count = static::getAllList($queryParams, []);
//        return $count;
        return static::getDBFVFormatList(8, 1, ['company_id' => $company_id], false);
    }


    /**
     * 根据id删除--可批量删除
     * 删除员工--还需要重新统计企业的员工数
     * 企业删除 ---有员工的企业不能删除，需要先删除/解绑员工
     * @param int  $company_id 企业id
     * @param string $id id 多个用，号分隔
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @param array $extendParams 其它参数--扩展用参数
     *  [
     *       'organize_id' => 3,操作的企业id 可以为0：不指定具体的企业
     *  ]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){
        $organize_id = $extendParams['organize_id'] ?? 0;// 操作的企业id 可以为0：不指定具体的企业

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }

//        $info = static::getInfo($id);
//        if(empty($info)) throws('记录不存在');
//        $staff_id = $info['staff_id'];
        $dataListObj = null;
        $organizeIds = [];

        // 获得需要删除的数据
//            $queryParams = [
//                'where' => [
////                ['company_id', $organize_id],
//                ['admin_type', $admin_type],
////                ['teacher_status',1],
//                ],
//                // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//            ];
            $queryParams = Tool::getParamQuery([], [], []);
            Tool::appendParamQuery($queryParams, $id, 'id', [0, '0', ''], ',', false);
            Tool::appendParamQuery($queryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
            $dataListObj = static::getAllList($queryParams, []);
            // $dataListObj = static::getListByIds($id);

            $dataListArr = $dataListObj->toArray();
            if(empty($dataListArr)) throws('没有需要删除的数据');
            // 用户删除要用到的
             $organizeIds = array_values(array_unique(array_column($dataListArr,'company_id')));
//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        CommonDB::doTransactionFun(function() use( &$id, &$organize_id, &$organizeIds){


            // 删除主记录
//            $delQueryParams = [
//                'where' => [
//                    ['admin_type', $admin_type],
//                    ['issuper','<>', 1],
//                ],
//            ];
            $delQueryParams = Tool::getParamQuery([], [], []);
            Tool::appendParamQuery($delQueryParams, $id, 'id', [0, '0', ''], ',', false);
            Tool::appendParamQuery($delQueryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
            static::del($delQueryParams);
            // static::deleteByIds($id);
            // 删除员工--还需要重新统计企业的员工数
            if(!empty($organizeIds)){
                foreach($organizeIds as $organizeId){
                    // 根据企业id更企业能力范围数
                    StaffDBBusiness::updateCertificateScheduleNum($organizeId);;
                }
            }
        });
        return $id;
    }

    /**
     * 导入数据
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id 操作的
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @param int $doType 操作类型  1、导入【默认】 ； 2 接口调用
     * @param int $open_status 操作类型   1首次 ;2扩项【默认】
     * @param int $certificate_info 1首次 时的证书信息
     *    [
     *      'certificate_no' => $certificate_no,
     *      'ratify_date' => $ratify_date,
     *      'valid_date' => $valid_date,
     *      'addr' => $addr,
     *  ]
     * @return  array 单条数据 - 记录的id数组--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function importDatas($saveData, $company_id, $operate_staff_id = 0, $modifAddOprate = 0, $doType = 1, $open_status = 2, $certificate_info = []){
//        ini_set('memory_limit','3072M');    // 临时设置最大内存占用为 3072M 3G
//        ini_set("max_execution_time", 0);
//        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期
        Tool::phpInitSet();
        $returnIds = [];
        if(empty($saveData)) return $returnIds;
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());

        // 对数据有效性进行校验
        $organize_id = array_values(array_unique(array_column($saveData, 'company_id')));
        foreach($organize_id as $tem_company_id){
            $staffInfo = StaffDBBusiness::getInfo($tem_company_id);
            if(empty($staffInfo)) throws('企业【' . $tem_company_id .'】不存在！');
        }
        $errsArr = [];// 错误数组
        // $saveArr = [];// 最终可以保存的数据
        $addrArr = [];
        $certificateNoArr = [];
        foreach($saveData as $k => &$info) {
            $recordErr = [];
            $tem_company_id = $info['company_id'] ?? 0;
            $category_name = $info['category_name'] ?? '';// 类别
            $project_name = $info['project_name'] ?? '';// 产品
            $three_name = $info['three_name'] ?? '';// 三级
            $four_name = $info['four_name'] ?? '';// 四级
            $param_name = $info['param_name'] ?? '';// 项目
            $method_name = $info['method_name'] ?? '';// 标准（方法）名称
            $temAddr = $info['addr'] ?? '';
            if($temAddr != '' && !in_array($temAddr, $addrArr)) array_push($addrArr, $temAddr);

            $temCertificateNo = $info['certificate_no'] ?? '';
            if($temCertificateNo != '' && !in_array($temCertificateNo, $certificateNoArr)) array_push($certificateNoArr, $temCertificateNo);

            if(!empty($method_name)){
                $method_name = replace_enter_char($method_name, 1);
                $info['method_name'] = $method_name;
            }
            $limit_range = $info['limit_range'] ?? '';// 限制范围
            if(!empty($limit_range)){
                $limit_range = replace_enter_char($limit_range, 1);
                $info['limit_range'] = $limit_range;
            }
            $explain_text = $info['explain_text'] ?? '';// 说明
            if(!empty($explain_text)){
                $explain_text = replace_enter_char($explain_text, 1);
                $info['explain_text'] = $explain_text;
            }
            if(!is_numeric($tem_company_id) || $tem_company_id <= 0) array_push($recordErr, '所属企业不能为空!');
           // if(empty($category_name) && empty($project_name) && empty($param_name)) array_push($recordErr, '类别、产品、项目不能都为空!');
            if(empty($method_name)) array_push($recordErr, '标准（方法）名称不能都为空!');

            if(!empty($recordErr)){
                array_push($errsArr,'第' . ($k + 1) . '条记录:<br/>' . implode('<br/>', $recordErr));
            }
            if(empty($category_name) && empty($project_name) && empty($three_name) && empty($four_name) && empty($param_name)){
                $queryParams = ['method_name' => $method_name ];
            }else{
                $queryParams = ['company_id' => $tem_company_id, 'category_name' => $category_name, 'project_name' => $project_name, 'three_name' => $three_name, 'four_name' => $four_name, 'param_name' => $param_name];
            }
            $temInfo = [];// static::getDBFVFormatList(4, 1, $queryParams, false); --不用修改，都是插入新的记录
            $saveData[$k]['id'] = $temInfo['id'] ?? 0;
        }
        // 如果有错，则返回错误
        if(!empty($errsArr)) throws(implode('<br/>', $errsArr));


        CommonDB::doTransactionFun(function() use( &$saveData, &$organize_id, &$returnIds, &$temNeedStaffIdOrHistoryId,
            &$operate_staff_id, &$company_id, &$modifAddOprate, &$operate_staff_id_history, &$doType,
            &$open_status, &$certificate_info, &$addrArr, &$certificateNoArr){
            if($open_status == 1){
                $certificateObj = null ;
                $searchConditon = [
                    'company_id' => $company_id,
                    // 'certificate_no' => $certificate_info['certificate_no'],// 一个企业只能有一个证书，所以去掉这个字段
                ];
                CertificateDBBusiness::updateOrCreate($certificateObj, $searchConditon, $certificate_info);
                $certificate_id = $certificateObj->id;// $certificate_id;
                // 同时更新企业的

                // 更新企业表信息


                $certificate_company_info = [
                    'company_certificate_no' => $certificate_info['certificate_no'],
                    'ratify_date' => $certificate_info['ratify_date'] ?? '',
                    'valid_date' => $certificate_info['valid_date'] ?? '',
                    'laboratory_addr' => $certificate_info['addr'] ?? '',
                ];
                $companyObj = null ;
//                $searchConditon = [
//                    'id' => $company_id,
//                    // 'certificate_no' => $certificate_info['certificate_no'],// 一个企业只能有一个证书，所以去掉这个字段
//                ];
//                StaffDBBusiness::updateOrCreate($companyObj, $searchConditon, $certificate_company_info);
                StaffDBBusiness::saveById($certificate_company_info, $company_id,$companyObj);

            }

            // 判断证书是否存在
            $certificateNOId = [];
            foreach($certificateNoArr as $temCertificateNo){
                $certificateInfo = CertificateDBBusiness::getDBFVFormatList(4, 1, ['company_id' => $company_id, 'certificate_no' => $temCertificateNo]);
                if(empty($certificateInfo)) throws('证书【' .$temCertificateNo . '】信息不存在！请先上传证书信息！');
                $certificateNOId[$temCertificateNo] = $certificateInfo['id'];
            }
            // 保存地址信息
            $addrIdArr = [];
            foreach($addrArr as $itemAddr){
//                $addrObj = null ;
//                $searchConditon = [
//                    'company_id' => $company_id,
//                    'addr' => $itemAddr,
//                    // 'certificate_no' => $certificate_info['certificate_no'],// 一个企业只能有一个证书，所以去掉这个字段
//                ];
//                $addrInfo = LaboratoryAddrDBBusiness::getDBFVFormatList(4, 1, $searchConditon);
//                $addr_id = $addrInfo['id'] ?? 0;
////                $addrInfo = $searchConditon;
////                LaboratoryAddrDBBusiness::updateOrCreate($addrObj, $searchConditon, $addrInfo);
////                $addr_id = $addrObj->id;// $certificate_id;
//                if(!is_numeric($addr_id) || $addr_id <= 0 || (!empty($addrInfo) && $addrInfo['open_status'] != 1)){// 没有，则新加 或 有记录，但是未开启，则开启
//                    $searchConditon['open_status'] = 1;
//                    LaboratoryAddrDBBusiness::replaceById($searchConditon, $company_id, $addr_id, $operate_staff_id, $modifAddOprate);
//                }
                $addr_id = 0;
                LaboratoryAddrDBBusiness::createOrOpenAddr($company_id, $itemAddr, $addr_id, $operate_staff_id, $modifAddOprate);
                if(!is_numeric($addr_id) || $addr_id <= 0 ) throws('保存实验室地址失败！');

                $addrIdArr[$itemAddr] = $addr_id;
            }


            // 对数据进行修改或新加
            // throws('对数据进行修改或新加');
            foreach($saveData as $k => &$info){
                $id = $info['id'] ?? 0;
                if(isset($info['id'])) unset($info['id']);
                $temAddr = $info['addr'] ?? '';
                $tem_company_id = $info['company_id'] ?? 0;
                $tem_certificate_no = $info['certificate_no'] ?? '';
                $info['certificate_id'] = $certificateNOId[$tem_certificate_no] ?? 0;
                // 加入操作人员信息
                if($temNeedStaffIdOrHistoryId) static::addOprate($info, $operate_staff_id,$operate_staff_id_history, 1);
                $info['laboratory_id'] = $addrIdArr[$temAddr] ?? 0;
                // 新加或更新
                static::replaceById($info, $tem_company_id, $id, $operate_staff_id, $modifAddOprate);
                array_push($returnIds, $id);

            }
            // 添加日志
            if(($doType & 1) == 1) CertificateImportLogDBBusiness::saveImportLog($saveData, $operate_staff_id, $operate_staff_id_history);
            // 根据企业id更企业能力范围数
            StaffDBBusiness::updateCertificateScheduleNum($organize_id);
        });
        return $returnIds;
    }

    /**
     * 批量保存数据
     *
     * @param array $saveData 要保存或修改的数组
     * [
    'company_info' => [
    'company_name' => $company_name,// 机构名称
    'company_certificate_no' => $certificate_no,// CMA证书号(资质认定编号)
    'ratify_date' => $ratify_date,// 发证日期 格式 2020-11-06
    'valid_date' => $valid_date,// 证书有效日期 格式 2020-11-06
    'laboratory_addr' => $addr,// 实验室地址
    'company_contact_name' => $contact_name,// 联系人
    'company_contact_mobile' => $contact_mobile,// 联系人手机或电话
    ],
    ]
     * @param int  $company_id 企业id 第三方操作者
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 数据所属企业的id
     * @author zouyan(305463219@qq.com)
     */
    public static function saveCompany($saveData, $company_id, $operate_staff_id = 0, $modifAddOprate = 0)
    {
//        ini_set('memory_limit', '3072M');    // 临时设置最大内存占用为 3072M 3G
//        ini_set("max_execution_time", 0);
//        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期
        Tool::phpInitSet();
        $staff_id = 0;// 数据所属的企业 id
        try{
            CommonDB::doTransactionFun(function() use( &$saveData, &$company_id, &$operate_staff_id, &$modifAddOprate, &$staff_id){
                $company_info = $saveData['company_info'] ?? [];// 必有值

                $staff_id = 0;// 数据所属的企业 id
                $isAddNew = false;// 企业是否是新加 true:新加 ； false:已存在
                // 新加或修改企业信息
                StaffDBBusiness::saveCompany($company_info, $staff_id, $isAddNew, true);

            });
        } catch ( \Exception $e) {
            throws($e->getMessage(), $e->getCode());
        }
        return $staff_id;
    }

    /**
     * 批量保存数据
     *
     * @param array $saveData 要保存或修改的数组
     * [
        'company_info' => [
            'company_name' => $company_name,// 机构名称
            'company_certificate_no' => $certificate_no,// CMA证书号(资质认定编号)
            'ratify_date' => $ratify_date,// 发证日期 格式 2020-11-06
            'valid_date' => $valid_date,// 证书有效日期 格式 2020-11-06
            'laboratory_addr' => $addr,// 实验室地址
            'company_contact_name' => $contact_name,// 联系人
            'company_contact_mobile' => $contact_mobile,// 联系人手机或电话
        ],
        'file_list' => $fileArr,// [['file_title'=> '文件名称 ','file_url'=> '文件网络读取地址','file_type'=> '文件类型  1能力附表 ; 2 机构自我声明 ;3机构处罚',]]
        'schedule_list' => $scheduleArr,// [['category_name'=> '类别[第一级][必填]','project_name'=> '产品[第二级]','three_name'=> '第三级'
           ,'four_name'=> '第四级','param_name'=> '项目[第五级]','method_name'=> '标准（方法）名称','limit_range'=> '限制范围','explain_text'=> '说明']]
    ]
     * @param int  $company_id 企业id 第三方操作者
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 数据所属企业的id
     * @author zouyan(305463219@qq.com)
     */
    public static function bathSaveDatas($saveData, $company_id, $operate_staff_id = 0, $modifAddOprate = 0)
    {
//        ini_set('memory_limit', '3072M');    // 临时设置最大内存占用为 3072M 3G
//        ini_set("max_execution_time", 0);
//        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期
        Tool::phpInitSet();
        $addFiels = [];
        $staff_id = 0;// 数据所属的企业 id
        try{
            CommonDB::doTransactionFun(function() use( &$saveData, &$company_id, &$operate_staff_id, &$modifAddOprate, &$addFiels, &$staff_id){
                $company_info = $saveData['company_info'] ?? [];// 必有值
                $file_list = $saveData['file_list'] ?? [];// 文件信息 可为空
                $schedule_list = $saveData['schedule_list'] ?? [];// 能力范围 必有值

                $staff_id = 0;// 数据所属的企业 id
                $isAddNew = false;// 企业是否是新加 true:新加 ； false:已存在
                // 新加或修改企业信息
                StaffDBBusiness::saveCompany($company_info, $staff_id, $isAddNew, false);
                // 获取文件并保存
                static::saveFiles($file_list, $staff_id, $addFiels, $isAddNew);

                // 保存能力范围
                $params = [
                    'company_id' => $staff_id,
                    'certificate_no' => $company_info['company_certificate_no'],
                    'ratify_date' => $company_info['ratify_date'],
                    'valid_date' => $company_info['valid_date'],
                    'addr' => $company_info['laboratory_addr'],
                ];
                Tool::arrAppendKeys($schedule_list, $params);
                static::importDatas($schedule_list, $staff_id, $operate_staff_id, $modifAddOprate, 2);

            });
        } catch ( \Exception $e) {
            // 删除发生错误时，上传的文件
            if(!empty($addFiels)){
                Tool::resourceDelFile($addFiels);
                $addFiels = [];
            }
             throws($e->getMessage(), $e->getCode());
        }
        return $staff_id;
    }

    /**
     * 能力范围删除或新加数据
     *
     * @param array $saveData 要保存或修改的数组
     * [
    'company_info' => [
    'company_name' => $company_name,// 机构名称
    'company_certificate_no' => $certificate_no,// CMA证书号(资质认定编号)
    ],
    'schedule_del_list' => $scheduleArr,// [['category_name'=> '类别[第一级][必填]','project_name'=> '产品[第二级]','three_name'=> '第三级'
    ,'four_name'=> '第四级','param_name'=> '项目[第五级]','method_name'=> '标准（方法）名称','limit_range'=> '限制范围','explain_text'=> '说明']]
    'schedule_add_list' => '格式同schedule_del_list'
    ]
     * @param int  $company_id 企业id 第三方操作者
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 数据所属企业的id
     * @author zouyan(305463219@qq.com)
     */
    public static function bathModifyDatas($saveData, $company_id, $operate_staff_id = 0, $modifAddOprate = 0)
    {
//        ini_set('memory_limit', '3072M');    // 临时设置最大内存占用为 3072M 3G
//        ini_set("max_execution_time", 0);
//        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期
        Tool::phpInitSet();
        $staff_id = 0;// 数据所属的企业 id
        try{
            CommonDB::doTransactionFun(function() use( &$saveData, &$company_id, &$operate_staff_id, &$modifAddOprate, &$staff_id){
                $company_info = $saveData['company_info'] ?? [];// 必有值
                $schedule_del_list = $saveData['schedule_del_list'] ?? [];// 能力范围 必有值
                $schedule_add_list = $saveData['schedule_add_list'] ?? [];// 能力范围 必有值

                $staff_id = 0;// 数据所属的企业 id
                $isAddNew = false;// 企业是否是新加 true:新加 ； false:已存在
                $companyInfo = [];
                // 查询企业信息
                StaffDBBusiness::getCompany($company_info, $staff_id, $companyInfo);
                // 删除能力围
                $params = [
                    'company_id' => $staff_id,
                    'certificate_no' => $company_info['company_certificate_no'],
//                    'ratify_date' => $company_info['ratify_date'],
//                    'valid_date' => $company_info['valid_date'],
//                    'addr' => $company_info['laboratory_addr'],
                ];
                Tool::arrAppendKeys($schedule_del_list, $params);
                foreach($schedule_del_list as $v){
                    // Tool::arrClsEmpty($v);// 去除空值
                    // 优先通过 五级分类来删除[查询到，只有一条数据时]
                    $searchInfo = Tool::getArrFormatFields($v, ['certificate_no', 'category_name', 'project_name', 'three_name', 'four_name', 'param_name'], false);
                    if(!empty($searchInfo)){
                        Tool::fieldValToConfig($searchInfo, [], ['excludeVals' => [0, '0'], 'valsSeparator' => ',', 'hasInIsMerge' => true]);

                        $extParams = [
//                        'sqlParams' => [
//                            'orderBy' => ['id' => 'desc'],// 审核通过的优先拿到
//                        ]
                        ];
                        $scheduleList = static::getDBFVFormatList(1, 1, $searchInfo, false, [], $extParams);
                        if(count($scheduleList) <= 1){// 刚好最多只有一条记录
                            foreach($scheduleList as $scheduleInfo){
                                $schedule_id = $scheduleInfo['id'] ?? 0;// 企业 id
                                static::delById($company_id, $schedule_id, $operate_staff_id, $modifAddOprate, ['organize_id' => $staff_id]);
                            }
                            continue;
                        }
                    }
                    // 对数据换行进行处理
                    if(isset($v['method_name']) && !empty($v['method_name'])){
                        $v['method_name'] = replace_enter_char($v['method_name'], 1);
                    }
                    if(isset($v['limit_range']) && !empty($v['limit_range'])){
                        $v['limit_range'] = replace_enter_char($v['limit_range'], 1);
                    }
                    if(isset($v['explain_text']) && !empty($v['explain_text'])){
                        $v['explain_text'] = replace_enter_char($v['explain_text'], 1);
                    }
                    // 查询记录
//                    $extParams = [
//                        'sqlParams' => [
//                            'orderBy' => ['id' => 'desc'],// 审核通过的优先拿到
//                        ]
//                    ];
//                    $scheduleInfo = static::getDBFVFormatList(4, 1, $v, false, [], $extParams);
//                    if(!empty($scheduleInfo)){
//                        $schedule_id = $scheduleInfo['id'] ?? 0;// 企业 id
//                        static::delById($company_id, $schedule_id, $operate_staff_id, $modifAddOprate, ['organize_id' => $staff_id]);
//                    }
                    $extParams = [
//                        'sqlParams' => [
//                            'orderBy' => ['id' => 'desc'],// 审核通过的优先拿到
//                        ]
                    ];
                    $temSearchFV = $v;
                    Tool::fieldValToConfig($temSearchFV, [], ['excludeVals' => [0, '0'], 'valsSeparator' => ',', 'hasInIsMerge' => true]);
                    $scheduleList = static::getDBFVFormatList(1, 1, $temSearchFV, false, [], $extParams);
                    if(!empty($scheduleList)){
                        foreach($scheduleList as $scheduleInfo){
                            $schedule_id = $scheduleInfo['id'] ?? 0;// 企业 id
                            static::delById($company_id, $schedule_id, $operate_staff_id, $modifAddOprate, ['organize_id' => $staff_id]);
                        }
                    }
                }
                // 保存能力范围
                $params = [
                    'company_id' => $staff_id,
                    'certificate_no' => $companyInfo['company_certificate_no'],
                    'ratify_date' => $companyInfo['ratify_date'],
                    'valid_date' => $companyInfo['valid_date'],
                    'addr' => $companyInfo['laboratory_addr'],
                ];
                Tool::arrAppendKeys($schedule_add_list, $params);
                static::importDatas($schedule_add_list, $staff_id, $operate_staff_id, $modifAddOprate, 2);

            });
        } catch ( \Exception $e) {
            throws($e->getMessage(), $e->getCode());
        }
        return $staff_id;
    }


    /**
     * 根据条件修改能力范围
     *
     * @param array $saveData 要保存或修改的数组
     * [
        'company_info' => [
            'company_name' => $company_name,// 机构名称
            'company_certificate_no' => $certificate_no,// CMA证书号(资质认定编号)
        ],
        'search_json' => $scheduleArr,// [['category_name'=> '类别[第一级][必填]','project_name'=> '产品[第二级]','three_name'=> '第三级'
        ,'four_name'=> '第四级','param_name'=> '项目[第五级]','method_name'=> '标准（方法）名称','limit_range'=> '限制范围','explain_text'=> '说明']]
        'schedule_json' => '格式同schedule_del_list'
    ]
     * @param int  $company_id 企业id 第三方操作者
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 数据所属企业的id
     * @author zouyan(305463219@qq.com)
     */
    public static function updateDatas($saveData, $company_id, $operate_staff_id = 0, $modifAddOprate = 0)
    {
//        ini_set('memory_limit', '3072M');    // 临时设置最大内存占用为 3072M 3G
//        ini_set("max_execution_time", 0);
//        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期
        Tool::phpInitSet();
        $staff_id = 0;// 数据所属的企业 id
        try{
            CommonDB::doTransactionFun(function() use( &$saveData, &$company_id, &$operate_staff_id, &$modifAddOprate, &$staff_id){
                $company_info = $saveData['company_info'] ?? [];// 必有值
                $schedule_search = $saveData['schedule_search'] ?? [];// 查询能力范围 必有值
                $schedule_update = $saveData['schedule_update'] ?? [];// 更新能力范围 必有值

                // 对数据换行进行处理
                if(isset($schedule_update['method_name']) && !empty($schedule_update['method_name'])){
                    $schedule_update['method_name'] = replace_enter_char($schedule_update['method_name'], 1);
                }
                if(isset($schedule_update['limit_range']) && !empty($schedule_update['limit_range'])){
                    $schedule_update['limit_range'] = replace_enter_char($schedule_update['limit_range'], 1);
                }
                if(isset($schedule_update['explain_text']) && !empty($schedule_update['explain_text'])){
                    $schedule_update['explain_text'] = replace_enter_char($schedule_update['explain_text'], 1);
                }

                $staff_id = 0;// 数据所属的企业 id
                $isAddNew = false;// 企业是否是新加 true:新加 ； false:已存在
                $companyInfo = [];
                // 查询企业信息
                StaffDBBusiness::getCompany($company_info, $staff_id, $companyInfo);

                $scheduleTemSearchArr = [$schedule_search];

                // 删除能力围
                $params = [
                    'company_id' => $staff_id,
                    'certificate_no' => $company_info['company_certificate_no'],
//                    'ratify_date' => $company_info['ratify_date'],
//                    'valid_date' => $company_info['valid_date'],
//                    'addr' => $company_info['laboratory_addr'],
                ];
                Tool::arrAppendKeys($scheduleTemSearchArr, $params);
                foreach($scheduleTemSearchArr as $v){
                    // Tool::arrClsEmpty($v);// 去除空值
                    // 优先通过 五级分类来删除[查询到，只有一条数据时]
                    $searchInfo = Tool::getArrFormatFields($v, ['certificate_no', 'category_name', 'project_name', 'three_name', 'four_name', 'param_name'], false);
                    // throws(json_encode($searchInfo));
                    if(!empty($searchInfo)){
                        Tool::fieldValToConfig($searchInfo, [], ['excludeVals' => [0, '0'], 'valsSeparator' => ',', 'hasInIsMerge' => true]);

                        $extParams = [
//                        'sqlParams' => [
//                            'orderBy' => ['id' => 'desc'],// 审核通过的优先拿到
//                        ]
                        ];
                        $scheduleList = static::getDBFVFormatList(1, 1, $searchInfo, false, [], $extParams);
                        if(count($scheduleList) <= 1){// 刚好最多只有一条记录
                            foreach($scheduleList as $scheduleInfo){
                                $schedule_id = $scheduleInfo['id'] ?? 0;// 企业 id
                                static::saveById($schedule_update, $schedule_id);
                            }
                            continue;
                        }
                    }
                    // 对数据换行进行处理
                    if(isset($v['method_name']) && !empty($v['method_name'])){
                        $v['method_name'] = replace_enter_char($v['method_name'], 1);
                    }
                    if(isset($v['limit_range']) && !empty($v['limit_range'])){
                        $v['limit_range'] = replace_enter_char($v['limit_range'], 1);
                    }
                    if(isset($v['explain_text']) && !empty($v['explain_text'])){
                        $v['explain_text'] = replace_enter_char($v['explain_text'], 1);
                    }
                    // 查询记录
//                    $extParams = [
//                        'sqlParams' => [
//                            'orderBy' => ['id' => 'desc'],// 审核通过的优先拿到
//                        ]
//                    ];
//                    $scheduleInfo = static::getDBFVFormatList(4, 1, $v, false, [], $extParams);
//                    if(!empty($scheduleInfo)){
//                        $schedule_id = $scheduleInfo['id'] ?? 0;// 企业 id
//                        static::delById($company_id, $schedule_id, $operate_staff_id, $modifAddOprate, ['organize_id' => $staff_id]);
//                    }
                    $extParams = [
//                        'sqlParams' => [
//                            'orderBy' => ['id' => 'desc'],// 审核通过的优先拿到
//                        ]
                    ];
                    $temSearchFV = $v;
                    Tool::fieldValToConfig($temSearchFV, [], ['excludeVals' => [0, '0'], 'valsSeparator' => ',', 'hasInIsMerge' => true]);
                    $scheduleList = static::getDBFVFormatList(1, 1, $temSearchFV, false, [], $extParams);
                    if(!empty($scheduleList)){
                        foreach($scheduleList as $scheduleInfo){
                            $schedule_id = $scheduleInfo['id'] ?? 0;// 企业 id
                            static::saveById($schedule_update, $schedule_id);
                        }
                    }
                }
            });
        } catch ( \Exception $e) {
            throws($e->getMessage(), $e->getCode());
        }
        return $staff_id;
    }

    /**
     * 批量保存文件数据
     *
     * @param array $saveData 要保存或修改的数组
     * [
    'company_info' => [
    'company_name' => $company_name,// 机构名称
    'company_certificate_no' => $certificate_no,// CMA证书号(资质认定编号)
    ],
    'file_list' => $fileArr,// [['file_title'=> '文件名称 ','file_url'=> '文件网络读取地址','file_type'=> '文件类型  1能力附表 ; 2 机构自我声明 ;3机构处罚',]]

    ]
     * @param int  $company_id 企业id 第三方操作者
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 数据所属企业的id
     * @author zouyan(305463219@qq.com)
     */
    public static function bathSaveFiles($saveData, $company_id, $operate_staff_id = 0, $modifAddOprate = 0)
    {
//        ini_set('memory_limit', '3072M');    // 临时设置最大内存占用为 3072M 3G
//        ini_set("max_execution_time", 0);
//        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期
        Tool::phpInitSet();
        $addFiels = [];
        $staff_id = 0;// 数据所属的企业 id
        try{
            CommonDB::doTransactionFun(function() use( &$saveData, &$company_id, &$operate_staff_id, &$modifAddOprate, &$addFiels, &$staff_id){
                $company_info = $saveData['company_info'] ?? [];// 必有值
                $file_list = $saveData['file_list'] ?? [];// 文件信息 必有值

                $staff_id = 0;// 数据所属的企业 id
                $isAddNew = false;// 企业是否是新加 true:新加 ； false:已存在
                $companyInfo = [];
                // 查询企业信息
                StaffDBBusiness::getCompany($company_info, $staff_id, $companyInfo);
                // 获取文件并保存
                static::saveFiles($file_list, $staff_id, $addFiels, $isAddNew);
            });
        } catch ( \Exception $e) {
            // 删除发生错误时，上传的文件
            if(!empty($addFiels)){
                Tool::resourceDelFile($addFiels);
                $addFiels = [];
            }
            throws($e->getMessage(), $e->getCode());
        }
        return $staff_id;
    }

    /**
     * 保存文件 ,注意：如果保存失败，在调用这个方法处，进行文件的删除
     * @param array $data  二维数组 [['file_title'=> '文件名称 ','file_url'=> '文件网络读取地址','file_type'=> '文件类型  1能力附表 ; 2 机构自我声明 ;3机构处罚',]]
     * @param int $company_id  当前数据所属的企业id
     * @param array $addFiels 已经成功保存的文件  [['resource_url' => $files_names]] ; 如果失败，需要删除文件
     * @param int $isAddNew 企业是否是新加 true:新加 ； false:已存在
     */
    public static function saveFiles($data, $company_id, &$addFiels, $isAddNew){

        foreach ($data as $info) {
            /**
             * {
            id: 3788,
            file_title: "能力附表",
            // 注意 "file_content" 、"file_url" 二选一，file_content优先
            "file_content": "文件内容的base64_encode值",
            "file_content_name": "文件内容的原文件名—注：带扩展名 如 aaaa.xlsx",
            file_url: "2020-4-22/e3b8d4d1-c2da-461d-9a9d-474aefc1d2f7.xls",
            czr: "admin",
            file_type: "1",
            czDate: "2020-04-22T15:01:29",
            sqid: 1298
            }
             */

            CommonDB::doTransactionFun(function() use( &$info, &$company_id, &$addFiels, &$isAddNew){

                $file_title = $info['file_title'];// 文件名称
                $file_content = $info['file_content'] ?? '';// 文件内容的base64_encode值
                $file_content_name = $info['file_content_name'] ?? '';// 文件内容的原文件名—注：带扩展名 如 aaaa.xlsx
                $file_path = $info['file_url'] ?? '';// 文件网络读取地址
                if(!empty(trim($file_content))) $file_path =  $file_content_name;
                $file_type = $info['file_type'];// 文件类型  1能力附表 ; 2 机构自我声明 ;3机构处罚
                $schedule_type = $info['schedule_type'] ?? 0;// 如果文件类型file_type值为1能力附表时；的操作类型 0：excel文件；1：首次;2：扩项;4：地址变更;8：标准变更;16：复查;
//                $file_czdate = $info['czDate'];// "2020-11-04T16:53:27"
//                $file_czdate = str_replace('T', ' ', $file_czdate);
//                $file_czdate = judgeDate($file_czdate,"Y-m-d H:i:s");

                $suffix = DownFile::getLocalFileExt($file_path);// strtolower(pathinfo($file_path,PATHINFO_EXTENSION));
                $files_name_txt = $file_title . '.' . $suffix;// basename($file_path);// 8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
                if(!empty(trim($file_content))){
                    // 保存内容
                    $fileArr = static::saveContentFile($file_title, $file_content, $file_path, $company_id);
                }else{
                    // 文件保存
                    $fileArr = static::saveFile($file_title, $file_path, $company_id);
                }
                $files_names = $fileArr['files_names'];// /resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
                $full_names = $fileArr['full_names'];// /srv/www/quality_control/quality_control/admin/public/resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf

                array_push($addFiels, ['resource_url' => $files_names]);
                $file_size = filesize($full_names);
                $mime_type = DownFile::getLocalFileMIME($full_names);
                // 保存图片资源--到数据库
                // 根据扩展名，重新获得文件的操作类型
                $resourceConfig = UploadFile::getResourceConfig($suffix);
                // if(empty($resourceConfig)) throws('请选择正确的文件！');
                $resourceType = $resourceConfig['resource_type'] ?? 0;

                $saveData =[
                    'resource_name' => $files_name_txt,
                    'resource_type' => $resourceType,
                    'resource_url' => $files_names,
                    'resource_size' => $file_size,
                    'resource_size_format' => Tool::formatBytesSize($file_size, 2),
                    'resource_ext' => $suffix,
                    'resource_mime_type' => $mime_type,
                    'ower_type' => 2,// $admin_type
                    'ower_id' => $company_id,
                    'resource_note' => '',
                ];

//                if($file_czdate !== false){
//                    $saveData['created_at'] = $file_czdate;
//                    $saveData['updated_at'] = $file_czdate;
//                }

                $resource_id = 0;
                ResourceDBBusiness::replaceById($saveData, $company_id, $resource_id, 0, 0);
                if($resource_id > 0){
                    $resource_ids = ',' . $resource_id . ',';
                    $resourceIdArr = [$resource_id];
                    switch ($file_type)
                    {
                        case 1:// 能力附表

                            $saveData = [
                                'company_id' => $company_id,
                                // 'type_id' => 0,
                                //             'resource_id' => $resource_id[0] ?? 0,// word资源的id
                                //            'resource_ids' => $resource_ids,// word资源id串(逗号分隔-未尾逗号结束)
//                                'resource_id_pdf' => $resource_id_pdf[0] ?? 0,// pdf资源的id
//                                'resource_ids_pdf' => $resource_ids_pdf,// pdf资源id串(逗号分隔-未尾逗号结束)
                                'resourceIds' => $resourceIdArr,// 此下标为图片资源关系
                            ];
                            if($suffix == 'pdf'){
                                $saveData = array_merge($saveData, [
                                    'type_id' => $schedule_type,// 0,
                                    'resource_id_pdf' => $resourceIdArr[0] ?? 0,// pdf资源的id
                                    'resource_ids_pdf' => $resource_ids,// pdf资源id串(逗号分隔-未尾逗号结束)
                                ]);
                            }else{
                                $saveData = array_merge($saveData, [
                                    'type_id' => $schedule_type,// 0,
                                    'resource_id' => $resourceIdArr[0] ?? 0,// pdf资源的id
                                    'resource_ids' => $resource_ids,// pdf资源id串(逗号分隔-未尾逗号结束)
                                ]);
                            }
                            // 企业 已存在
                            if(!$isAddNew) $saveData['is_import'] = 1;

//                            if($file_czdate !== false){
//                                $saveData['created_at'] = $file_czdate;
//                                $saveData['updated_at'] = $file_czdate;
//                            }

                            $record_id = 0;
                            CompanyScheduleDBBusiness::replaceByIdNew($saveData, $company_id, $record_id, 0, 0);
                            break;
                        case 2:// 机构自我声明管理

                            $saveData = [
                                'company_id' => $company_id,
                                'resource_name' => $file_title,
                                // 'resource_id' => $resourceIdArr[0] ?? 0,// 文件资源的id
                                'resource_ids' => $resource_ids,// 资源id串(逗号分隔-未尾逗号结束)
                                'resourceIds' => $resourceIdArr,// 此下标为资源关系
                            ];
//                            if($file_czdate !== false){
//                                $saveData['created_at'] = $file_czdate;
//                                $saveData['updated_at'] = $file_czdate;
//                            }

                            $record_id = 0;
                            CompanyStatementDBBusiness::replaceById($saveData, $company_id, $record_id, 0, 0);
                            break;
                        case 5:// 机构处罚管理
                            $saveData = [
                                'company_id' => $company_id,
                                'resource_name' => $file_title,
                                // 'resource_id' => $resourceIdArr[0] ?? 0,// 文件资源的id
                                'resource_ids' => $resource_ids,// 资源id串(逗号分隔-未尾逗号结束)
                                'resourceIds' => $resourceIdArr,// 此下标为资源关系
                            ];

//                            if($file_czdate !== false){
//                                $saveData['created_at'] = $file_czdate;
//                                $saveData['updated_at'] = $file_czdate;
//                            }

                            $record_id = 0;
                            CompanyPunishDBBusiness::replaceById($saveData, $company_id, $record_id, 0, 0);
                            break;
                        default:
                            break;
                    }
                }

            });
        }
    }
    // 保存单个远程文件到本机

    /**
     * @param string $fileName  文件中文名 ；如 “能力附表";暂时没有使用--为空
     * @param string $filePath "2020-4-22/e3b8d4d1-c2da-461d-9a9d-474aefc1d2f7.xls",
     * @param int $company_id 数据记录的企业id
     * @return mixed
     */
    /**
     * [publicPath] => /srv/www/quality_control/quality_control/admin/public
    [savePath] => /resource/company/0/down/2020/11/04/
    [saveName] => 8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
    [files_names] => /resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
    [web_url] => http://qualitycontrol.admin.cunwo.net/resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
    [full_names] => /srv/www/quality_control/quality_control/admin/public/resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
     */
    //public static function saveFile($fileName, $filePath, $company_id){
     public static function saveFile($fileName, $filePath, $company_id){
        // $fileName = '2020年4月法人变更自我声明';// '能力附表';
        // $filePath = '2020-4-26/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf';// '2020-4-22/e3b8d4d1-c2da-461d-9a9d-474aefc1d2f7.xls';
    //    $fileUrl = 'http://113.140.67.203:1283/jsp/Jyjc/ZzxxDown.jsp?fileName=' . $fileName . '&filePath=' . $filePath;
        // $files_names = '8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf';
    //    $files_names = basename($filePath);// 8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
         $files_names = '';
    //    return DownFile::getUrlFileToLocal($fileUrl, $company_id,2, '', $files_names);
         return DownFile::getUrlFileToLocal($filePath, $company_id,2, '', $files_names);
    }

    public static function saveContentFile($fileName, $fileContent, $sourceFileName, $company_id){
        // $fileName = '2020年4月法人变更自我声明';// '能力附表';
        // $filePath = '2020-4-26/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf';// '2020-4-22/e3b8d4d1-c2da-461d-9a9d-474aefc1d2f7.xls';
        //    $fileUrl = 'http://113.140.67.203:1283/jsp/Jyjc/ZzxxDown.jsp?fileName=' . $fileName . '&filePath=' . $filePath;
        // $files_names = '8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf';
        //    $files_names = basename($filePath);// 8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
        $files_names = '';
        //    return DownFile::getUrlFileToLocal($fileUrl, $company_id,2, '', $files_names);
        // return DownFile::getUrlFileToLocal($filePath, $company_id,2, '', $files_names);
        return DownFile::saveFileContentToLocal($fileContent, $sourceFileName,  $company_id, 2,1, '', $files_names );
    }

    public static function test(){
        $key = '标';
        // 获得数量

        $tableName = (new CertificateSchedule)->getTable();
        // DB::table($tableName)->selectRaw('price * ? as price_with_tax', [1.0825])->get();
//        $count = DB::table($tableName)->distinct()->count();
        $count = DB::table($tableName)->distinct('company_id')->count('company_id');
//        DB::table($tableName)->raw("distinct(company_id)")->count();
//        $count = DB::table(' ((select distinct company_id from certificate_schedule ))')->count();
        pr(456);
    }

}
