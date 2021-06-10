<?php
// 课程详细内容管理
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCourseContentBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CourseContent';
    public static $table_name = 'course_content';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
