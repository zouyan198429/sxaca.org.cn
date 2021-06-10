<?php
// 付款/收款项目历史
namespace App\Business\API\QualityControl;


class PaymentProjectHistoryAPIBusiness extends PaymentProjectAPIBusiness
{
    public static $model_name = 'QualityControl\PaymentProjectHistory';
    public static $table_name = 'payment_project_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
