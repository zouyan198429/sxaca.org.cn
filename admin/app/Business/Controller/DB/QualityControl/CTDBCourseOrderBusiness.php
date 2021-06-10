<?php
// 报名企业(主表)
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCourseOrderBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CourseOrder';
    public static $table_name = 'course_order';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
