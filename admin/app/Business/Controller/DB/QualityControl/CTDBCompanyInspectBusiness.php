<?php
// 监督检查
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyInspectBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyInspect';
    public static $table_name = 'company_inspect';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
