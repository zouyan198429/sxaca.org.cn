<?php
// 帐号管理
namespace App\Business\API\QualityControl;


class StaffAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\Staff';
    public static $table_name = 'staff';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
