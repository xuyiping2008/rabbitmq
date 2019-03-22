<?php
/*
 * topic 模式
 * create by superrd
 */

$exchangeName = 'extopic';
$routeKey1 = "black.critical.high";
$routeKey2 = "red.critical.high";
$routeKey3 = "white.critical.high";

$message1 = 'black-critical-high!';
$message2 = 'red-critical-high!';
$message3 = 'white-critical-high!';

//$connection = new AMQPConnection(array('host' => '10.99.121.137', 'port' => '5672', 'vhost' => '/', 'login' => 'superrd', 'password' => 'superrd'));
$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
$connection->connect() or die("Cannot connect to the broker!\n");
try {
    $channel = new AMQPChannel($connection);
    $exchange = new AMQPExchange($channel);
    $exchange->setName($exchangeName);
    $exchange->setType(AMQP_EX_TYPE_TOPIC);
    $exchange->setFlags(AMQP_DURABLE);
    $exchange->declareExchange();

    $exchange->publish($message1,$routeKey1);
    var_dump("[x] Sent ".$message1);
    $exchange->publish($message2,$routeKey2);
    var_dump("[x] Sent ".$message2);
    $exchange->publish($message3,$routeKey3);
    var_dump("[x] Sent ".$message3);

} catch (AMQPConnectionException $e) {
    var_dump($e);
    exit();
}
$connection->disconnect();