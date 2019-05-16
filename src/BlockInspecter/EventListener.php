<?php
namespace BlockInspecter;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
class EventListener implements Listener{
    private $BlockInspecter;
    public function __construct(Main $BlockInspecter){
        $this->BlockInspecter = $BlockInspecter;
    }
    public function onBlockBreak(BlockBreakEvent $event) : void{
        if($this->BlockInspecter->checkInspect($event->getBlock(), $event->getPlayer())){
            $event->setCancelled();
        }elseif(!$event->isCancelled() and in_array($event->getPlayer()->getLevel()->getFolderName(), $this->blockProtector->getConfig()->get("worlds"))){
            $this->BlockInspecter->provider->log("broke", $event->getBlock(), $event->getPlayer());
        }
    }
    public function onBlockPlace(BlockPlaceEvent $event) : void{
        if($this->BlockInspecter->checkInspect($event->getBlock(), $event->getPlayer())){
            $event->setCancelled();
        }elseif(!$event->isCancelled() and in_array($event->getPlayer()->getLevel()->getFolderName(), $this->blockProtector->getConfig()->get("worlds"))){
            $this->BlockInspecter->provider->log("placed", $event->getBlock(), $event->getPlayer());
        }
    }
}
