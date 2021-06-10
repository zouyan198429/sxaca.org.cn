<?php
// 企业其它内容【新闻】
namespace App\Business\API\QualityControl;


class CompanyNewContentAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyNewContent';
    public static $table_name = 'company_new_content';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
