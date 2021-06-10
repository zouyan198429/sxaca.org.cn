<?php
// 企业会员等级操作日志
namespace App\Business\API\QualityControl;


class CompanyGradeLogAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyGradeLog';
    public static $table_name = 'company_grade_log';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
