<?php
/**
 * Created by PhpStorm.
 * User: xuyiping
 * Date: 2018/12/26
 * Time: 14:20
 */

error_reporting(0);
include_once('RabbitMqCommand.php');


$configs = array('host'=>'127.0.0.1','port'=>5672,'username'=>'guest','password'=>'guest','vhost'=>'/');
$exchange_name = 'class-e-1';
$queue_name = 'class-q-1';
$route_key = 'class-r-1';
$ra = new RabbitMQCommand($configs,$exchange_name,$queue_name,$route_key);


class A{
    function processMessage($envelope, $queue) {
        $msg = $envelope->getBody();
        //自增id
        $envelopeID = $envelope->getDeliveryTag();
        $pid = posix_getpid();
        //将取出来的内容放到文件里面
        file_put_contents("log{$pid}.log", $msg.'|'.$envelopeID.''."\r\n",FILE_APPEND);
        $queue->ack($envelopeID);
    }
}
$a = new A();


$s = $ra->run(array($a,'processMessage'),false);