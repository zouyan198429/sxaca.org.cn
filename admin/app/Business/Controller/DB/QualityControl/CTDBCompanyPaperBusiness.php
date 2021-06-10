<?php
// 试卷
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyPaperBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyPaper';
    public static $table_name = 'company_paper';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
