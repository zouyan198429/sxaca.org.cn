<?php
// 检验检测机构资质认定证书附表-名称表【只增，不改不删】
namespace App\Business\DB\QualityControl;

/**
 *
 */
class CertificateNamesDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CertificateNames';
    public static $table_name = 'certificate_names';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 根据名称，返回名称表id,没有则返回0
     *
     * @param string $main_name 名称
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人历史id
     * @return int 名称表id
     * @author zouyan(305463219@qq.com)
     */
    public static function getNameId($main_name, $operate_staff_id = 0, &$operate_staff_id_history = 0){
        if(!empty($main_name)){
            $nameObj = null ;
            $searchConditon = [
                'type_name' => $main_name
            ];
            $updateFields = [];
            static::addOprate($updateFields, $operate_staff_id,$operate_staff_id_history, 1);
            static::firstOrCreate($nameObj, $searchConditon, $updateFields);
            return $nameObj->id;
        }
        return 0;
    }
}
