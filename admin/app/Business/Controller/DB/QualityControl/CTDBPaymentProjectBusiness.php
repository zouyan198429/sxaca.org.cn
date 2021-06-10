<?php
// 付款/收款项目
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBPaymentProjectBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\PaymentProject';
    public static $table_name = 'payment_project';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // public static $orderBy = AaaaaBbbb::ORDER_BY;// ['sort_num' => 'desc', 'id' => 'desc'];// 默认的排序字段数组 ['id' => 'desc']--默认 或 ['sort_num' => 'desc', 'id' => 'desc']

}
