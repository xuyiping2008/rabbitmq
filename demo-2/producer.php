<?php
/**
 * Created by PhpStorm.
 * User: xuyiping
 * Date: 2019/3/22
 * Time: 14:46
 */

$conn_args = array(
    'host' => '127.0.0.1',
    'port' => '5672',
    'login' => 'guest',
    'password' => 'guest',
    'vhost'=>'/'
);

//创建连接和channel
$conn = new AMQPConnection($conn_args);
if (!$conn->connect()) {
    die("Cannot connect to the broker!\n");
}
$channel = new AMQPChannel($conn);
##3，exchange 与  routingkey ： 交换机 与 路由键##
//为了将不同类型的消息进行区分，设置了交换机与路由两个概念。比如，将A类型的消息发送到名为‘C1’的交换机，将类型为B的发送到'C2'的交换机。当客户端连接C1处理队列消息时，取到的就只是A类型消息。进一步的，如果A类型消息也非常多，需要进一步细化区分，比如某个客户端只处理A类型消息中针对K用户的消息，routingkey就是来做这个用途的。

$e_name = 'e_linvo'; //交换机名
$k_route = array(0=> 'key_1', 1=> 'key_2'); //路由key
//创建交换机
$ex = new AMQPExchange($channel);
$ex->setName($e_name);
$ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
$ex->setFlags(AMQP_DURABLE); //持久化
echo "Exchange Status:".$ex->declare()."\n";
for($i=0; $i<5; ++$i){
    echo "Send Message:".$ex->publish($message . date('H:i:s'), $k_route[$i%2])."\n";
}