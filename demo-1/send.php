<?php
/**
 * Created by PhpStorm.
 * User: xuyiping
 * Date: 2019/3/22
 * Time: 14:04
 */

$exchangeName = 'demo';
$queueName = 'hello';
$routeKey = 'hello';
$message = 'Hello World!';
$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
$connection->connect() or die("Cannot connect to the broker!\n");
try {
    $channel = new AMQPChannel($connection);
    $exchange = new AMQPExchange($channel);
    $exchange->setName($exchangeName);
    $queue = new AMQPQueue($channel);
    $queue->setName($queueName);
    $exchange->publish($message, $routeKey);
    var_dump("[x] Sent 'Hello World!'");
} catch (AMQPConnectionException $e) {
    var_dump($e);
    exit();
}
$connection->disconnect();