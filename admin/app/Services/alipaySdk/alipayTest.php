<?php


namespace App\Services\alipaySdk;

// require_once app_path('Library') . '/alipayWapPay/aop/AopClient.php';
require_once 'aop/AopCertClient.php';
require_once 'aop/AopCertification.php';
require_once 'aop/request/AlipayTradeQueryRequest.php';
require_once 'aop/request/AlipayTradeWapPayRequest.php';
require_once 'aop/request/AlipayTradeAppPayRequest.php';

/**
 * 证书类型AopCertClient功能方法使用测试，特别注意支付宝根证书预计2037年会过期，请在适当时间下载更新支付更证书
 * 1、execute 证书模式调用示例
 * 2、sdkExecute 证书模式调用示例
 * 3、pageExecute 证书模式调用示例
 */

class alipayTest extends BasicAlipay
{

    /**
     * --- 接口测试
     *
     * @param array $config  接口相关的配置信息
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
   public static function test($config = []){

        //1、execute 使用
       $aop = static::getAop($config);// new \AopCertClient ();

       $request = new \AlipayTradeQueryRequest();
       // 2、对于每一个接口对应的请求类，调用它的 setNeedEncrypt 方法，设置为 true 就可以了。
       if(static::isOpenEncrypt($config)){
            $request->setNeedEncrypt(true);
       }
       $request->setBizContent("{" .
           "\"out_trade_no\":\"20150320010101001\"," .
           "\"trade_no\":\"2014112611001004680 073956707\"," .
           "\"org_pid\":\"2088101117952222\"," .
           "      \"query_options\":[" .
           "        \"TRADE_SETTE_INFO\"" .
           "      ]" .
           "  }");
       $result = $aop->execute($request);
       pr($result);
      // echo $result;
       pr('111');
   }
}
