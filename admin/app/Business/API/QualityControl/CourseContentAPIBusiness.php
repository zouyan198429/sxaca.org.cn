<?php
// 课程详细内容管理
namespace App\Business\API\QualityControl;


class CourseContentAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CourseContent';
    public static $table_name = 'course_content';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
