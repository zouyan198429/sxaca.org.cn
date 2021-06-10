<?php
// 试题分析历史
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanySubjectAnalyseHistoryBusiness extends CTDBCompanySubjectAnalyseBusiness
{
    public static $model_name = 'QualityControl\CompanySubjectAnalyseHistory';
    public static $table_name = 'company_subject_analyse_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
