<?php
// 机构自我声明管理
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyStatementBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyStatement';
    public static $table_name = 'company_statement';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
