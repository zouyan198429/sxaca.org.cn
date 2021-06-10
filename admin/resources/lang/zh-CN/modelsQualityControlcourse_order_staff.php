<?php
// 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择*** ;字段下还可以 加 enum 用来替换 message中的{enum}
return  [
    'table_name' => '报名学员',// 表/栏目名称
    'fields' => [
        'id' => [
            "field_name" => '{id}',
            "message" => '{fieldName}{valMustInt}!'
        ],
//        'aaaaa' => [
//            "field_name"=>'bbb',
//            "message" => '{fieldName}{valueLenIs}0~ 100{numsCharacters}'
//        ],
//        'aaaaa' => [
//            "field_name"=>'bbb',
//            "message" => '{fieldName}{valueLenIs}0~ 100{numsCharacters}'
//        ],
//        'aaaaa' => [
//            "field_name"=>'bbb',
//            "message" => '{fieldName}{valueLenIs}0~ 100{numsCharacters}'
//        ],
//        'aaaaa' => [
//            "field_name"=>'bbb',
//            "message" => '{fieldName}{valueLenIs}0~ 100{numsCharacters}'
//        ],
//        'aaaaa' => [
//            "field_name"=>'bbb',
//            "message" => '{fieldName}{valueLenIs}0~ 100{numsCharacters}'
//        ],
//        'aaaaa' => [
//            "field_name"=>'bbb',
//            "message" => '{fieldName}{valueLenIs}0~ 100{numsCharacters}'
//        ],
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
