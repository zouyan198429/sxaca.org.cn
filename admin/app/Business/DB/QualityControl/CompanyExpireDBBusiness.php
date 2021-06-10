<?php
// 企业到期配置
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class CompanyExpireDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CompanyExpire';
    public static $table_name = 'company_expire';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 根据id删除--可批量删除-可以重写此方法
     * @param int  $company_id 企业id
     * @param string $id id 多个用，号分隔  或 一维数组
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @param array $extendParams 其它参数--扩展用参数
     * [
     *      'primary_field' => 'id',// 主键字段 默认：id
     *      'sqlParams' => [// 其它sql条件[拼接/覆盖式],下面是常用的，其它的也可以---查询用
     *          // '如果有值，则替换where' --拼接
     *          'where' => [// -- 可填 如 默认条件 'type_id' => 5  'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']
     *          ['type_id', 5],
     *          ],
     *          'select' => '如果有值，则替换select',// --覆盖
     *          'orderBy' => '如果有值，则替换orderBy',//--覆盖
     *          'whereIn' => '如果有值，则替换whereIn',// --拼接
     *          'whereNotIn' => '如果有值，则替换whereNotIn',//  --拼接
     *          'whereBetween' => '如果有值，则替换whereBetween',//  --拼接
     *          'whereNotBetween' => '如果有值，则替换whereNotBetween',//  --拼接
     *      ],
     * ]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){

        if(is_string($id) && strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }
        if(empty($id)){
            throws('操作记录标识不能为空！');
        }

//        $info = static::getInfo($id);
//        if(empty($info)) throws('记录不存在');
//        $staff_id = $info['staff_id'];
//        $dataListObj = null;
        $dataListArr = [];
        $abilityIds = [];
        $primary_field = $extendParams['primary_field'] ?? 'id';
        // 获得需要删除的数据
        $queryParams = [
            'where' => [
//                ['company_id', $organize_id],
                //  ['admin_type', $admin_type],
//                ['teacher_status',1],
            ],
            // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
        ];
        // 对数据进行拼接处理
        if(isset($extendParams['sqlParams'])){
            $sqlParams = $extendParams['sqlParams'] ?? [];
            foreach($sqlParams as $tKey => $tVals){
                if(isset($queryParams[$tKey]) && in_array($tKey, ['where',  'whereIn', 'whereNotIn', 'whereBetween', 'whereNotBetween'])){// 'select', 'orderBy',
                    $queryParams[$tKey] = array_merge($queryParams[$tKey], $tVals);
                }else{
                    $queryParams[$tKey] = $tVals;
                }

            }
            unset($extendParams['sqlParams']);
        }
        Tool::appendParamQuery($queryParams, $id, $primary_field, [0, '0', ''], ',', false);

//        $dataListObj = static::getAllList($queryParams, []);
//        // $dataListObj = static::getListByIds($id);
//
//        $dataListArr = $dataListObj->toArray();
//        if(empty($dataListArr)) throws('没有需要删除的数据');
//        // 用户删除要用到的
//        $abilityIds = array_values(array_unique(array_column($dataListArr,'ability_id')));

//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        CommonDB::doTransactionFun(function() use(&$queryParams){

            // 删除主记录
            static::del($queryParams);
            // static::deleteByIds($id);
            // 如果是删除，则减少报名数量
//            foreach($abilityIds as $ability_id){
//                if($ability_id > 0){
//                    $queryParams = [
//                        'where' => [
//                            ['id', $ability_id],
//                        ],
//                        // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//                    ];
//                    AbilitysDBBusiness::saveDecIncByQuery('join_num', 1,  'dec', $queryParams, []);
//                }
//            }
        });
        return $id;
    }


}
