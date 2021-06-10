<?php
namespace App\ModelsVerify\QualityControl;

use OpenApi\Annotations as OA;

class staff extends BaseDBVerify
{
    public static $model_name = 'QualityControl\Staff';// 相对于Models的数据模型名称;在子类中定义，使用时用static::,不用self::
    public static $table_name = 'staff';// 数据表名称
    // 需要从父的去掉的字段  -- 一维数组
    // 如 ['version_history_id', 'version_num_history']
    public static $delFields = [];

    /**
     * 获得验证规则
     * // 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择***
     * @param array $langModel 多语言公用配置数组
     * @param array $langModelDB 多语言单个数据库配置数组
     * @param array $langTable 多语言单个数据表配置数组
     * @return array  单个数据表配置数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getTableConfig($dbFileTag = 'models', $langModel = [], $langModelDB = [], $langTable = []){
        $tableConfig = [
            'fields' => [
                'id' => [
                    "field_name" => '',//  $langModel['field_names']['id'] ?? 'id',
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'admin_type' => [
                    "field_name" => '',// '拥有者类型',
                    'data_type'=>'tinyint',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"custom","regexp"=>"/^([01248]|16)$/","message" => '',// '{fieldName}' . ($langModel['validValueIs'] ?? '')  . '（1平台2城市分站4城市代理8商家16店铺32快跑人员64用户）'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'admin_username' => [
                    "field_name" => '',// '用户名',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"30","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 30 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'admin_password' => [
                    "field_name" => '',// '密码',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"100","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 100 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'issuper' => [
                    "field_name" => '',// '是否超级帐户',
                    'data_type'=>'tinyint',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"custom","regexp"=>"/^([012])$/","message" => '',// '{fieldName}' . ($langModel['validValueIs'] ?? '')  . '（0否1是）'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'open_status' => [
                    "field_name" => '',// '审核状态',
                    'data_type'=>'tinyint',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"custom","regexp"=>"/^([0124])$/","message" => '',// '{fieldName}' . ($langModel['validValueIs'] ?? '')  . '（1待审核2审核通过3审核未通过--32快跑人员用）'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'open_fail_reason' => [
                    "field_name" => '',// '审核失败原因',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"100","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 100 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'account_status' => [
                    "field_name" => '',// '状态',
                    'data_type'=>'tinyint',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"custom","regexp"=>"/^([012])$/","message" => '',// '{fieldName}' . ($langModel['validValueIs'] ?? '')  . '（0正常 1冻结）'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'frozen_fail_reason' => [
                    "field_name" => '',// '冻结原因',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"100","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 100 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'real_name' => [
                    "field_name" => '',// '真实姓名',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"100","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 100 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'sex' => [
                    "field_name" => '',// '性别',
                    'data_type'=>'smallint',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"custom","regexp"=>"/^([012])$/","message" => '',// '{fieldName}' . ($langModel['validValueIs'] ?? '')  . '（0未知1男2女）'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'tel' => [
                    "field_name" => '',//  $langModel['field_names']['tel'] ?? '', // '电话',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"25","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 25 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'mobile' => [
                    "field_name" => '',//  $langModel['field_names']['mobile'] ?? '', // '手机',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"30","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 30 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'qq_number' => [
                    "field_name" => '',// 'QQ',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"30","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 30 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'wechat' => [
                    "field_name" => '',// '审核失败原因',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"30","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 100 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'email' => [
                    "field_name" => '',// '审核失败原因',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"30","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 100 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'remarks' => [
                    "field_name" => '',// '审核失败原因',
                    'data_type'=>'varchar',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"length","min"=>"0","max"=>"200","message" => '',// '{fieldName}' . ($langModel['valueLenIs'] ?? '') . ' 0~ 100 ' . ($langModel['numsCharacters'] ?? '') . '!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'firstlogintime' => [
                    "field_name" => '',//  $langModel['field_names']['created_at'] ?? '', // '操作日期',
                    'data_type'=>'timestamp',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"datatime","message" => '',// '{fieldName}' . ($langModel['valMustDateTime'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'lastlogintime' => [
                    "field_name" => '',//  $langModel['field_names']['created_at'] ?? '', // '操作日期',
                    'data_type'=>'timestamp',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"datatime","message" => '',// '{fieldName}' . ($langModel['valMustDateTime'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'create_class_num' => [
                    "field_name" => '',//  $langModel['field_names']['sort_num'] ?? '', // '排序[降序]',
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'class_num' => [
                    "field_name" => '',//  $langModel['field_names']['sort_num'] ?? '', // '排序[降序]',
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'work_num' => [
                    "field_name" => '',//  $langModel['field_names']['sort_num'] ?? '', // '排序[降序]',
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'operate_staff_id' => [
                    "field_name" => '',//  $langModel['field_names']['operate_staff_id'] ?? '', // '操作员工',,
                    'data_type'=>'int',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"integer","message" => '',// '{fieldName}' . ($langModel['valMustInt'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'created_at' => [
                    "field_name" => '',//  $langModel['field_names']['created_at'] ?? '', // '操作日期',
                    'data_type'=>'timestamp',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"datatime","message" => '',// '{fieldName}' . ($langModel['valMustDateTime'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
                'updated_at' => [
                    "field_name" => '',//  $langModel['field_names']['updated_at'] ?? '', // '更新日期',
                    'data_type'=>'timestamp',//数据类型 int
                    // 'must_message' => '数据校验不通过，没有{fieldName}数据值！',// 必填时，无值的错误提示
                    'valiDateParam' => [// 参数有值时验证信息；如需要多个验证--此值为二维数组【注意：此时的多语言resources\lang\zh-CN 下具体的表文件中的message也有用一维数组】
                        // "input"=>$_POST["market_id"],"require"=>"false","var_name" => "queue" ,
                        "validator"=>"datatime","message" => '',// '{fieldName}' . ($langModel['valMustDateTime'] ?? '') .'!'
                    ],
                    'powerNum' => 0,// (2 | 4)  特殊权限[小心设置] 2新加时必填;8修改时如果有下标则判断必填[主键不要设置，不然新建不了] 4不可修改
                ],
            ]
        ];
        // 如果用父类的，则在此指定父类
        // $tableConfig = brands::getVerifyRuleArr($dbFileTag);
        return $tableConfig;
    }

    /**
     * 单个数据表配置数组中，需要新加的字段配置
     *
     * @param array $langModel 多语言公用配置数组
     * @param array $langModelDB 多语言单个数据库配置数组
     * @param array $langTable 多语言单个数据表配置数组
     * @return array  单个数据表配置数组中需要新加的字段配置-- field下标的值--数组
     * @author zouyan(305463219@qq.com)
     */
    public static function addFields($langModel = [], $langModelDB = [], $langTable = []){
        $addFields = [

        ];
        return $addFields;
    }

    //##################属性#######################################################

    /**
     * 个人访问客户端属性-品牌名称
     * @ OA\Schema(
     *   schema="Schema_QualityControl_staff_brand_name",
     *   type="string",
     *   title="个人访问客户端属性-品牌名称",
     *   description="个人访问客户端属性-品牌名称",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="川渝人家",
     * )
     *
     */

    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @ OA\Parameter(
     *      parameter="Schema_QualityControl_staff_brand_name_optional",
     *      name="type_name",
     *      in="query",
     *      description="类型名称",
     *      required=false,
     *      deprecated=false,
     *      @ OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_brand_name")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @ OA\Parameter(
     *      parameter="Schema_QualityControl_staff_brand_name_required",
     *      name="type_name",
     *      in="query",
     *      description="类型名称",
     *      required=true,
     *      deprecated=false,
     *      @ OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_brand_name")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************


    /**
     * 用户类型1平台2老师4学生
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_admin_type",
     *   type="integer",
     *   format="int32",
     *   title="用户类型",
     *   description="用户类型1平台2老师4学生",
     *   default=0,
     *   enum={"1","2","4"},
     *   example="1",
     * )
     *
     */

    /**
     * 用户类型文字
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_admin_type_text",
     *   type="string",
     *   title="用户类型文字",
     *   description="用户类型文字",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="否",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_admin_type_optional",
     *      name="admin_type",
     *      in="query",
     *      description="用户类型",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_admin_type")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_admin_type_required",
     *      name="admin_type",
     *      in="query",
     *      description="用户类型",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_admin_type")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_admin_type_text_optional",
     *      name="admin_type_text",
     *      in="query",
     *      description="用户类型",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_admin_type_text")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_admin_type_text_required",
     *      name="admin_type_text",
     *      in="query",
     *      description="用户类型",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_admin_type_text")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************


    /**
     * 人员属性-用户名
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_admin_username",
     *   type="string",
     *   title="用户名",
     *   description="用户名",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="admin",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_admin_username_optional",
     *      name="admin_username",
     *      in="query",
     *      description="用户名",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_admin_username")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_admin_username_required",
     *      name="admin_username",
     *      in="query",
     *      description="用户名",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_admin_username")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 人员属性-密码
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_admin_password",
     *   type="string",
     *   title="密码",
     *   description="密码",
     *   default="",
     *   minLength=0,
     *   maxLength=100,
     *   example="123456",
     * )
     *
     */



    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_admin_password_optional",
     *      name="admin_password",
     *      in="query",
     *      description="密码",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_admin_password")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_admin_password_required",
     *      name="admin_password",
     *      in="query",
     *      description="密码",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_admin_password")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 是否超级帐户1是2否
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_issuper",
     *   type="integer",
     *   format="int32",
     *   title="是否超级帐户",
     *   description="是否超级帐户1是2否",
     *   default=0,
     *   enum={"1","2"},
     *   example="1",
     * )
     *
     */

    /**
     * 是否超级帐户文字
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_issuper_text",
     *   type="string",
     *   title="是否超级帐户文字",
     *   description="是否超级帐户文字",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="否",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_issuper_optional",
     *      name="issuper",
     *      in="query",
     *      description="是否超级帐户",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_issuper")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_issuper_required",
     *      name="issuper",
     *      in="query",
     *      description="是否超级帐户",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_issuper")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_issuper_text_optional",
     *      name="issuper_text",
     *      in="query",
     *      description="是否超级帐户",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_issuper_text")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_issuper_text_required",
     *      name="issuper_text",
     *      in="query",
     *      description="是否超级帐户",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_issuper_text")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 审核状态1待审核2审核通过4审核不通过
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_open_status",
     *   type="integer",
     *   format="int32",
     *   title="审核状态",
     *   description="审核状态1待审核2审核通过4审核不通过",
     *   default=0,
     *   enum={"1","2","4"},
     *   example="1",
     * )
     *
     */

    /**
     * 审核状态文字
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_open_status_text",
     *   type="string",
     *   title="审核状态文字",
     *   description="审核状态文字",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="否",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_open_status_optional",
     *      name="open_status",
     *      in="query",
     *      description="审核状态",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_open_status")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_open_status_required",
     *      name="open_status",
     *      in="query",
     *      description="审核状态",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_open_status")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_open_status_text_optional",
     *      name="open_status_text",
     *      in="query",
     *      description="审核状态",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_open_status_text")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_open_status_text_required",
     *      name="open_status_text",
     *      in="query",
     *      description="审核状态",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_open_status_text")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 审核失败原因
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_open_fail_reason",
     *   type="string",
     *   title="审核失败原因",
     *   description="审核失败原因",
     *   default="",
     *   minLength=0,
     *   maxLength=100,
     *   example="小王",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_open_fail_reason_optional",
     *      name="open_fail_reason",
     *      in="query",
     *      description="审核失败原因",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_open_fail_reason")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_open_fail_reason_required",
     *      name="open_fail_reason",
     *      in="query",
     *      description="审核失败原因",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_open_fail_reason")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 状态 1正常 2冻结
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_account_status",
     *   type="integer",
     *   format="int32",
     *   title="状态",
     *   description="状态 1正常 2冻结",
     *   default=0,
     *   enum={"1","2"},
     *   example="1",
     * )
     *
     */

    /**
     * 状态文字
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_account_status_text",
     *   type="string",
     *   title="状态文字",
     *   description="状态文字",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="冻结",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_account_status_optional",
     *      name="account_status",
     *      in="query",
     *      description="状态",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_account_status")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_account_status_required",
     *      name="account_status",
     *      in="query",
     *      description="状态",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_account_status")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_account_status_text_optional",
     *      name="account_status_text",
     *      in="query",
     *      description="状态",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_account_status_text")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_account_status_text_required",
     *      name="account_status_text",
     *      in="query",
     *      description="状态",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_account_status_text")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************


    /**
     * 冻结原因
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_frozen_fail_reason",
     *   type="string",
     *   title="冻结原因",
     *   description="冻结原因",
     *   default="",
     *   minLength=0,
     *   maxLength=100,
     *   example="小王",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_frozen_fail_reason_optional",
     *      name="frozen_fail_reason",
     *      in="query",
     *      description="冻结原因",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_frozen_fail_reason")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_frozen_fail_reason_required",
     *      name="frozen_fail_reason",
     *      in="query",
     *      description="冻结原因",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_frozen_fail_reason")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 真实姓名
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_real_name",
     *   type="string",
     *   title="真实姓名",
     *   description="真实姓名",
     *   default="",
     *   minLength=0,
     *   maxLength=100,
     *   example="小王",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_real_name_optional",
     *      name="real_name",
     *      in="query",
     *      description="真实姓名",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_real_name")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_real_name_required",
     *      name="real_name",
     *      in="query",
     *      description="真实姓名",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_real_name")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 性别0未知1男2女
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_sex",
     *   type="integer",
     *   format="int32",
     *   title="性别",
     *   description="性别0未知1男2女",
     *   default=0,
     *   enum={"0","1","2"},
     *   example="1",
     * )
     *
     */

    /**
     * 性别文字
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_sex_text",
     *   type="string",
     *   title="性别文字",
     *   description="性别文字",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="男",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_sex_optional",
     *      name="sex",
     *      in="query",
     *      description="性别",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_sex")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_sex_required",
     *      name="sex",
     *      in="query",
     *      description="性别",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_sex")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_sex_text_optional",
     *      name="sex_text",
     *      in="query",
     *      description="性别",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_sex_text")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_sex_text_required",
     *      name="sex_text",
     *      in="query",
     *      description="性别",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_sex_text")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 电话
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_tel",
     *   type="string",
     *   title="电话",
     *   description="电话",
     *   default="",
     *   minLength=0,
     *   maxLength=25,
     *   example="029-88214602",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_tel_optional",
     *      name="tel",
     *      in="query",
     *      description="电话",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_tel")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_tel_required",
     *      name="tel",
     *      in="query",
     *      description="电话",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_tel")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 手机
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_mobile",
     *   type="string",
     *   title="手机",
     *   description="手机",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="15829686962",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_mobile_optional",
     *      name="mobile",
     *      in="query",
     *      description="手机",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_mobile")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_mobile_required",
     *      name="mobile",
     *      in="query",
     *      description="手机",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_mobile")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * QQ
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_qq_number",
     *   type="string",
     *   title="QQ",
     *   description="QQ",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="15829686962",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_qq_number_optional",
     *      name="qq_number",
     *      in="query",
     *      description="QQ",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_qq_number")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_qq_number_required",
     *      name="qq_number",
     *      in="query",
     *      description="QQ",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_qq_number")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 微信
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_wechat",
     *   type="string",
     *   title="微信",
     *   description="微信",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="15829686962",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_wechat_optional",
     *      name="wechat",
     *      in="query",
     *      description="微信",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_wechat")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_wechat_required",
     *      name="wechat",
     *      in="query",
     *      description="微信",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_wechat")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 邮箱
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_email",
     *   type="string",
     *   title="邮箱",
     *   description="邮箱",
     *   default="",
     *   minLength=0,
     *   maxLength=30,
     *   example="305463219@qq.com",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_email_optional",
     *      name="email",
     *      in="query",
     *      description="邮箱",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_email")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_email_required",
     *      name="email",
     *      in="query",
     *      description="邮箱",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_email")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 备注
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_remarks",
     *   type="string",
     *   title="备注",
     *   description="备注",
     *   default="",
     *   minLength=0,
     *   maxLength=200,
     *   example="超级管理人员",
     * )
     *
     */



    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_remarks_optional",
     *      name="remarks",
     *      in="query",
     *      description="备注",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_remarks")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_remarks_required",
     *      name="remarks",
     *      in="query",
     *      description="备注",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_remarks")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 初次登录
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_firstlogintime",
     *   type="string",
     *   format="date-time",
     *   title="初次登录",
     *   description="初次登录",
     *   example="2019-12-04 12:31:30",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_firstlogintime_optional",
     *      name="firstlogintime",
     *      in="query",
     *      description="初次登录",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_firstlogintime")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_firstlogintime_required",
     *      name="firstlogintime",
     *      in="query",
     *      description="初次登录",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_firstlogintime")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 上次登录[最近一次]
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_lastlogintime",
     *   type="string",
     *   format="date-time",
     *   title="上次登录时间[最近一次]",
     *   description="上次登录时间[最近一次]",
     *   example="2019-12-04 12:31:30",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_lastlogintime_optional",
     *      name="lastlogintime",
     *      in="query",
     *      description="字段说明",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_lastlogintime")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_lastlogintime_required",
     *      name="lastlogintime",
     *      in="query",
     *      description="字段说明",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_lastlogintime")
     * ),
     *
     *
     */

    /**
     * 创建班级数量
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_create_class_num",
     *   type="integer",
     *   format="int32",
     *   title="创建班级数量",
     *   description="创建班级数量",
     *   default=0,
     *   minimum="0",
     *   example="1",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_create_class_num_optional",
     *      name="create_class_num",
     *      in="query",
     *      description="创建班级数量",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_create_class_num")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_create_class_num_required",
     *      name="create_class_num",
     *      in="query",
     *      description="创建班级数量",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_create_class_num")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 所属班级数量
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_class_num",
     *   type="integer",
     *   format="int32",
     *   title="所属班级数量",
     *   description="所属班级数量",
     *   default=0,
     *   minimum="0",
     *   example="1",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_class_num_optional",
     *      name="class_num",
     *      in="query",
     *      description="所属班级数量",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_class_num")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_class_num_required",
     *      name="class_num",
     *      in="query",
     *      description="所属班级数量",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_class_num")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    /**
     * 作品数量
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_work_num",
     *   type="integer",
     *   format="int32",
     *   title="作品数量",
     *   description="作品数量",
     *   default=0,
     *   minimum="0",
     *   example="1",
     * )
     *
     */


    //***********************字段查询***开始*******************************
    // 上面字段对应的查询参数
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_work_num_optional",
     *      name="work_num",
     *      in="query",
     *      description="作品数量",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_work_num")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_work_num_required",
     *      name="work_num",
     *      in="query",
     *      description="作品数量",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_work_num")
     * ),
     *
     *
     */
    //***********************字段查询***结束****************************

    //***********************字段查询***结束****************************

    // 其它表会用到的属性字段

    /**
     * 个人访问客户端属性-id
     * @OA\Schema(
     *   schema="Schema_QualityControl_staff_id",
     *   type="integer",
     *   format="int32",
     *   title="个人访问客户端属性-id",
     *   description="个人访问客户端属性-id",
     *   default=0,
     *   minimum="0",
     *   example="1",
     * )
     *
     */

    // id 类型 ---自已用的
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_id_optional",
     *      name="id",
     *      in="query",
     *      description="用户-id",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_id")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_id_required",
     *      name="id",
     *      in="query",
     *      description="用户-id",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_id")
     * ),
     *
     *
     */

    // **_id 类型 ---别的表引用的
    /**
     *
     * 上面字段对应的查询参数--可填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_id_optional_quote",
     *      name="administrator_id",
     *      in="query",
     *      description="用户-id",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_id")
     * ),
     *
     *
     */

    /**
     *
     * 上面字段对应的查询参数--必填
     * @OA\Parameter(
     *      parameter="Schema_QualityControl_staff_id_required_quote",
     *      name="administrator_id",
     *      in="query",
     *      description="用户-id",
     *      required=true,
     *      deprecated=false,
     *      @OA\Schema(ref="#/components/schemas/Schema_QualityControl_staff_id")
     * ),
     *
     *
     */



    //##################请求参数#######################################################
    /**
     * 模糊查询字段名--具体的表模型用
     * @OA\Parameter(
     *      parameter="Parameter_QualityControl_staff_field",
     *      name="field",
     *      in="query",
     *      description="模糊查询字段名",
     *      required=false,
     *      deprecated=false,
     *      @OA\Schema(
     *          type="string",
     *          default="字段名1",
     *          enum={"字段名1","字段名2"},
     *          example="字段名1",
     *      )
     * ),
     *
     */

    //##################对象属性集#######################################################
    // 有所有字段的对象
    /**
     * @OA\Schema(
     *     schema="Schema_QualityControl_staff_obj",
     *     title="个人访问客户端",
     *     description="个人访问客户端列表",
     *     required={},
     *     @OA\Property(property="id", ref="#/components/schemas/common_Schema_id"),
     *     @OA\Property(property="admin_type", ref="#/components/schemas/Schema_QualityControl_staff_admin_type"),
     *     @OA\Property(property="admin_type_text", ref="#/components/schemas/Schema_QualityControl_staff_admin_type_text"),
     *     @OA\Property(property="admin_username", ref="#/components/schemas/Schema_QualityControl_staff_admin_username"),
     *     @OA\Property(property="admin_password", ref="#/components/schemas/Schema_QualityControl_staff_admin_password"),
     *     @OA\Property(property="issuper", ref="#/components/schemas/Schema_QualityControl_staff_issuper"),
     *     @OA\Property(property="issuper_text", ref="#/components/schemas/Schema_QualityControl_staff_issuper_text"),
     *     @OA\Property(property="open_status", ref="#/components/schemas/Schema_QualityControl_staff_open_status"),
     *     @OA\Property(property="open_status_text", ref="#/components/schemas/Schema_QualityControl_staff_open_status_text"),
     *     @OA\Property(property="open_fail_reason", ref="#/components/schemas/Schema_QualityControl_staff_open_fail_reason"),
     *     @OA\Property(property="account_status", ref="#/components/schemas/Schema_QualityControl_staff_account_status"),
     *     @OA\Property(property="account_status_text", ref="#/components/schemas/Schema_QualityControl_staff_account_status_text"),
     *     @OA\Property(property="frozen_fail_reason", ref="#/components/schemas/Schema_QualityControl_staff_frozen_fail_reason"),
     *     @OA\Property(property="real_name", ref="#/components/schemas/Schema_QualityControl_staff_real_name"),
     *     @OA\Property(property="sex", ref="#/components/schemas/Schema_QualityControl_staff_sex"),
     *     @OA\Property(property="sex_text", ref="#/components/schemas/Schema_QualityControl_staff_sex_text"),
     *     @OA\Property(property="tel", ref="#/components/schemas/Schema_QualityControl_staff_tel"),
     *     @OA\Property(property="mobile", ref="#/components/schemas/Schema_QualityControl_staff_mobile"),
     *     @OA\Property(property="qq_number", ref="#/components/schemas/Schema_QualityControl_staff_qq_number"),
     *     @OA\Property(property="wechat", ref="#/components/schemas/Schema_QualityControl_staff_wechat"),
     *     @OA\Property(property="email", ref="#/components/schemas/Schema_QualityControl_staff_email"),
     *     @OA\Property(property="remarks", ref="#/components/schemas/Schema_QualityControl_staff_remarks"),
     *     @OA\Property(property="firstlogintime", ref="#/components/schemas/Schema_QualityControl_staff_firstlogintime"),
     *     @OA\Property(property="lastlogintime", ref="#/components/schemas/Schema_QualityControl_staff_lastlogintime"),
     *     @OA\Property(property="create_class_num", ref="#/components/schemas/Schema_QualityControl_staff_create_class_num"),
     *     @OA\Property(property="class_num", ref="#/components/schemas/Schema_QualityControl_staff_class_num"),
     *     @OA\Property(property="work_num", ref="#/components/schemas/Schema_QualityControl_staff_work_num"),
     *     @OA\Property(property="operate_staff_id", ref="#/components/schemas/common_Schema_operate_staff_id"),
     *     @OA\Property(property="created_at", ref="#/components/schemas/common_Schema_created_at"),
     *     @OA\Property(property="updated_at", ref="#/components/schemas/common_Schema_updated_at"),
     * )
     */

    // 有所有字段的对象
    /**
     * @OA\Schema(
     *     schema="Schema_QualityControl_staff_obj_login",
     *     title="人员",
     *     description="人员列表",
     *     required={},
     *     @OA\Property(property="id", ref="#/components/schemas/common_Schema_id"),
     *     @OA\Property(property="admin_type", ref="#/components/schemas/Schema_QualityControl_staff_admin_type"),
     *     @OA\Property(property="admin_type_text", ref="#/components/schemas/Schema_QualityControl_staff_admin_type_text"),
     *     @OA\Property(property="admin_username", ref="#/components/schemas/Schema_QualityControl_staff_admin_username"),
     *     @OA\Property(property="admin_password", ref="#/components/schemas/Schema_QualityControl_staff_admin_password"),
     *     @OA\Property(property="issuper", ref="#/components/schemas/Schema_QualityControl_staff_issuper"),
     *     @OA\Property(property="issuper_text", ref="#/components/schemas/Schema_QualityControl_staff_issuper_text"),
     *     @OA\Property(property="open_status", ref="#/components/schemas/Schema_QualityControl_staff_open_status"),
     *     @OA\Property(property="open_status_text", ref="#/components/schemas/Schema_QualityControl_staff_open_status_text"),
     *     @OA\Property(property="open_fail_reason", ref="#/components/schemas/Schema_QualityControl_staff_open_fail_reason"),
     *     @OA\Property(property="account_status", ref="#/components/schemas/Schema_QualityControl_staff_account_status"),
     *     @OA\Property(property="account_status_text", ref="#/components/schemas/Schema_QualityControl_staff_account_status_text"),
     *     @OA\Property(property="frozen_fail_reason", ref="#/components/schemas/Schema_QualityControl_staff_frozen_fail_reason"),
     *     @OA\Property(property="real_name", ref="#/components/schemas/Schema_QualityControl_staff_real_name"),
     *     @OA\Property(property="sex", ref="#/components/schemas/Schema_QualityControl_staff_sex"),
     *     @OA\Property(property="sex_text", ref="#/components/schemas/Schema_QualityControl_staff_sex_text"),
     *     @OA\Property(property="tel", ref="#/components/schemas/Schema_QualityControl_staff_tel"),
     *     @OA\Property(property="mobile", ref="#/components/schemas/Schema_QualityControl_staff_mobile"),
     *     @OA\Property(property="qq_number", ref="#/components/schemas/Schema_QualityControl_staff_qq_number"),
     *     @OA\Property(property="wechat", ref="#/components/schemas/Schema_QualityControl_staff_wechat"),
     *     @OA\Property(property="email", ref="#/components/schemas/Schema_QualityControl_staff_email"),
     *     @OA\Property(property="remarks", ref="#/components/schemas/Schema_QualityControl_staff_remarks"),
     *     @OA\Property(property="firstlogintime", ref="#/components/schemas/Schema_QualityControl_staff_firstlogintime"),
     *     @OA\Property(property="lastlogintime", ref="#/components/schemas/Schema_QualityControl_staff_lastlogintime"),
     *     @OA\Property(property="create_class_num", ref="#/components/schemas/Schema_QualityControl_staff_create_class_num"),
     *     @OA\Property(property="class_num", ref="#/components/schemas/Schema_QualityControl_staff_class_num"),
     *     @OA\Property(property="work_num", ref="#/components/schemas/Schema_QualityControl_staff_work_num"),
     *     @OA\Property(property="operate_staff_id", ref="#/components/schemas/common_Schema_operate_staff_id"),
     *     @OA\Property(property="created_at", ref="#/components/schemas/common_Schema_created_at"),
     *     @OA\Property(property="updated_at", ref="#/components/schemas/common_Schema_updated_at"),
     *     @OA\Property(property="modifyTime", ref="#/components/schemas/common_Schema_modifyTime"),
     *     @OA\Property(property="redisKey", ref="#/components/schemas/common_Schema_redisKey"),
     * )
     */

    //##################请求主体对象#######################################################
    /**
     * 单条记录请求体
     *
     * @OA\RequestBody(
     *     request="RequestBody_QualityControl_info_staff",
     *     description="单个增加",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/Schema_QualityControl_staff_obj"),
     *     @OA\MediaType(
     *         mediaType="application/xml",
     *         @OA\Schema(
     *              @OA\Property(property="info", ref="#/components/schemas/Schema_QualityControl_staff_obj"),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *          ),
     *     )
     * )
     *
     */

    /**
     * 多条记录请求体
     *
     * @OA\RequestBody(
     *     request="RequestBody_QualityControl_multi_staff",
     *     description="批量增加",
     *     required=true,
     *     @OA\JsonContent(
     *          type="object",
     *          @OA\Property(
     *              property="data_list",
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Schema_QualityControl_staff_obj"),
     *          ),
     *     ),
     *     @OA\MediaType(
     *         mediaType="application/xml",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="data_list",
     *                  @OA\Property(property="info", ref="#/components/schemas/Schema_QualityControl_staff_obj"),
     *              ),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *          ),
     *     ),
     * )
     *
     *
     */

    //##################响应参数#######################################################

    //响应对象
    // response=200,
    /**
     *
     * {
     *      "apistatus": "0:失败；1：成功",
     *      "result": {
     *              "has_page": "true:有下一页数据；false:没有下一页数据",
     *          "data_list": [
     *              {
     *                  "id": "1",
     *                  "version_num": "1",
     *                  "history_id": "1",
     *                  "version_num_history": "1",
     *                  "brand_name": "川渝人家",
     *                  ......
     *                  "addr": "城关镇北大街明珠馨苑",
     *                  "operate_staff_id": "1",
     *                  "operate_staff_id_history": "1",
     *                  "created_at": "2019-12-04 12:31:30",
     *                  "updated_at": "2019-12-04 12:31:30"
     *              }
     *          ],
     *          "total": "1008",
     *          "page": "1",
     *          "pagesize": "15",
     *          "totalPage": "68",
     *          "pageInfo": "<li><a href='javascript:;' id='totalpage' totalpage='68' >总数:1008个 / 68页<> 跳转 </button></span>"
     *      },
     *      "errorMsg": "有错时的错误信息"
     * }
     *
     *
     * 列表页
     *     @OA\Response(
     *         response="Response_QualityControl_list_staff",
     *         description="操作成功返回",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="has_page", ref="#/components/schemas/common_Schema_has_page"),
     *                  @OA\Property(
     *                      property="data_list",
     *                      type="array",
     *                      description="当前页列表数据",
     *                      @OA\Items(ref="#/components/schemas/Schema_QualityControl_staff_obj"),
     *                  ),
     *                  @OA\Property(property="total", ref="#/components/schemas/common_Schema_total"),
     *                  @OA\Property(property="page", ref="#/components/schemas/common_Schema_page"),
     *                  @OA\Property(property="pagesize", ref="#/components/schemas/common_Schema_pagesize"),
     *                  @OA\Property(property="totalPage", ref="#/components/schemas/common_Schema_totalPage"),
     *                  @OA\Property(property="pageInfo", ref="#/components/schemas/common_Schema_pageInfo"),
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *        ),
     *        @OA\XmlContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="has_page", ref="#/components/schemas/common_Schema_has_page"),
     *                  @OA\Property(
     *                      property="data_list",
     *                      type="object",
     *                      description="当前页列表数据",
     *                      @OA\Property(
     *                          property="info",
     *                          type="array",
     *                          @OA\Items(ref="#/components/schemas/Schema_QualityControl_staff_obj"),
     *                      ),
     *                  ),
     *                  @OA\Property(property="total", ref="#/components/schemas/common_Schema_total"),
     *                  @OA\Property(property="page", ref="#/components/schemas/common_Schema_page"),
     *                  @OA\Property(property="pagesize", ref="#/components/schemas/common_Schema_pagesize"),
     *                  @OA\Property(property="totalPage", ref="#/components/schemas/common_Schema_totalPage"),
     *                  @OA\Property(property="pageInfo", ref="#/components/schemas/common_Schema_pageInfo"),
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *         ),
     *     )
     */

    /**
     * 详情页
     * {
     *  "apistatus": "0:失败；1：成功",
     *   "result": {
     *      "info": {
     *          "id": "1",
     *          "version_num": "1",
     *          "history_id": "1",
     *           ......
     *          "addr": "城关镇北大街明珠馨苑",
     *          "operate_staff_id": "1",
     *          "operate_staff_id_history": "1",
     *          "created_at": "2019-12-04 12:31:30",
     *          "updated_at": "2019-12-04 12:31:30"
     *      }
     *   },
     *  "errorMsg": "有错时的错误信息"
     * }
     *     @OA\Response(
     *         response="Response_QualityControl_info_staff",
     *         description="操作成功返回",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="info",ref="#/components/schemas/Schema_QualityControl_staff_obj"),
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *        ),
     *        @OA\XmlContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  @OA\Property(property="info",ref="#/components/schemas/Schema_QualityControl_staff_obj",),
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *         ),
     *     )

     *
     * 登录返回
     * {
     *  "apistatus": "0:失败；1：成功",
     *   "result": {
     *          "id": "1",
     *          "version_num": "1",
     *          "history_id": "1",
     *           ......
     *          "addr": "城关镇北大街明珠馨苑",
     *          "operate_staff_id": "1",
     *          "operate_staff_id_history": "1",
     *          "created_at": "2019-12-04 12:31:30",
     *          "updated_at": "2019-12-04 12:31:30"
     *   },
     *  "errorMsg": "有错时的错误信息"
     * }
     *     @OA\Response(
     *         response="Response_QualityControl_info_staff_login",
     *         description="操作成功返回",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  ref="#/components/schemas/Schema_QualityControl_staff_obj_login",
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *        ),
     *        @OA\XmlContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(
     *                  property="result",
     *                  type="object",
     *                  ref="#/components/schemas/Schema_QualityControl_staff_obj_login",
     *               ),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *         ),
     *     )
     */



    /**
     * 详情页--result对象
     * {
     *  "apistatus": "0:失败；1：成功",
     *   "result": {
     *          "id": "1",
     *          "version_num": "1",
     *          "history_id": "1",
     *           ......
     *          "addr": "城关镇北大街明珠馨苑",
     *          "operate_staff_id": "1",
     *          "operate_staff_id_history": "1",
     *          "created_at": "2019-12-04 12:31:30",
     *          "updated_at": "2019-12-04 12:31:30"
     *   },
     *  "errorMsg": "有错时的错误信息"
     * }
     *     @OA\Response(
     *         response="Response_QualityControl_result_staff",
     *         description="操作成功返回",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(property="result",ref="#/components/schemas/Schema_QualityControl_staff_obj"),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *        ),
     *        @OA\XmlContent(
     *              type="object",
     *              @OA\Property(property="apistatus", ref="#/components/schemas/common_Schema_apistatus"),
     *              @OA\Property(property="result",ref="#/components/schemas/Schema_QualityControl_staff_obj"),
     *              @OA\Property(property="errorMsg", ref="#/components/schemas/common_Schema_errorMsg"),
     *              @OA\Xml(
     *                  name="root",
     *                  wrapped=true
     *              ),
     *         ),
     *     )
     */

}
