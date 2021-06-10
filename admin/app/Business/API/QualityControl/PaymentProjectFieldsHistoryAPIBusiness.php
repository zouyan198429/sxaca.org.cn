<?php
// 付款/收款项目字段历史
namespace App\Business\API\QualityControl;


class PaymentProjectFieldsHistoryAPIBusiness extends PaymentProjectFieldsAPIBusiness
{
    public static $model_name = 'QualityControl\PaymentProjectFieldsHistory';
    public static $table_name = 'payment_project_fields_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
