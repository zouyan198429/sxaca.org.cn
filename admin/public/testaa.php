<?php
// 设置显示错误气提示
ini_set('display_errors', true);
error_reporting(E_ALL);

// 配置session 存储于redis
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=15&auth=ABCabc123456!@!');

// 24 * 5个钟头
$limitSecond = 60 * 60 * 24 * 5;
ini_set('session.gc_maxlifetime', $limitSecond);

session_start();
// 这个就是redis中存储数据的key,
// redis用session_id作为key 并且是以string的形式存储
// php向redis写入数据时是已经序列化之后的数据
$redisKey = 'PHPREDIS_SESSION:' . session_id();

// SESSION 赋值测试
$_SESSION['message'] = "Hello, I'm in redis";
// $_SESSION['arr'] = [1, 2, 3, 4, 5, 6];

echo $_SESSION['message'] , '<br/>';
echo 'Redis key =    ' . $redisKey . '<br/>';

echo '以下是从Redis获取的数据', '<br/>';
// 取数据
$redis = new Redis();
$redis->connect('localhost', 6379);
$redis->auth('ABCabc123456!@!');

print_r(unpack("C*",$redis->get($redisKey)));
unset($_SESSION['message']);
echo $_SESSION['message'] ?? '无值：' , '<br/>';
