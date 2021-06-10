<?php
// 考次的人员
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyExamStaffBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyExamStaff';
    public static $table_name = 'company_exam_staff';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
