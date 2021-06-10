<?php
// 企业能力附表
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyScheduleBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanySchedule';
    public static $table_name = 'company_schedule';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
