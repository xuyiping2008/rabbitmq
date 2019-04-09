<?php
/*
 * 发布-订阅
 * create by superrd
 */

$queueName = 'superrd';
$exchangeName = 'superrd';
$routeKey = 'superrd';


//创建一个connection,并进行配置
$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
$connection->connect() or die("Cannot connect to the broker!\n");
try {
    //通过连接工厂创建连接
    $channel = new AMQPChannel($connection);

    //创建channel
    $exchange = new AMQPExchange($channel);
    $exchange->setName($exchangeName);
    $exchange->setType(AMQP_EX_TYPE_DIRECT);
    $exchange->setFlags(AMQP_DURABLE);
    $exchange->declareExchange();


    //绑定队列
    $queue = new AMQPQueue($channel);
    $queue->setName($queueName);
    $queue->setFlags(AMQP_DURABLE);
    $queue->declareQueue();

    //绑定队列名和路由key
    $queue->bind($exchangeName, $routeKey);

    //生产消息
    for ($i = 0; $i < 3; $i++){
        $message = 'Hello World!--'.$i;
        $exchange->publish($message,$routeKey);
    }
    var_dump("[x] Sent 'Hello World!'");

} catch (AMQPConnectionException $e) {
    var_dump($e);
    exit();
}
//关闭
 $connection->disconnect();