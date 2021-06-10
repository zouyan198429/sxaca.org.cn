<?php
// 付款/收款项目字段
namespace App\Business\API\QualityControl;


class PaymentProjectFieldsAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\PaymentProjectFields';
    public static $table_name = 'payment_project_fields';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
