<?php
/**
 * Created by PhpStorm.
 * User: zhanglong
 * Date: 2019/1/12
 * Time: 4:54 PM
 */

namespace Zl\Compose\Mq\AMQP;


class SignHandler
{
    public static function sign($signNo)
    {
        switch ($signNo) {
            case SIGHUP:
                break;
            case SIGINT: // 通常是Ctrl-C
                echo 111;die;
                break;
            case SIGKILL:
                break;
            case SIGQUIT:
                break;
            case SIGHUP:
                break;
        }
    }
}