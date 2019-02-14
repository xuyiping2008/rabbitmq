<?php
/**
 * Created by PhpStorm.
 * User: xuyiping
 * Date: 2018/12/25
 * Time: 15:18
 */

$conn_args = array(
    'host' => '127.0.0.1',
    'port' => '5672',
    'login' => 'guest',
    'password' => 'guest',
    'vhost'=>'/test'
);

//创建连接
$conn = new AMQPConnection($conn_args);

if(!$conn->connect()){
    die('Cannot connect to the broke!');
}

$channel = new AMQPChannel($conn);

$e_name = 'e_xuyiping'; //交换机名
$k_route = 'key_1'; //路由key
//创建交换机
$ex = new AMQPExchange($channel);
$ex->setName($e_name);
$ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
$ex->setFlags(AMQP_DURABLE); //持久化
date_default_timezone_set("Asia/Shanghai");

for($i=0; $i<5; ++$i){
    $message = "Hello world!".$i;
    echo "Send Message:".$ex->publish($message , $k_route)."\n";
}
$conn->disconnect();