<?php
// 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择*** ;字段下还可以 加 enum 用来替换 message中的{enum}
return  [
    'table_name' => '资源',// 表/栏目名称
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
        'type_self_id' => [
            "field_name"=>'类型自定义',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'type_self_id_history' => [
            "field_name"=>'类型自定义历史',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'resource_name' => [
            "field_name"=>'资源名称',
            "message" => '{fieldName}{valueLenIs}0~ 500{numsCharacters}'
        ],
        'resource_type' => [
            "field_name"=>'资源类型',
            "message" => '{fieldName}{validValueIs}（1图片2excel 4 PDF、word 8 PDF16 word）'
        ],
        'resource_note' => [
            "field_name"=>'资源说明',
            "message" => '{fieldName}{valueLenIs}0~ 2000{numsCharacters}'
        ],
        'resource_url' => [
            "field_name"=>'资源地址',
            "message" => '{fieldName}{valueLenIs}0~ 500{numsCharacters}'
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
