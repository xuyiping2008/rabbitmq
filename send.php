<?php
/**
 * Created by PhpStorm.
 * User: xuyiping
 * Date: 2018/12/26
 * Time: 14:19
 */

set_time_limit(0);
include_once('RabbitMqCommand.php');

$configs = array('host'=>'127.0.0.1','port'=>5672,'username'=>'guest','password'=>'guest','vhost'=>'/');
$exchange_name = 'class-e-1';
$queue_name = 'class-q-1';
$route_key = 'class-r-1';
$ra = new RabbitMQCommand($configs,$exchange_name,$queue_name,$route_key);
for($i=0;$i<=100;$i++) {
    $ra->send("hello world ".$i);
    echo "hello world ".$i.'\n';
}