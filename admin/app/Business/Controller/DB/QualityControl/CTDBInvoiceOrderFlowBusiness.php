<?php
// 订单发票流水
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBInvoiceOrderFlowBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\InvoiceOrderFlow';
    public static $table_name = 'invoice_order_flow';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
