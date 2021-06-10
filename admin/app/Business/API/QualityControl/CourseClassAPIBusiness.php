<?php
// 培训班管理
namespace App\Business\API\QualityControl;


class CourseClassAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CourseClass';
    public static $table_name = 'course_class';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
