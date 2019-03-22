<?php

$exchangeName = 'log';
$queueName = 'queueb';
$routeKey = '';

$connection = new AMQPConnection(array('host' => '10.99.121.137','port' => '5672', 'vhost' => '/', 'login' => 'superrd', 'password' => 'superrd'));
$connection->connect() or die("Cannot connect to the broker!\n");

$channel = new AMQPChannel($connection);

$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$exchange->setType(AMQP_EX_TYPE_FANOUT);
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
    //$queue->consume('processMessage', AMQP_AUTOACK); //自动ACK应答
}

$conn->disconnect();
/**
 * 消费回调函数
 * 处理消息
 */
function processMessage($envelope, $q) {
    $msg = $envelope->getBody();
    sleep(1);
    echo $msg."\n"; //处理消息
    $q->ack($envelope->getDeliveryTag()); //手动发送ACK应答
}
