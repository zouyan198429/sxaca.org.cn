<?php
// 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择*** ;字段下还可以 加 enum 用来替换 message中的{enum}
return  [
    'table_name' => '付款/收款项目字段历史',// 表/栏目名称
    'fields' => [
        'payment_project_fields_id' => [// 主表字段
            "field_name"=>'所属付款/收款项目字段',// 主表字段名称
            "message" => '{fieldName}{valMustInt}!'
        ],
    ],
    'judge_err' => [// 数据验证相关的错误
    ],
    'tishi' => [// 其它显示

    ]
];
