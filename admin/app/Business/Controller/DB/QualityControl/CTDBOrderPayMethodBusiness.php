<?php
// 收款方式配置
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBOrderPayMethodBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\OrderPayMethod';
    public static $table_name = 'order_pay_method';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
