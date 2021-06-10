<?php
// 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择*** ;字段下还可以 加 enum 用来替换 message中的{enum}
return  [
    'table_name' => '验证码',// 表/栏目名称
    'fields' => [
        'id' => [
            "field_name" => '{id}',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'staff_id' => [
            "field_name"=>'所属老师',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'sms_code' => [
            "field_name"=>'短信验证码',
            "message" => '{fieldName}{valueLenIs}0~ 50{numsCharacters}'
        ],
        'sms_type' => [
            "field_name"=>'类型',
            "message" => '{fieldName}{validValueIs}（1登录/注册）'
        ],
        'sms_status' => [
            "field_name"=>'{status}',
            "message" => '{fieldName}{validValueIs}（1待发送2已发送4已使用8发送失败）'
        ],
        'count_date' => [
            "field_name"=>'日期',
            "message"=>'{fieldName}{valMustDate}!'
        ],
        'count_year' => [
            "field_name"=>'年',
            "message" => '{fieldName}{valueLenIs}0~ 100{numsCharacters}'
        ],
        'count_month' => [
            "field_name"=>'月',
            "message" => '{fieldName}{validValueIs}（ 0~12）'
        ],
        'count_day' => [
            "field_name"=>'日',
            "message" => '{fieldName}{validValueIs}（ 0~31）'
        ],
        'operate_staff_id' => [
            "field_name" => '{operate_staff}',
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
