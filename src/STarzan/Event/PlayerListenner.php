<?php


namespace STarzan\Event;


use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDataSaveEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use STarzan\Utils\AntiDupli;
use STarzan\Main;

class PlayerListenner implements Listener
{

    /**
     * Ajoute le joueur pour qu'il soit verifer si sont inventaire sois mi
     * @param PlayerPreLoginEvent $event
     */
    public function onPreJoin(PlayerPreLoginEvent $event){
        AntiDupli::getInstance()->setAntiDropPlayer($event->getPlayer()->getXuid());
    }

    /**
     * Permet de set les Info du Joueur de la dataBase sur le Joueur
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event)
    {
        Main::getSyncPlayerAPI()->SyncPlayer($event->getPlayer());
    }

    /**
     * Permet d'enlever un bug de duplication
     * @param PlayerDropItemEvent $event
     */
    public function onDrop(PlayerDropItemEvent $event){
        if (!AntiDupli::getInstance()->getAntiDropPlayer($event->getPlayer()->getXuid())){
            $event->setCancelled();
        }
    }

    /**
     * Permet d'enlever un bug de duplication
     * @param InventoryOpenEvent $event
     */
    public function onOpenInventory(InventoryOpenEvent $event){
        if (!AntiDupli::getInstance()->getAntiDropPlayer($event->getPlayer()->getXuid())){
            $event->setCancelled();
        }
    }

    /**
     * Permet d'arrrete la transaction de item avant que l'inventaire soit mis
     * @param InventoryTransactionEvent $event
     */
    public function onTransaction(InventoryTransactionEvent $event){
        if (!AntiDupli::getInstance()->getAntiDropPlayer($event->getTransaction()->getSource()->getXuid())){
            $event->setCancelled();
        }
    }


    /**
     * Permet de set les Info du Joueur dans la dataBase
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        Main::getSyncPlayerAPI()->registerInv($player);
        AntiDupli::getInstance()->removeAntiDropPlayer($player->getXuid());
    }

    /**
     * Permet de set les Info du Joueur dans la dataBase
     * @param PlayerDataSaveEvent $event
     */
    public function onSave(PlayerDataSaveEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            if ($player->isOnline()) {
                Main::getSyncPlayerAPI()->registerInv($player);
            }
        }
    }


}