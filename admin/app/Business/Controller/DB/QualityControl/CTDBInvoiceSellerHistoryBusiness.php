<?php
// 发票配置销售方历史
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBInvoiceSellerHistoryBusiness extends CTDBInvoiceSellerBusiness
{
    public static $model_name = 'QualityControl\InvoiceSellerHistory';
    public static $table_name = 'invoice_seller_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
