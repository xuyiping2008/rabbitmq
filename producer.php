<?php

$conn_args = array(
    'host' => '127.0.0.1',
    'port' => '5672',
    'login' => 'guest',
    'password' => 'guest',
    'vhost'=>'/'
);

//创建连接
$conn = new AMQPConnection($conn_args);
if(!$conn->connect()){
    die('Cannot connect to the broke!');
}

$channel = new AMQPChannel($conn);

$e_name = 'e_linvo'; //交换机名
$k_route1 = 'user.save'; //路由key
$k_route2 = 'user.update'; //路由key
$k_route3 = 'user.delete.abc'; //路由key
//创建交换机
$ex = new AMQPExchange($channel);
$ex->setName($e_name);
//$ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
$ex->setType(AMQP_EX_TYPE_TOPIC); //direct类型
//$ex->setFlags(AMQP_DURABLE); //持久化
date_default_timezone_set("Asia/Shanghai");

$message = "Hello world!";
echo "Send Message:".$ex->publish($message , $k_route1)."\n";
echo "Send Message:".$ex->publish($message , $k_route2)."\n";
echo "Send Message:".$ex->publish($message , $k_route3)."\n";

/*for($i=0; $i<5; ++$i){
    $message = "Hello world!".$i;
    echo "Send Message:".$ex->publish($message , $k_route)."\n";
}*/
$conn->disconnect();