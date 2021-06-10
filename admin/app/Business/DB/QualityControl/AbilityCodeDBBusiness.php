<?php
// 能力验证代码
namespace App\Business\DB\QualityControl;

use App\Services\Tool;
use Carbon\Carbon;

/**
 *
 */
class AbilityCodeDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\AbilityCode';
    public static $table_name = 'ability_code';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    // 获得最新的能力验证代码编号 // 单号 生成  2020NLYZ0001
    public static function getAbilityCode(){
        $currentNow = Carbon::now();
        $year = $currentNow->year;
        // 查询
//        $queryParams = [
//            'where' => [
//                // ['id', '&' , '4=4'],
//                ['number_year', '=' ,$year],
//                //['admin_type',self::$admin_type],
//            ],
//            //            'select' => [
//            //                'id','company_id','type_name','sort_num'
//            //                //,'operate_staff_id','operate_staff_history_id'
//            //                ,'created_at'
//            //            ],
//            // 'orderBy' => ['id'=>'desc'],
//        ];
        $queryParams = Tool::getParamQuery(['number_year' => $year], [], []);
        $infoData = static::getInfoByQuery(1, $queryParams, []);
        $current_num = 0;
        if(empty($infoData)){
            $dataParams = [
                'number_year' => $year,
                'current_num' => 1,
            ];
            static::create($dataParams);

        }else{
            $current_num = $infoData['current_num'];
            // 自增1
            static::saveDecIncByQuery('current_num', 1,  'inc', $queryParams, []);
        }
        $current_num++;
        $length = 4;
        // 单号 生成  2020NLYZ0001
        return $year . 'NLYZ' . str_pad(substr($current_num, -$length), $length, 0, STR_PAD_LEFT);;
    }
}
