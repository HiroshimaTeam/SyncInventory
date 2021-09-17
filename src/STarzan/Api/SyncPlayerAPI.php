<?php
namespace STarzan\Api;

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\Config;
use STarzan\Utils\AntiDupli;
use STarzan\Main;

class SyncPlayerAPI
{

    /**
     * Permet d'enrengistre ou màj le info importante joueur
     * @param Player $player
     */
    public function registerInv(Player $player)
    {
        if ($player->spawned) {
            $xuid = $player->getXuid();
            $xp_lvl = $player->getXpLevel();
            $xp_progress = $player->getXpProgress();
            $inv_armor = [];
            $inv = [];
            $inv_ender = [];
            foreach ($player->getArmorInventory()->getContents() as $slot => $item) {
                $inv_armor[$slot] = $item->jsonSerialize();
            }
            foreach ($player->getInventory()->getContents() as $slot => $item) {
                $inv[$slot] = $item->jsonSerialize();
            }
            foreach ($player->getEnderChestInventory()->getContents() as $slot => $item) {
                $inv_ender[$slot] = $item->jsonSerialize();
            }

            $data = ['xuid' => $xuid, "inv" => json_encode($inv), "armor" => json_encode($inv_armor), "ender" => json_encode($inv_ender), "xpLvl" => $xp_lvl, "xpP" => $xp_progress];
            Main::getDatabase()->executeSelect("SyncPLayer.save", ["xuid" => $xuid], function (array $rows) use ($data) {
                if (count($rows) === 0) {
                    Main::getDatabase()->executeInsert("SyncPLayer.register", $data);
                    return;
                }
                Main::getDatabase()->executeChange("SyncPLayer.update", $data);
            });
        }

    }


    /**
     * Permet de synchronizer les inventaire du joueur et leur xp
     * @param Player $player
     */
    public function SyncPlayer(Player $player): void
    {
        Main::getDatabase()->executeSelect("SyncPLayer.save", ["xuid" => $xuid = $player->getXuid()], function (array $rows) use ($player,$xuid) {
            if (count($rows) === 0) {
                return;
            }
            $xp_lvl = null;
            $xp_progress = null;
            $inv_armor = null;
            $inv_db = null;
            $inv_ender = null;
            foreach ($rows as $result) {
                $inv_db = $result["inv"];
                $inv_armor = $result["armor"];
                $inv_ender = $result["ender"];
                $xp_lvl = $result["xpLvl"];
                $xp_progress = $result["xpP"];
                break;
            }
            if ($player->isConnected()) {
                if ($inv_db !== null) {
                    $inv = $player->getInventory();
                    if ($inv !== null) {
                        $inv->clearAll();
                        foreach (json_decode($inv_db, true) as $slot => $item) {
                            $inv->setItem($slot, Item::jsonDeserialize($item));
                        }
                    }
                }

                if ($inv_armor !== null) {
                    $armor = $player->getArmorInventory();
                    if ($armor !== null) {
                        $armor->clearAll();
                        foreach (json_decode($inv_armor, true) as $slot => $item) {
                            $armor->setItem($slot, Item::jsonDeserialize($item));
                        }
                    }
                }

                if ($inv_ender !== null) {
                    $ender = $player->getEnderChestInventory();
                    if ($ender !== null) {
                        $ender->clearAll();
                        foreach (json_decode($inv_ender, true) as $slot => $item) {
                            $ender->setItem($slot, Item::jsonDeserialize($item));
                        }
                    }
                }

                if ($xp_lvl !== null) {
                    $player->setXpLevel((int)$xp_lvl);
                }
                if ($xp_progress !== null) {
                    $player->setXpProgress((float)$xp_progress);
                }
                AntiDupli::getInstance()->setAntiDropPlayer($xuid,true);
            }
        });
    }


}