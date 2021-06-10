<?php
// 订单发票关联表
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBInvoiceOrderBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\InvoiceOrder';
    public static $table_name = 'invoice_order';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
