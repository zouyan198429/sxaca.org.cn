<?php
// 考次的人员试题答案
namespace App\Business\API\QualityControl;


class CompanyExamStaffSubjectAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyExamStaffSubject';
    public static $table_name = 'company_exam_staff_subject';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
