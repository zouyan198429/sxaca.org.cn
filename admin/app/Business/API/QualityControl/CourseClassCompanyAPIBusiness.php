<?php
// 培训班企业管理
namespace App\Business\API\QualityControl;


class CourseClassCompanyAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CourseClassCompany';
    public static $table_name = 'course_class_company';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
