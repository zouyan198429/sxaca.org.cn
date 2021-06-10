<?php
// 实验室地址
namespace App\Business\API\QualityControl;


class LaboratoryAddrAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\LaboratoryAddr';
    public static $table_name = 'laboratory_addr';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
