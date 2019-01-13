<?php
/**
 * Created by PhpStorm.
 * User: zhanglong
 * Date: 2018/12/7
 * Time: 上午10:50
 */

namespace Zl\Compose\Search\Kernel\Interfaces;

interface SearchInterface
{
    public function __construct($config);

    public function connect();

    public function isConnected();

    public function ping();

    public function close();

    public function isClose();
}