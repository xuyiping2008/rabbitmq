<?php

/*
 * RabbitMQ fanout
 * create by superrd
 */

$exchangeName = 'log';
$message = 'log--';
//$connection = new AMQPConnection(array('host' => '10.99.121.137', 'port' => '5672', 'vhost' => '/', 'login' => 'superrd', 'password' => 'superrd'));
$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
$connection->connect() or die("Cannot connect to the broker!\n");
try {
    $channel = new AMQPChannel($connection);
    $exchange = new AMQPExchange($channel);
    $exchange->setName($exchangeName);
    $exchange->setType(AMQP_EX_TYPE_FANOUT);
    $exchange->setFlags(AMQP_DURABLE);
    $exchange->declareExchange();

    for($i=0 ; $i<100;$i++){
        $exchange->publish($message.$i,"");

        var_dump("[x] Sent $message $i");
    }
} catch (AMQPConnectionException $e) {
    var_dump($e);
    exit();
}
$connection->disconnect();