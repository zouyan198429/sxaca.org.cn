<?php
// 证书导入批次
namespace App\Business\DB\QualityControl;

use App\Services\Tool;

/**
 *
 */
class CertificateImportLogDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CertificateImportLog';
    public static $table_name = 'certificate_import_log';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 日志
     *
     * @param array $dataList 需要导入的数据 -- 二维数组
     * @param int $staff_id 操作人员id
     * @param int $ability_join_id  所属报名主表--没有则为0
     * @param int $ability_join_item_id  所属能力验证报名项--没有则为0
     * @param string $logContent 操作说明
     * @param int $operate_staff_id 操作员工id
     * @param int $operate_staff_history_id 操作员工历史id
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function saveImportLog($dataList = [], $operate_staff_id = 0, $operate_staff_id_history = 0){

//         $currentNow = Carbon::now();
//         $currentNow->toDateString();
        // 能力验证报名操作日志
        $Record = [
            'import_no' => date('YmdHis') . Tool::getRandNum(0, 10),// 导入批次
            'import_time' => date('Y-m-d H:i:s'),// 导入时间
            'success_num' => count($dataList),// 所属报名主表--没有则为0
            'fail_num' => 0,// 所属能力验证报名项--没有则为0
            'import_content' => json_encode($dataList), // 记录内容
            'operate_staff_id' => $operate_staff_id,//$orderObj->operate_staff_id, // 操作员工id
            'operate_staff_id_history' => $operate_staff_id_history,//$orderObj->operate_staff_id_history,// 操作员工历史id
        ];
        static::create($Record);
    }
}
