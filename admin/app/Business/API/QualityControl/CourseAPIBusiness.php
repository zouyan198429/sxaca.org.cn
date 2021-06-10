<?php
// 课程管理
namespace App\Business\API\QualityControl;


class CourseAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\Course';
    public static $table_name = 'course';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
