<?php


namespace Tarzan\Event;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDataSaveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use Tarzan\Main;

class PlayerListenner implements Listener
{
    /**
     * Permet de set les Info du Joueur de la dataBase sur le Joueur
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event)
    {
        Main::getSyncPlayerAPI()->SyncPlayer($event->getPlayer());
    }


    /**
     * Permet de set les Info du Joueur dans la dataBase
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event)
    {
        Main::getSyncPlayerAPI()->registerInv($event->getPlayer());
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