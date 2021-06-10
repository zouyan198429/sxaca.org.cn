<?php
// 考次
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyExamBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyExam';
    public static $table_name = 'company_exam';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
