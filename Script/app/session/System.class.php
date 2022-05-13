<?php


namespace CloudMonster\Session;

use CloudMonster\Helpers\Logger;
use CloudMonster\Helpers\Help;
use Phpfastcache\Core\Pool\TaggableCacheItemPoolInterface;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\CacheManager;

/**
 * Class System
 * @author John Anta
 * @package CloudMonster\Session
 */
class System{

    /**
     * Unique session ID
     * @var string
     */
    protected string $sessId = '';

    /**
     * Session save directory
     * @var string
     */
    protected string $dir = '';

    /**
     * resource type
     * @var string
     */
    protected string $type;

    /**
     * Session expiration time (seconds)
     * @var int
     */
    protected int $expiresAfter = 5;

    /**
     * Check memory usage record is needed or not
     * @var bool
     */
    protected bool $recordMemoryUsage = false;

    /**
     * Current session item
     * @var object
     */
    public  object $sessItem;

    /**
     * Current cache instance
     * @var object
     */
    public  object $cache;

    /**
     * initialization
     * @var bool
     */
    protected bool $isInit = false;


    /**
     * System constructor.
     * @param string $type
     */
    public function __construct(string $type = 'system'){

        //set session storage directory
       $this->dir = Help::storagePath('session');
       $this->type =  $type;

       //attempt to setup cache manager
       if(!$this->setupCacheManager()){
           //attempt to init session cached item
           Logger::debug('cache manager init failed');
       }

    }

    /**
     * Start session
     * @param false $autoSave
     */
    public static function start(bool $autoSave = false){
        if(!static::isInit()){
            static::$instance = new static();
            if($autoSave){
                static::$instance::save();
            }
        }else{
            $tmpInstance = static::getInstance();
            die($tmpInstance->type . ' session already enabled.');
        }
    }

    /**
     * Update session
     */
    public static function update(){

        if(static::isInit()){
            $self = static::$instance;
            if(isset($self->sessItem)){
                $self->sessItem
                    ->set($self->getData())
                    ->expiresAfter($self->expiresAfter);
                $self->cache->save($self->sessItem);
            }
        }

    }

    /**
     * Save session
     */
    public static function save(){
        if(static::isInit()){
            static::$instance->setupSessItem();
        }
    }

    /**
     * Get all active sessions
     * @return array
     */
    public static function getAll() : array{

        if(static::isInit()){
            $self = static::$instance;
            $items = $self->cache->getItemsByTag($self->type, TaggableCacheItemPoolInterface::TAG_STRATEGY_ALL );
            if(!empty($items)){
                return $items;
            }
        }

        return [];

    }

    /**
     * destroy sessions
     */
    public static function destroy(){
        if(static::isInit()){
            $self = static::$instance;
            if(isset($self->sessItem)){
                $self->cache->deleteItem($self->sessItem->getKey());
            }
            static::$instance = null;
        }
    }

    /**
     * Remove sessions
     * @param array $itemIds
     */
    public static function removeSessItems(array $itemIds = []){
        if(static::isInit()){
            static::$instance->cache->deleteItems($itemIds);
        }
    }

    /**
     * Setup session item
     */
    protected function setupSessItem(){

        $this->sessId = strtolower(Help::random(8));

        if(isset($this->cache)){

            $sessItem = $this->cache->getItem($this->sessId);

            if(!$sessItem->isHit()){

                $sessItem
                    ->set($this->getData())
                    ->addTag($this->type)
                    ->expiresAfter($this->expiresAfter);
                $this->cache->save($sessItem);
            }


            $this->sessItem = $sessItem;
            $this->cache->detachAllItems();

        }

    }


    /**
     * Get data to save in session
     * @return array
     */
    protected function getData(): array
    {
        return $this->recordMemoryUsage ? ['memoryUsage' => memory_get_usage()] : [];
    }

    /**
     * Setup cache manager
     * @return bool
     */
    protected function setupCacheManager(): bool
    {

        try{
            CacheManager::setDefaultConfig(new ConfigurationOption([
                'path' => $this->dir,
                'itemDetailedDate' => true
            ]));
            $this->cache = CacheManager::getInstance('files');


            return true;

        }catch(\Exception $e){
            Logger::debug($e->getMessage());
        }

        return false;
    }

    /**
     * Check session is initialized or not
     * @return bool
     */
    public static function isInit(): bool
    {
        return isset(static::$instance);
    }

    /**
     * Get current cache instance
     * @return System
     */
    public static function getInstance()
    {
        if(isset(static::$instance)){
            return static::$instance;
        }
        return new self();
    }

    public function __debugInfo() {
        $vars = get_object_vars($this);
        if(isset($vars['cache'])){
            unset($vars['cache']);
        }
        return $vars;
    }


}