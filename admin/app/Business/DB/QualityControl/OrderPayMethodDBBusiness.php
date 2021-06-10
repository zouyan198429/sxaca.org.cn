<?php
// 收款方式配置
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class OrderPayMethodDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\OrderPayMethod';
    public static $table_name = 'order_pay_method';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

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

        if(isset($saveData['pay_name']) && empty($saveData['pay_name'])  ){
            throws('支付方式名称不能为空！');
        }

        $methodArr = [1,2,4,8,16,32,64,128,256,512,1024,2048,4096,8192,16284,32768];
        if(isset($saveData['pay_method']) && (!is_numeric($saveData['pay_method']) || !in_array($saveData['pay_method'], $methodArr)) ){
            throws('支付方式代码值格式不正确！');
        }

        // 支付方式代码值--唯一
        if( isset($saveData['pay_method']) && static::judgeFieldExist($company_id, $id ,"pay_method", $saveData['pay_method'], [],1)){
            throws('支付方式代码值已存在！');
        }

        // 是否有图片资源
        $hasResource = false;

        $resourceIds = [];
        if(Tool::getInfoUboundVal($saveData, 'resourceIds', $hasResource, $resourceIds, 1)){
            // $saveData['resource_id'] = $resourceIds[0] ?? 0;// 第一个图片资源的id
        }
        // $resource_ids = $saveData['resource_ids'] ?? '';// 图片资源id串(逗号分隔-未尾逗号结束)
        // if(isset($saveData['resource_ids']))  unset($saveData['resource_ids']);
        // if(isset($saveData['resource_id']))  unset($saveData['resource_id']);

        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history
            , &$modelObj, &$isModify, &$hasResource, &$resourceIds){


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
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            // 同步修改图片资源关系
            if($hasResource){
//                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
//                // 更新图片资源表
//                if(!empty($resourceIds)) {
//                    $resourceArr = ['column_type' => 8, 'column_id' => $id];
//                    ResourceDBBusiness::saveByIds($resourceArr, $resourceIds);
//                }
                // ResourceDBBusiness::bathResourceSync(static::thisObj(), 8, [['id' => $id, 'resourceIds' => $resourceIds, 'otherData' => []]], $operate_staff_id, $operate_staff_id_history);
                ResourceDBBusiness::resourceSync(static::thisObj(), 512, $id, $resourceIds, [], $operate_staff_id, $operate_staff_id_history);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
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
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }

        return CommonDB::doTransactionFun(function() use(&$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$extendParams){
            // 删除资源及文件
            ResourceDBBusiness::delResourceByIds(static::thisObj(), $id, 512);
            // 删除记录
            static::deleteByIds($id);
            return $id;
        });
    }


    /**
     * 获得所有的收款方式的键值对，及不可使用的支付方式的值数组【一维】
     *
     * @param array $disablePayMethod 不可用的收款方式的值数组--一维数组， 可为空，或传入，想要禁和的收款方式
     * @param array $payKVList 支付方式的 KV数组 -- 一维数组, 可为空【默认--一般传空数组】--为空，则方法内会自动获取
     * @return  array [ '所有的收款方式的kv值数组', '所有不可用的收款方式的值数组--一维数组', '源始的收款方式数据--二维数组', '按状态格式化数据']
     * @author zouyan(305463219@qq.com)
     */
    public static function getPayMethodDisable(&$disablePayMethod, &$payKVList = []){
        $payMethodList = static::getDBFVFormatList(1, 1, [], true, '', [
            'sqlParams' => [
                'orderBy' => ['sort_num' => 'desc', 'id' => 'desc'],
        ]]);
        if(empty($payKVList)) $payKVList = Tool::formatArrKeyVal($payMethodList, 'pay_method', 'pay_name');

        // 按状态格式化数据
        $formatPayMethodList = Tool::arrUnderReset($payMethodList, 'status_online', 2, '_');
        $disableList = $formatPayMethodList[2] ?? [];
        $disablePayMethod = array_values(array_unique(array_merge($disablePayMethod, Tool::getArrFields($disableList, 'pay_method'))));
        return [$payKVList, $disablePayMethod, $payMethodList, $formatPayMethodList];
    }
}
