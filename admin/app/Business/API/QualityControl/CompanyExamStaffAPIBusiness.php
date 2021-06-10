<?php
// 考次的人员
namespace App\Business\API\QualityControl;


class CompanyExamStaffAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyExamStaff';
    public static $table_name = 'company_exam_staff';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
