<?php
// 分数等级
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyCoreGradeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyCoreGrade';
    public static $table_name = 'company_core_grade';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
