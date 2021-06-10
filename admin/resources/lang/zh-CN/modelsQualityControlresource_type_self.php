<?php
// 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择*** ;字段下还可以 加 enum 用来替换 message中的{enum}
return  [
    'table_name' => '资源分类自定义',// 表/栏目名称
    'fields' => [
        'id' => [
            "field_name" => '{id}',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'version_num' => [
            "field_name" => '{version_num}',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'version_history_id' => [
            "field_name" => '{version_history_id}',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'version_num_history' => [
            "field_name"=>'{version_num_history}',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'ower_type' => [
            "field_name"=>'拥有者类型',
            "message" => '{fieldName}{validValueIs}（1平台2城市分站4城市代理8商家16店铺32快跑人员64用户）'
        ],
        'ower_id' => [
            "field_name" => '{ower}',// '拥有者',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'type_name' => [
            "field_name"=>'类型名称',
            "message" => '{fieldName}{valueLenIs}0~ 50{numsCharacters}'
        ],
        'sort_num' => [
            "field_name" => '{sort_num}',
            "message" => '{fieldName}{valMustInt}!'
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