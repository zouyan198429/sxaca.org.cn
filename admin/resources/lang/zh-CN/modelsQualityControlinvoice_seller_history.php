<?php
// 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择*** ;字段下还可以 加 enum 用来替换 message中的{enum}
return  [
    'table_name' => '发票配置销售方历史',// 表/栏目名称
    'fields' => [
        'invoice_seller_id' => [// 主表字段
            "field_name"=>'发票配置销售方',// 主表字段名称
            "message" => '{fieldName}{valMustInt}!'
        ],
    ],
    'judge_err' => [// 数据验证相关的错误
    ],
    'tishi' => [// 其它显示

    ]
];
