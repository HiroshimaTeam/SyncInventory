<?php

namespace Tarzan;


use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use Tarzan\Api\SyncPlayerAPI;
use Tarzan\Event\PlayerListenner;

class Main extends PluginBase{


    private static self $instance;
    private static DataConnector $database;
    private static SyncPlayerAPI $infoplayers;

    public function onLoad()
    {
        $this->saveResource("config.yml");
        self::$instance = $this;
        self::$database = libasynql::create($this, $this->getConfig()->get("database"), [
            "mysql" => "mysql.sql"
        ]);
        self::$infoplayers = new SyncPlayerAPI();

    }


    public function onEnable()
    {
        self::$database->executeGeneric('SyncPLayer.init');
        self::$database->waitAll();
        $this->getServer()->getPluginManager()->registerEvents(new PlayerListenner(),$this);
    }

    public function onDisable()
    {
       if (self::$database !== null){
           self::$database->waitAll();
           sleep(2);
           self::$database->close();
       }
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        return self::$instance;
    }

    /**
     * @return DataConnector
     */
    public static function getDatabase(): DataConnector
    {
        return self::$database;
    }

    /**
     * @return SyncPlayerAPI
     */
    public static function getSyncPlayerAPI(): SyncPlayerAPI
    {
        return self::$infoplayers;
    }
}