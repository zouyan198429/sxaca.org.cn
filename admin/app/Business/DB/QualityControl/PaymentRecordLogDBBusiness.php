<?php
// 付款/收款记录操作日志
namespace App\Business\DB\QualityControl;

/**
 *
 */
class PaymentRecordLogDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\PaymentRecordLog';
    public static $table_name = 'payment_record_log';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];


    /**
     * 日志
     *
     * @param int $payment_record_id 所属记录id
     * @param array $info 所属记录--一维数组 ， 可为空数组
     * @param array / string $logContent 操作说明
     * @param int $operate_staff_id 操作员工id
     * @param int $operate_staff_history_id 操作员工历史id
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function saveLog($payment_record_id, $info = [], $logContent = '', $operate_staff_id = 0, $operate_staff_id_history = 0){
        if(empty($info)) $info = PaymentRecordDBBusiness::getInfo($payment_record_id);
//         $currentNow = Carbon::now();
//         $currentNow->toDateString();
        // 能力验证报名操作日志
//        $staffInfo = StaffDBBusiness::getDBFVFormatList(4, 1, ['id' => $operate_staff_id], false);
//        $admin_type = $staffInfo['admin_type'] ?? 0;
        $Record = [
            'company_id' => $info['company_id'] ?? 0,// 公司ID
            'payment_project_id' => $info['payment_project_id'] ?? 0,// 所属付款/收款项目id
            'payment_project_id_history' => $info['payment_project_id_history'] ?? 0,// 所属付款/收款项目id历史
            'payment_record_id' => $payment_record_id,// 所属记录id
//            'course_order_id' => $course_order_id,// 所属报名企业(主表)--没有则为0
//            'class_id' => $class_id,// 所属培训班id(分班)(可为0)
//            'class_company_id' => $class_company_id,// 所属培训班企业id(分班)
//            'course_staff_id' => $course_staff_id,// 报名学员表id(可为0)--没有则为0
           // 'admin_type' => $admin_type,// 类型1平台2企业4个人--没有则为0
            'staff_id' => $operate_staff_id,
            'content' => (is_array($logContent)) ? json_encode($logContent) : $logContent, // 记录内容
            'operate_staff_id' => $operate_staff_id,//$orderObj->operate_staff_id, // 操作员工id
            'operate_staff_id_history' => $operate_staff_id_history,//$orderObj->operate_staff_id_history,// 操作员工历史id
        ];
        static::create($Record);
    }
}
