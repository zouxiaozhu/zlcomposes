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

    public function publishWithOutExp($body);

    public function publishWithConfirm($body);

    public function publishWithConfirmWithoutExp($body);

    public function onceConsumer();

    public function consumerBlock(callable $callable, $no_ack = false, $suggest_death_count = 5, $block_num = 0);

    public function consumerBlockRetry();

    public function reconnect();

    public function isConnected();

    public function close();

    public function ping();

    public function channelValid();

}