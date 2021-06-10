<?php
// 试卷历史
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyPaperHistoryBusiness extends CTDBCompanyPaperBusiness
{
    public static $model_name = 'QualityControl\CompanyPaperHistory';
    public static $table_name = 'company_paper_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
