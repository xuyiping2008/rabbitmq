<?php

/*
 * 发布-订阅
 * create by superrd
 */
$queueName = 'superrd';
$exchangeName = 'superrd';
$routeKey = 'superrd';
$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
$connection->connect() or die("Cannot connect to the broker!\n");

$channel = new AMQPChannel($connection);
$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->setFlags(AMQP_DURABLE);
$exchange->declareExchange();

$queue = new AMQPQueue($channel);
$queue->setName($queueName);
$queue->setFlags(AMQP_DURABLE);
$queue->declareQueue();

$queue->bind($exchangeName, $routeKey);

//阻塞模式接收消息

echo "Message:\n";
while(True){
    $queue->consume('processMessage');
//自动ACK应答
    //$queue->consume('processMessage', AMQP_AUTOACK);
}

$conn->disconnect();

/*
* 消费回调函数
* 处理消息
*/
function processMessage($envelope, $q) {
    $msg = $envelope->getBody();
    echo $msg."\n"; //处理消息
    $q->ack($envelope->getDeliveryTag()); //手动发送ACK应答
}