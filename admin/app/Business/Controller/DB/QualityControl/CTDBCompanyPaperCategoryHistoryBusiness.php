<?php
// 试卷题目
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyPaperCategoryHistoryBusiness extends CTDBCompanyPaperCategoryBusiness
{
    public static $model_name = 'QualityControl\CompanyPaperCategoryHistory';
    public static $table_name = 'company_paper_category_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
