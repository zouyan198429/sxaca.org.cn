<?php
// 发票配置销售方
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBInvoiceSellerBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\InvoiceSeller';
    public static $table_name = 'invoice_seller';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
