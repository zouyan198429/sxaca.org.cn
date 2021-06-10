<?php
return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关--注：修改配置，一定要重新队列服务
        'gateways' => [
            // 'yunpian',//云片
            // 'aliyun',// 阿里云短信
             'qcloud', //腾讯云
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => '/tmp/easy-sms.log',
        ],
        'yunpian' => [// 短信内容使用 content
            'api_key' => env('YUNPIAN_API_KEY', ''),// '云片短信平台账户api_key',
        ],
        'aliyun' => [// 短信内容使用 template + data
            'access_key_id' => env('ALIYUN_SMS_ACCESS_KEY_ID', ''),
            'access_key_secret' => env('ALIYUN_SMS_ACCESS_KEY_SECRET', ''),
            'sign_name' => env('ALIYUN_SMS_SIGN_NAME', ''),//  签名名称
            'regionId' => env('ALIYUN_SMS_REGION_ID', 'cn-hangzhou'),// 地域和可用区 https://help.aliyun.com/document_detail/40654.html?spm=a2c6h.13066369.0.0.54a120f89HVXHt
            // 尊敬的用户，您的验证码${code}，请在3分钟内使用，工作人员不会索取，请勿泄漏。
            // 参数必须是 [a-zA-Z0-9]
            'verification_code_params' => [// 验证码相关参数
                'SignName' => env('ALIYUN_SMS_VERIFICATION_SIGN_NAME', ''),// 值为空或没有此下标，会自动使用上层的sign_name值。 短信签名名称。请在控制台签名管理页面签名名称一列查看。
                'TemplateCode' => env('ALIYUN_SMS_VERIFICATION_TEMPLATE_CODE', ''),// 短信模板ID。请在控制台模板管理页面模板CODE一列查看。

            ],
            'template_params' => [// 短信模板替换参数
                'verification_code_params' => ['code'],// 验证码模板 参数必须是 [a-zA-Z0-9]
            ]
        ],
        'qcloud'   => [// 短信内容使用 content。
            'sdk_app_id' => env('QCLOUD_SMS_SDK_APP_ID', ''), // SDK APP ID '腾讯云短信平台sdk_app_id'
            'app_key'    => env('QCLOUD_SMS_APP_KEY', ''), // APP KEY '腾讯云短信平台app_key'
            'secret_id' => env('QCLOUD_SMS_SECRET_ID', ''), // 通过接口访问时的 SecretId 密钥
            'secret_key' => env('QCLOUD_SMS_SECRET_KEY', ''), // 通过接口访问时的 SecretKey 密钥
            'sign_name'  => env('QCLOUD_SMS_SIGN_NAME', ''),// '可以不填写', // 对应的是短信签名中的内容（非id） '腾讯云短信平太签名'  (此处可设置为空，默认签名)
            /***
             *
             *
             *  # 请选择大区 https://console.cloud.tencent.com/api/explorer?Product=sms&Version=2019-07-11&Action=SendSms&SignVersion=
             *  # ap-beijing 华北地区(北京)
             *  # ap-chengdu 西南地区(成都)
             *  # ap-chongqing 西南地区(重庆)
             *  # ap-guangzhou 华南地区(广州)
             *  # ap-guangzhou-open 华南地区(广州Open)
             *  # ap-hongkong 港澳台地区(中国香港)
             *  # ap-seoul 亚太地区(首尔)
             *  # ap-shanghai 华东地区(上海)
             *  #
             *  # ap-singapore 东南亚地区(新加坡)
             *  # eu-frankfurt 欧洲地区(法兰克福)
             *  # na-siliconvalley 美国西部(硅谷)
             *  # na-toronto 北美地区(多伦多)
             *  # ap-mumbai 亚太地区(孟买)
             *  # na-ashburn 美国东部(弗吉尼亚)
             *  # ap-bangkok 亚太地区(曼谷)
             *  # eu-moscow 欧洲地区(莫斯科)
             *  # ap-tokyo 亚太地区(东京)
             *  # 金融区
             *  # ap-shanghai-fsi 华东地区(上海金融)
             *  # ap-shenzhen-fsi 华南地区(深圳金融)
             *
             */
            'regionId' => env('QCLOUD_SMS_REGION_ID', 'ap-beijing'),// 地域和可用区
            // ID 468796  --- 作废，因为第一个参数不能传中文。所以不用了
            // 尊敬的用户：您的{1}验证码{2}，请在{3}分钟内使用，工作人员不会索取，请勿泄漏。
            // 1: operateType 操作类型 如：注册--用不了  ； 2： code 如：验证码 2456  ； 3 ：有效时间(单位分钟) validMinute 如 3

            // ID 470052
            // 内容 尊敬的用户：您的{1}验证码{2}，请在{3}分钟内使用，工作人员不会索取，请勿泄漏。
            // 1： code 如：验证码 2456  ； 2 ：有效时间(单位分钟) validMinute 如 3
            'verification_code_params' => [// 验证码相关参数
                'SignName' => env('QCLOUD_SMS_VERIFICATION_SIGN_NAME', ''),// 值为空或没有此下标，会自动使用上层的sign_name值。 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请
                'TemplateCode' => env('QCLOUD_SMS_VERIFICATION_TEMPLATE_CODE', ''),// 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
            ],
            'template_params' => [// 短信模板替换参数
                'verification_code_params' => ['code', 'validMinute'],// 'operateType', 验证码模板--注意验证码模板变量参数只能是<=6的数字，不能是中文及字母。
            ]
        ],
    ],
];
