<?php
// 试题分析
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanySubjectAnalyseBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanySubjectAnalyse';
    public static $table_name = 'company_subject_analyse';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
