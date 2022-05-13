<?php


namespace CloudMonster\Exception;



use CloudMonster\Helpers\Logger;

/**
 * Class MonsterException
 * @package CloudMonster\Exception
 */
class MonsterException extends \Exception{

    /**
     * MonsterException constructor.
     * @param $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = null, $code = 0, Throwable $previous = null) {
        // message save to log
        if(!empty($message))  {
            $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
            Logger::error($message, $bt);
        }
        parent::__construct($message, $code, $previous);
    }



}