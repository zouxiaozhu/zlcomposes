<?php
/**
 * Created by PhpStorm.
 * User: zhanglong
 * Date: 2018/12/7
 * Time: 上午11:07
 */

namespace Zl\Compose\Mq\Exp;

class RabbitMqExp extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $this->message = sprintf(get_class_name(__CLASS__) . '%s', $message );
        $this->code = $code;
    }
}