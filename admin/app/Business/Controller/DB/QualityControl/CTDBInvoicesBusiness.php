<?php
// 发票主表
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBInvoicesBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\Invoices';
    public static $table_name = 'invoices';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
