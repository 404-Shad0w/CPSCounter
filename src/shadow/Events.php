<?php

declare(strict_types=1);

namespace shadow;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use shadow\cps\Cps;

class Events implements Listener
{
    /** @var array<string, int> */
    private array $lastClick = [];

    public function __construct(private PluginBase $plugin)
    {
        $this->plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() {
            $now = time();
            foreach ($this->lastClick as $name => $last) {
                if ($now - $last >= 1) {
                    Cps::resetCps($name);
                    unset($this->lastClick[$name]);
                }
            }
        }), 5);
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void
    {
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();

        if ($player === null) return;

        if ($packet instanceof InventoryTransactionPacket) {
            if ($packet->trData->getTypeId() === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY ||
                $packet->trData->getTypeId() === InventoryTransactionPacket::TYPE_USE_ITEM) {
                $name = $player->getName();
                Cps::addClicks($name);
                $this->lastClick[$name] = time();
                $player->sendActionBarMessage(
                    "§l§aCPS: §f" . Cps::getCps($name)
                );
            }
        }
    }
}