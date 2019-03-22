<?php
/*
 * 发布-订阅-P
 * create by superrd
 */

$queueName = 'superrd';
$exchangeName = 'superrd';
$routeKey = 'superrd';
$message = 'task--';
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

    for($i=0 ; $i<100;$i++){
        $exchange->publish($message.$i,$routeKey);
        var_dump("Ack Sent $message $i");
    }

} catch (AMQPConnectionException $e) {
    var_dump($e);
    exit();
}
$connection->disconnect();

