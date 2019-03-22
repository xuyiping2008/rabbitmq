<?php
/*
 * 发布-订阅
 * create by superrd
 */

$queueName = 'superrd';
$exchangeName = 'superrd';
$routeKey = 'superrd';
$message = 'Hello World!';
$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
$connection->connect() or die("Cannot connect to the broker!\n");
try {
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

    $exchange->publish($message,$routeKey);

    var_dump("[x] Sent 'Hello World!'");

} catch (AMQPConnectionException $e) {
    var_dump($e);
    exit();
}
 $connection->disconnect();