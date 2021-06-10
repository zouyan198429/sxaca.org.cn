<?php
// 企业会员等级操作日志
namespace App\Business\DB\QualityControl;

/**
 *
 */
class CompanyGradeLogDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CompanyGradeLog';
    public static $table_name = 'company_grade_log';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];
    /**
     * 日志
     *
     * @param int $company_id 所属企业id-没有则为0
     * @param int $grade_config_id  企业会员等级配置id-没有则为0
     * @param string $begin_date  开始时间
     * @param string $end_date  到期时间
     * @param int $company_grade_old  企业--会员等级[原始]-没有则为0
     * @param int $company_grade  企业--会员等级[最终]-没有则为0
     * @param string $logContent 操作说明
     * @param int $operate_staff_id 操作员工id
     * @param int $operate_staff_history_id 操作员工历史id
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function saveGradeLog($company_id = 0, $grade_config_id = 0, $begin_date = '', $end_date = '', $company_grade_old = 0, $company_grade = 0, $logContent = '', $operate_staff_id = 0, $operate_staff_id_history = 0){

//         $currentNow = Carbon::now();
//         $currentNow->toDateString();
        // 能力验证报名操作日志
        $Record = [
            'company_id' => $company_id,// 所属企业id-没有则为0
            'grade_config_id' => $grade_config_id,// 企业会员等级配置id--没有则为0
            'begin_date' => $begin_date,// 开始时间
            'end_date' => $end_date,// 到期时间
            'company_grade_old' => $company_grade_old,// 企业--会员等级[原始]--没有则为0
            'company_grade' => $company_grade,// 企业--会员等级[最终]--没有则为0
            'content' => $logContent, // 记录内容
            'staff_id' => $operate_staff_id,
            'operate_staff_id' => $operate_staff_id,//$orderObj->operate_staff_id, // 操作员工id
            'operate_staff_id_history' => $operate_staff_id_history,//$orderObj->operate_staff_id_history,// 操作员工历史id
        ];
        static::create($Record);
    }
}
