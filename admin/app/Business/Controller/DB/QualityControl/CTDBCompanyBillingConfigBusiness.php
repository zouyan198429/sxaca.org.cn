<?php
// 企业开票配置信息
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyBillingConfigBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyBillingConfig';
    public static $table_name = 'company_billing_config';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
