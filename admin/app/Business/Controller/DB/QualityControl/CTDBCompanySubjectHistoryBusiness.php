<?php
// 试题历史
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanySubjectHistoryBusiness extends CTDBCompanySubjectBusiness
{
    public static $model_name = 'QualityControl\CompanySubjectHistory';
    public static $table_name = 'company_subject_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
