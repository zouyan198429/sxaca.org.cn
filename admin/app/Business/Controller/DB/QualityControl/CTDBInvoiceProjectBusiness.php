<?php
// 发票项目明细
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBInvoiceProjectBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\InvoiceProject';
    public static $table_name = 'invoice_project';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
