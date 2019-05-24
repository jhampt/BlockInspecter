<?php
namespace BlockInspecter\Main;
use BlockInspecter\BlockInspecter\Providers\JsonProvider;
use BlockInspecter\BlockInspecter\Providers\SQLite3Provider;
use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
class Main extends PluginBase{
    public $inspect = [];
    public $provider;
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."logs/");
        $this->saveDefaultConfig();
        switch($this->getConfig()->get("provider")){
            case "json":
                $this->provider = new JsonProvider($this);
                $this->getLogger()->info("data provider set to json");
                break;
            case "sqlite3":
                $this->provider = new SQLite3Provider($this);
                $this->getLogger()->info("data provider set to sqlite3");
                break;
            default:
                $this->provider = new SQLite3Provider($this);
                $this->getLogger()->info("data provider set to sqlite3");
                break;
        }
    }
    public function onDisable(){
        $this->provider->close();
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(!isset($args[0])){
            $sender->sendMessage("Usage: /ib inspect to enable block inspector");
            return true;
        }
        $sub = array_shift($args);
        if(strtolower($sub) === "inspect" or strtolower($sub) === "i" or strtolower($sub) === "wand"){
            if(isset($this->inspect[$sender->getName()])){
                unset($this->inspect[$sender->getName()]);
                $sender->sendMessage("you disabled the inspector");
                return true;
            }
            $this->inspect[$sender->getName()] = true;
            $sender->sendMessage("you enabled the inspector");
            $sender->sendMessage("place or break blocks to see who built at its position");
            return true;
        }
        $sender->sendMessage("Usage: /ib inspect");
        return true;
    }
    public function checkInspect(Block $block, Player $player) : bool{
        if(isset($this->inspect[$player->getName()])){
            $logs = $this->provider->getLogsAt($block);
            if(count($logs) === 0){
                $player->sendMessage("no logs found at this position");
            }else{
                foreach($logs as $log){
                    $player->sendMessage("[Log] ".$log["player"]." ".$log["action"]." ".$log["block"]." here");
                }
            }
            return true;
        }
        return false;
    }
}
