<?php
// 企业其它【新闻】
namespace App\Business\API\QualityControl;


class CompanyNewsAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyNews';
    public static $table_name = 'company_news';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
