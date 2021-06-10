<?php
// 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择*** ;字段下还可以 加 enum 用来替换 message中的{enum}
return  [
    'table_name' => '资源模块使用',// 表/栏目名称
    'fields' => [
        'id' => [
            "field_name" => '{id}',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'resource_id' => [
            "field_name" => '{resource}',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'resource_id_history' => [
            "field_name" => '{resource_history}',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'module_id' => [
            "field_name"=>'模块',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'module_type' => [
            "field_name"=>'模块标识',
            "message" => '{fieldName}{valueLenIs}0~ 80{numsCharacters}'
        ],
        'operate_staff_id' => [
            "field_name" => '{operate_staff}',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'operate_staff_id_history' => [
            "field_name" => '{operate_staff_history}',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'created_at' => [
            "field_name" => '{created_at}',
            "message"=>'{fieldName}{valMustDateTime}!'
        ],
        'updated_at' => [
            "field_name" => '{updated_at}',
            "message"=>'{fieldName}{valMustDateTime}!'
        ],
    ],
    'judge_err' => [// 数据验证相关的错误

    ],
    'tishi' => [// 其它显示

    ]
];