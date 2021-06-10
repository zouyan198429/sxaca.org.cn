<?php
// 发票配置沪友
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBInvoiceConfigHydzfpBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\InvoiceConfigHydzfp';
    public static $table_name = 'invoice_config_hydzfp';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
