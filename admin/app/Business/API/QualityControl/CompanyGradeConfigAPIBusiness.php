<?php
// 企业会员等级配置
namespace App\Business\API\QualityControl;


class CompanyGradeConfigAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyGradeConfig';
    public static $table_name = 'company_grade_config';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
