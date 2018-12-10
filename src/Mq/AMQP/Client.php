<?php
/**
 * Created by PhpStorm.
 * User: zhanglong
 * Date: 2018/12/7
 * Time: 上午10:54
 */

namespace Zl\Compose\Mq\AMQP;

use App\Enum\CodeEnum;
use Framework\Handlers\ConfigHandler;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Zl\Compose\Mq\Exp\ChannelExp;
use Zl\Compose\Mq\Exp\ConnectExp;
use Zl\Compose\Mq\Kernel\Interfaces\MQInterface;

/**
 * Class Client
 * @package Zl\Compose\Mq\AMQP
 */
class Client implements MQInterface
{
    /**
     * @var
     */
    protected $config_key;

    /**
     * @var AMQPChannel $channel
     */
    protected $channel;

    /**
     * @var AMQPStreamConnection $connection
     */
    protected $connection;

    /**
     * @var AMQPChannel $exchange
     */
    protected $exchange;

    /**
     * @var
     */
    protected $queue;

    /**
     * @var string $queue_name
     */
    protected $queue_name;

    /**
     * @var string $exchange_name
     */
    protected $exchange_name;
    /**
     * @var $config
     */
    protected $config;

    /**
     * @var $routing_key
     */
    protected $routing_key;

    /**
     * Client constructor.
     * @param $config
     * @throws ConnectExp
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->_connect($config);
    }

    /**
     * @param $config
     * @return $this
     * @throws ConnectExp
     */
    public function channel()
    {
        /**
         * @var AMQPStreamConnection $amqpConnection
         */
        $this->channel = $this->connection->channel();
        return $this;
    }


    /**
     * @param $exchange_name
     * @param string $type
     * @return $this
     */
    public function setExchange($exchange_name, $type = AMQP_EX_TYPE_DIRECT)
    {
        $this->channel->exchange_declare($exchange_name, $type, false, true);
        $this->exchange_name = $exchange_name;
        return $this;
    }

    /**
     * @param $queue_name
     * @param string $type
     * @return $this
     */
    public function setQueue($queue_name, $type = AMQP_EX_TYPE_DIRECT, $args = [], $delayed_requeue_ms = 10000)
    {
        if ($delayed_requeue_ms) {
            $subQueueName = $queue_name . '__dlx';
            $subDelayedQueueName = $subQueueName . '_requeue_' . $delayed_requeue_ms . 'ms';
        }

        $this->channel->queue_declare($subDelayedQueueName, false, false, false, false, false,
            new AMQPTable([
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => $subQueueName,
                'x-message-ttl' => 50000
            ]));

        $this->channel->queue_declare($subQueueName, false, false, false, false, false,
            new AMQPTable([
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => $subDelayedQueueName,
            ]));

        $this->queue_name = $subQueueName;
        return $this;
    }

    /**
     * @param $routing_key
     * @param array $args
     * @return $this
     */
    public function bind($routing_key, $args = [])
    {
        $this->routing_key = $routing_key;
        $this->channel->queue_bind($this->queue_name, $this->exchange_name, $routing_key);
        return $this;
    }

    /**
     * @param $exchange_name
     * @param $routing_key
     * @param $body
     * @param $config_key
     */
    public function publish($body)
    {
        $this->isChannelValid();
        $body = is_array($body) ? json_encode($body) : $body;
        $msgBody = new AMQPMessage($body, ['delivery_mode' => AMQP_DURABLE]);
        return $this->channel->basic_publish(
            $msgBody, $this->exchange_name, $this->routing_key);
    }

    /**
     *
     */
    public static function publishMsg()
    {

    }

    /**
     *
     */
    public function publishWithOutExp()
    {
        // TODO: Implement publishWithOutExp() method.
    }

    /**
     *
     */
    public function publishWithConfirm()
    {
        // TODO: Implement publishWithConfirm() method.
    }

    /**
     *
     */
    public function publishWithConfirmWithoutExp()
    {
        // TODO: Implement publishWithConfirmWithoutExp() method.
    }

    /**
     *
     */
    public function onceConsumer()
    {
        // TODO: Implement onceConsumer() method.
    }

    /**
     *
     */
    public function consumerBlock()
    {
        // TODO: Implement onceConsumer() method.
    }

    /**
     *
     */
    public function consumerBlockRetry()
    {
        // TODO: Implement consumerBlockRetry() method.
    }

    /**
     *
     */
    public function reconnect()
    {
        // TODO: Implement reconnect() method.
    }

    /**
     *
     */
    public function close()
    {
        // TODO: Implement close() method.
    }

    /**
     *
     */
    public function ping()
    {
        // TODO: Implement ping() method.
    }

    /**
     * @param array $config
     * @return AMQPStreamConnection
     * @throws ConnectExp
     */
    public function _connect(array $config, $pconnect = true)
    {
        if (!$config) {
            throw new ConnectExp("连接参数异常", 500);
        }

        $this->config_key = $this->getConfigAlias($config);
//        echo   $config['host'], $config['port'], $config['login'], $config['password'];die;

        try {
            $AMQPConnection = new AMQPStreamConnection(
                $config['host'], $config['port'], $config['login'], $config['password'], $config['vhost']
            );
        } catch (\Exception $exception) {
            zxzLogExp($exception);
            $AMQPConnection = new AMQPStreamConnection(
                $config['host'], $config['port'], $config['login'], $config['password'], $config['vhost']
            );
        }

//        if (!$AMQPConnection->isConnected()) {
//            throw new ConnectExp("连接参数异常", 500);
//        }

        $this->connection = $AMQPConnection;
        return $this;
    }

    /**
     * @return bool
     */
    public function channelValid()
    {
        if (!empty($this->channel)) return false;
        $result = $this->channel->getConnection()->select(0);
        if ($result === 1 or $result === false) {
            throw new ChannelExp('连接失败', CodeEnum::getEnum('AMQP_CONN_ERROR'));
        }

        return true;
    }

    /**
     * @return bool
     * @throws ConnectExp
     */
    public function isConnected()
    {
        if (!$this->connection) {
            throw new ConnectExp("连接不存在", 500);
        }

        return $this->connection->isConnected();
    }

    /**
     * @param $config
     * @return string
     */
    protected function getConfigAlias($config): string
    {
        $configKey = trim(($config['host'] ?? '') . ($config['username'] ?? ''));
        return $configKey;
    }

    /**
     * @return mixed
     */
    public function getConfigKey()
    {
        return $this->config_key;
    }

    /**
     *
     */
    public function isChannelValid()
    {
        return $this->channelValid();
    }
}