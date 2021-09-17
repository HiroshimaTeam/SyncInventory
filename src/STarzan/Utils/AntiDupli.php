<?php

namespace src\Tarzan\Utils;

class AntiDupli
{

    private array $antidupli = [];

    private static AntiDupli $instance;

    public function __construct()
    {
        self::$instance = $this;
    }



    public function setAntiDropPlayer(string $name, bool $value = false){
        $this->antidupli[$name] = $value;
    }

    public function getAntiDropPlayer(string $name){
        return $this->antidupli[$name] ?? false;
    }

    public function removeAntiDropPlayer(string $name){
        if (isset($this->antidupli[$name])) {
            unset($this->antidupli[$name]);
        }
    }


    /**
     * @return AntiDupli
     */
    public static function getInstance(): AntiDupli
    {
        return self::$instance;
    }
}