<?php
/**
 * Created by PhpStorm.
 * User: xuyiping
 * Date: 2018/12/26
 * Time: 14:10
 */

class RabbitMqCommand {


    public $configs=array();
    //交换机名称
    public $exchange_name='';
    //队列名称
    public $queue_name='';
    //路由名称
    public $route_key='';
    //持久化
    public $durable=True;
    /*
    * 自动删除
    * exchange is deleted when all queues have finished using it
    * queue is deleted when last consumer unsubscribes
    *
    */
    public $autodelete = False;
    /*
    * 镜像
    * 镜像队列，打开后消息会在节点之间复制，有master和slave的概念
    */
    public $mirror = False;

    private $_conn = Null;
    private $_exchange = Null;
    private $_channel = Null;
    private $_queue = Null;

    public function __construct($configs=array(),$exchange_name='',$queue_name='',$route_key=''){
        $this->setConfig($configs);
        $this->exchange_name=$exchange_name;
        $this->queue_name=$queue_name;
        $this->route_key=$route_key;
    }
    private function setConfig($configs){
        if(!is_array($configs)){
            throw new Exception('config is not array');
        }
        if(!($configs['host'] && $configs['port'] && $configs['username'] && $configs['password'])){
            throw new Exception('configs is empty');
        }
        if(empty($configs['vhost'])){
            $configs['vhost']='/';
        }
        $configs['login']=$configs['username'];
        unset($configs['username']);
        $this->configs=$configs;
    }
    /*
     * 设置是否持久化，默认为True
     */
    public function setDurable($durable){
        $this->durable=$durable;
    }
    /*
     * 设置是否自动删除
     */
    public function setAutoDelete($autodelete){
        $this->autodelete=$autodelete;
    }
    /*
     * 设置是否镜像
     */
    public function setMirror($mirror){
        $this->mirror=$mirror;
    }
    /*
     * 打开amqp连接
     */
    private function open(){
        if(!$this->_conn){
            try {
                $this->_conn=new AMQPConnection($this->configs);
                $this->_conn->connect();
                $this->initConnection();
            } catch (AMQPConnectionException $ex) {
                throw new Exception('cannot connection rabbitmq',500);
            }
        }
    }
    /*
    * rabbitmq连接不变
    * 重置交换机，队列，路由等配置
    */
    public function reset($exchange_name,$queue_name,$route_key){
        $this->exchange_name=$exchange_name;
        $this->queue_name=$queue_name;
        $this->route_key=$route_key;
        $this->initConnection();
    }
    /*
   * 初始化rabbit连接的相关配置
   */
    private function initConnection(){
        if(empty($this->exchange_name) || empty($this->queue_name) || empty($this->route_key)){
            throw new Exception('rabbitmq exhange_name or queue_name or route_key is empty',500);
        }
        //连接channel
        $this->_channel=new AMQPChannel($this->_conn);
        //创建交换机
        $this->_exchange=new AMQPExchange($this->_channel);
        $this->_exchange->setName($this->exchange_name);
        $this->_exchange->setType(AMQP_EX_TYPE_DIRECT);
        if($this->durable){
            $this->_exchange->setFlags(AMQP_DURABLE);
        }
        if($this->autodelete){
            $this->_exchange->setFlags(AMQP_AUTODELETE);
        }
       // $this->_exchange->declare();
        //创建队列
        $this->_queue=new AMQPQueue($this->_channel);
        $this->_queue->setName($this->queue_name);
        if($this->durable){
            $this->_queue->setFlags(AMQP_DURABLE);
        }
        if($this->autodelete){
            $this->_queue->setFlags(AMQP_AUTODELETE);
        }
        if($this->mirror){
            $this->_queue->setArgument('x-ha-policy','all');
        }
       // $this->_queue->declare();
        //将队列通过制定路由绑定到指定交换机上
        $this->_queue->bind($this->exchange_name,$this->route_key);

    }

    public function close(){

        if($this->_conn){
            $this->_conn->disconnect();
        }
    }
    public function __sleep(){
        $this->close();
        return array_keys(get_object_vars($this));
    }
    public function __destruct(){
        $this->close();
    }
    /*
     * 生产者发送消息
     */
    public function send($msg) {
        $this->open();
        if(is_array($msg)){
            $msg = json_encode($msg);
        }else{
            $msg = trim(strval($msg));
        }
        return $this->_exchange->publish($msg, $this->route_key);
    }
    /*
    * 消费者
    * $fun_name = array($classobj,$function) or function name string
    * $autoack 是否自动应答
    *
    * function processMessage($envelope, $queue) {
           $msg = $envelope->getBody();
           echo $msg."\n"; //处理消息
           $queue->ack($envelope->getDeliveryTag());//手动应答
       }
    */
    public function run($fun_name,$autoack=True){
        $this->open();
        if(!$fun_name || !$this->_queue) return false;
        while(True){
            if($autoack) $this->_queue->consume($fun_name,AMQP_AUTOACK);
            else $this->_queue->consume($fun_name);
        }
    }

}