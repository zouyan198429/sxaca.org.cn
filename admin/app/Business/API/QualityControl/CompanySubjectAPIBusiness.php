<?php
// 试题
namespace App\Business\API\QualityControl;


class CompanySubjectAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanySubject';
    public static $table_name = 'company_subject';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
