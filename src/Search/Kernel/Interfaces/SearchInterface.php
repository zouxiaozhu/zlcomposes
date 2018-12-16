<?php
/**
 * Created by PhpStorm.
 * User: zhanglong
 * Date: 2018/12/7
 * Time: 上午10:50
 */

namespace Zl\Compose\Mq\Kernel\Interfaces;

use Grpc\Call;

interface SearchInterface
{
    public function __construct($config);

    public function channel();

    public function reconnect();

    public function isConnected();

    public function close();

    public function ping();

}