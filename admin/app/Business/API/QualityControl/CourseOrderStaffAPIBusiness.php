<?php
// 报名学员
namespace App\Business\API\QualityControl;


class CourseOrderStaffAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CourseOrderStaff';
    public static $table_name = 'course_order_staff';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
