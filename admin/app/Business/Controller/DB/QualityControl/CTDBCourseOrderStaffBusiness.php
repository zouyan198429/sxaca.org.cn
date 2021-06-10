<?php
// 报名学员
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCourseOrderStaffBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CourseOrderStaff';
    public static $table_name = 'course_order_staff';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
