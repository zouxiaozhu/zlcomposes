<?php
/**
 * Created by PhpStorm.
 * User: zhanglong
 * Date: 2018/12/7
 * Time: 上午10:50
 */

namespace Zl\Compose\Mq\Kernel\Interfaces;

interface MQInterface
{
    public function __construct($config);

    public function channel();

    public function publish($body);

    public function publishWithOutExp();

    public function publishWithConfirm();

    public function publishWithConfirmWithoutExp();

    public function onceConsumer();

    public function consumerBlock();

    public function consumerBlockRetry();

    public function reconnect();

    public function isConnected();

    public function close();

    public function ping();

    public function channelValid();

}