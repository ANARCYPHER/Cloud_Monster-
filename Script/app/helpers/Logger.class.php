<?php


namespace CloudMonster\Helpers;


/**
 * Class Logger
 * @package CloudMonster\Helpers
 */
class Logger{


    /**
     * Log entry info
     * @var array
     */
    protected static array $logEntry = [];

    /**
     * Log directory
     * @var string
     */
    protected static string $logDir = '';

    /**
     * Log file
     * @var string
     */
    protected static string $logFile = '';

    /**
     * Log filename
     * @var string
     */
    public static string $logFilename = 'logger';

    /**
     * Log level
     * @var string
     */
    protected static string $logLevel = 'debug';

    /**
     * Ready of log
     * @var bool
     */
    protected static bool $isLogReady = false;

    /**
     * Logger status
     * @var bool
     */
    protected static bool $isOn = false;


    /**
     * Info log
     * @param $msg
     */
    public static function info($msg){
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        self::add($msg, 'info', $bt);
    }

    /**
     * Debug log
     * @param $msg
     */
    public static function debug($msg){
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        self::add($msg, 'debug', $bt);
    }

    /**
     * Error log
     * @param $msg
     */
    public static function error($msg, $bt = null){
        if(empty($bt)){
            $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        }
        self::add($msg, 'error', $bt);
    }

    /**
     * Warning log
     * @param $msg
     */
    public static function warn($msg, $bt = null){
        if(empty($bt)){
            $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        }
        self::add($msg, 'warn', $bt);
    }

    /**
     * Fatal log
     * @param $msg
     */
    public static function fatal($msg){
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        self::add($msg, 'fatal', $bt);
    }

    /**
     * Open logfile
     */
    private static function openLog(){


        if(self::$isOn){
            //set log directory and file
            $logDir = Help::storagePath('logs');
            if(is_dir($logDir)){
                $logFile = Help::cleanDS($logDir . '/' . self::$logFilename . '.json');
                if(!file_exists($logFile)){
                    @file_put_contents($logFile, '');
                }
            }
        }

        //check log is ready or not
        if(isset($logFile)){
            self::$logDir = $logDir;
            self::$logFile = $logFile;
            if(file_exists($logFile) && !is_dir($logFile)){
                self::$isLogReady = true;
            }
        }

        if(self::$isOn && !self::$isLogReady) die('Logger initialization failed.');

    }

    /**
     * Add log
     * @param $message
     * @param string $level
     * @param array $backtrace
     */
    private static function add($message, string $level, array $backtrace){

        //extract caller file line and path
        $caller = array_shift($backtrace);

        if(!isset($caller['line'])) return '';

        $line = $caller['line'];
        $path = str_replace(ROOT, '',$caller['file']);
        $path = ltrim($path, '\\');

        //create log entry
        $logEntry = [
            'timestamp' => Help::timeNow(),
            'level' => $level,
            'path' => $path,
            'line' => $line,
            'message' => $message
        ];
        self::$logEntry = $logEntry;

        //initialize the logger if it hasn't been done already
        if(!self::$isLogReady){
            self::openLog();
        }

        //write log
        if(self::$isLogReady) self::write();

    }

    /**
     * Write log
     */
    private static function write(){

        $handle = @fopen(self::$logFile, 'r+');
        if($handle){
            fseek($handle, 0, SEEK_END);
            if(ftell($handle) > 0){
                //move back  a byte
                fseek($handle, -1, SEEK_END);
                //add the trailing comma
                fwrite($handle, ',', 1);
                //add the new json string
                fwrite($handle, json_encode(self::$logEntry) . ']');
            }else{
                //write the first time
                fwrite($handle, json_encode([self::$logEntry]));
            }
            fclose($handle);
        }

    }

    /**
     * Logger ON
     */
    public static function on(){
        self::$isOn = true;
    }

    /**
     * Logger OFF
     */
    public static function off(){
        self::$isOn = false;
    }

    /**
     * Read log data
     * @return array
     */
    public static function getLogData() : array{
        $data = [];
        $logger = Help::cleanDS( Help::storagePath('logs') . '/' . 'logger.json');
        if(file_exists($logger)){
            $json = file_get_contents($logger);
            if(Help::isJson($json)){
                $data = Help::toArray($json);
            }
        }
        return $data;
    }




}