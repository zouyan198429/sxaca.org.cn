<?php
// 面授操作日志
namespace App\Business\API\QualityControl;


class CourseLogAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CourseLog';
    public static $table_name = 'course_log';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
