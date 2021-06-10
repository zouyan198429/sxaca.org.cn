

<?php
// 不用看这里，只是收藏的代码，参考用
//Php文档
//PHP
//为方便PHP开发者调试和接入SendCloudAPI,我们提供了基于PHP的SDK
//
//点击下载：邮件_短信SDK
//
//邮件短信SDK是为了让PHP开发者能够在自己的代码里更快捷的使用SendCloud的API发送短信和邮件而开发的SDK工具包
//
//资源
//见不同模块API的参数说明、返回码. 如邮件API普通发送, 邮件API模板发送, 短信API send, 短信API返回码
//
//使用指引
//1.将下载好的SDK放入到您的程序目录。详细使用方法请参考demo目录下的代码示例。
//
//代码示例目录
//代码示例路径：../sendcloud-php-sdk/examples
//
//文件名	说明
//SendMail.php	邮件发送示例
//SendSms.php	短信发送示例
//SendVoice.php	语言发送示例
    function send_mail() {
      $url = 'http://api.sendcloud.net/apiv2/mail/send';
      $API_USER = '您账户中的API_USER';
      $API_KEY = 'API_KEY已发送到您的注册邮箱';

      //您需要登录SendCloud创建API_USER，使用API_USER和API_KEY才可以进行邮件的发送。
      $param = array(
          'apiUser' => $API_USER,
          'apiKey' => '您自己设置的API_KEY',
          'from' => 'service@sendcloud.im',
          'fromName' => 'SendCloud测试邮件',
          'to' => '收件人地址',
          'subject' => '来自SendCloud的第一封邮件！',
          'html' => '你太棒了！你已成功的从SendCloud发送了一封测试邮件，接下来快登录前台去完善账户信息吧！',
          'respEmailId' => 'true');

    $data = http_build_query($param);

    $options = array(
          'http' => array(
          'method'  => 'POST',
          'header'  => 'Content-Type: application/x-www-form-urlencoded',
          'content' => $data
    ));

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
  }
echo send_mail();


//
//  <html>
//        <head>
//            <title>send mail by post</title>
//            <meta http-equiv="Content-Type"content="text/html;charset=UTF-8">
//        </head>
//        <body>
//            <form action="http://api.sendcloud.net/apiv2/mail/send" method="post" enctype="multipart/form-data">
//                <p>apiUser: <input type="text" name="apiUser"/></p>
//                <p>apiKey:  <input type="text" name="apiKey"/></p>
//                <p>to: <input type="text" name="to"/></p>
//                <p>from: <input type="text" name="from"/></p>
//                <p>fromName: <input type="text" name="fromName"/></p>
//                <p>replyTo: <input type="text" name="replyTo"/></p>
//                <p>cc: <input type="text" name="cc"/></p>
//                <p>bcc: <input type="text" name="bcc"/></p>
//                <p>subject: <input type="text" name="subject"/></p>
//                <p>html: <textarea rows="30" cols="50" name="html"></textarea></p>
//                <p>file1: <input type="file" name="files"/></p>
//                <p>file2: <input type="file" name="files"/></p>
//                <input type="submit" value="Submit"/>
//            </form>
//        </body>
//    </html>

