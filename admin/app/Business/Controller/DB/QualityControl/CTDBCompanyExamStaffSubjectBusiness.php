<?php
// 考次的人员试题答案
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyExamStaffSubjectBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyExamStaffSubject';
    public static $table_name = 'company_exam_staff_subject';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
