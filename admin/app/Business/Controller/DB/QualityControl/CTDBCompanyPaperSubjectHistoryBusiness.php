<?php
// 试卷试题
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyPaperSubjectHistoryBusiness extends CTDBCompanyPaperSubjectBusiness
{
    public static $model_name = 'QualityControl\CompanyPaperSubjectHistory';
    public static $table_name = 'company_paper_subject_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
