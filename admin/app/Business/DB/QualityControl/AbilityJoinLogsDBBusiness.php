<?php
// 能力验证操作日志
namespace App\Business\DB\QualityControl;

use Carbon\Carbon;

/**
 *
 */
class AbilityJoinLogsDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinLogs';
    public static $table_name = 'ability_join_logs';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 日志
     *
     * @param int $admin_type 操作人的类型1平台2企业4个人
     * @param int $staff_id 操作人员id
     * @param int $ability_join_id  所属报名主表--没有则为0
     * @param int $ability_join_item_id  所属能力验证报名项--没有则为0
     * @param string $logContent 操作说明
     * @param int $operate_staff_id 操作员工id
     * @param int $operate_staff_history_id 操作员工历史id
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function saveAbilityJoinLog($admin_type = 2, $staff_id = 0, $ability_join_id = 0, $ability_join_item_id = 0, $logContent = '', $operate_staff_id = 0, $operate_staff_id_history = 0){

//         $currentNow = Carbon::now();
//         $currentNow->toDateString();
         // 能力验证报名操作日志
        $Record = [
            'admin_type' => $admin_type,// 类型1平台2企业4个人
            'staff_id' => $staff_id,// 操作人员id
            'ability_join_id' => $ability_join_id,// 所属报名主表--没有则为0
            'ability_join_item_id' => $ability_join_item_id,// 所属能力验证报名项--没有则为0
            'content' => $logContent, // 记录内容
            'operate_staff_id' => $operate_staff_id,//$orderObj->operate_staff_id, // 操作员工id
            'operate_staff_id_history' => $operate_staff_id_history,//$orderObj->operate_staff_id_history,// 操作员工历史id
        ];
        static::create($Record);
    }
}
