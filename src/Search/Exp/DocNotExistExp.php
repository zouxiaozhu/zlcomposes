<?php
/**
 * Created by PhpStorm.
 * User: zhanglong
 * Date: 2019/1/2
 * Time: 10:57 AM
 */

namespace Zl\Compose\Search;

class DocNotExistExp extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $this->message = sprintf(get_class_name(__CLASS__) . '%s', $message );
        $this->code = $code;
    }
}