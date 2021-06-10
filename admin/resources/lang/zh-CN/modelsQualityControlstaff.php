<?php
// 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择*** ;字段下还可以 加 enum 用来替换 message中的{enum}
return  [
    'table_name' => '帐号',// 表/栏目名称
    'fields' => [
        'id' => [
            "field_name" => '{id}',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'admin_type' => [
            "field_name"=>'用户类型',
            "message" => '{fieldName}{validValueIs}（1平台2企业4个人8专家16第三方服务商）'
        ],
        'admin_username' => [
            "field_name"=>'用户名',
            "message" => '{fieldName}{valueLenIs}0~ 30{numsCharacters}'
        ],
        'admin_password' => [
            "field_name"=>'密码',
            "message" => '{fieldName}{valueLenIs}0~ 100{numsCharacters}'
        ],
        'issuper' => [
            "field_name"=>'是否超级帐户',
            "message" => '{fieldName}{validValueIs}（2否1是）'
        ],
        'open_status' => [
            "field_name"=>'审核状态',
            "message" => '{fieldName}{validValueIs}（1待审核2审核通过4审核不通过）'
        ],
        'open_fail_reason' => [
            "field_name"=>'审核失败原因',
            "message" => '{fieldName}{valueLenIs}0~ 100{numsCharacters}'
        ],
        'account_status' => [
            "field_name" => '{status}',// '状态',
            "message" => '{fieldName}{validValueIs}（1正常 2冻结）'
        ],
        'frozen_fail_reason' => [
            "field_name"=>'冻结原因',
            "message" => '{fieldName}{valueLenIs}0~ 100{numsCharacters}'
        ],
        'real_name' => [
            "field_name" => '{real_name}',// '真实姓名',
            "message" => '{fieldName}{valueLenIs}0~ 100{numsCharacters}'
        ],
        'sex' => [
            "field_name" => '{sex}',// '性别',
            "message" => '{fieldName}{validValueIs}（0未知1男2女）'
        ],
        'tel' => [
            "field_name" => '{tel}',
            "message" => '{fieldName}{valueLenIs}0~ 25{numsCharacters}'
        ],
        'mobile' => [
            "field_name" => '{mobile}',
            "message" => '{fieldName}{valueLenIs}0~ 30{numsCharacters}'
        ],
        'qq_number' => [
            "field_name"=>'QQ',
            "message" => '{fieldName}{valueLenIs}0~ 30{numsCharacters}'
        ],
        'wechat' => [
            "field_name"=>'微信',
            "message" => '{fieldName}{valueLenIs}0~ 30{numsCharacters}'
        ],
        'email' => [
            "field_name"=>'邮箱',
            "message" => '{fieldName}{valueLenIs}0~ 30{numsCharacters}'
        ],
        'remarks' => [
            "field_name"=>'备注',
            "message" => '{fieldName}{valueLenIs}0~ 200{numsCharacters}'
        ],
        'firstlogintime' => [
            "field_name"=>'初次登录',
            "message"=>'{fieldName}{valMustDateTime}!'
        ],
        'lastlogintime' => [
            "field_name"=>'上次登录',
            "message"=>'{fieldName}{valMustDateTime}!'
        ],
        'create_class_num' => [
            "field_name"=>'创建班级数量',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'class_num' => [
            "field_name"=>'所属班级数量',
            "message" => '{fieldName}{valMustInt}!'
        ],
        'work_num' => [
            "field_name"=>'作品数量',
            "message" => '{fieldName}{valMustInt}!'
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
        "real_name_is_must" => '真实姓名不能为空！',
        "mobile_is_must" => '手机不能为空！',
        "admin_username_is_must" => '用户名不能为空！',

    ],
    'tishi' => [// 其它显示

    ]
];
