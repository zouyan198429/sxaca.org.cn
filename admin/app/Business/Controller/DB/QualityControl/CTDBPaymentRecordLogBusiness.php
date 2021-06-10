<?php
// 付款/收款记录操作日志
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBPaymentRecordLogBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\PaymentRecordLog';
    public static $table_name = 'payment_record_log';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // public static $orderBy = AaaaaBbbb::ORDER_BY;// ['sort_num' => 'desc', 'id' => 'desc'];// 默认的排序字段数组 ['id' => 'desc']--默认 或 ['sort_num' => 'desc', 'id' => 'desc']

}
