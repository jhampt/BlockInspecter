<?php
namespace BlockInspecter\Providers;
use BlockInspecter\BlockInspecter\Main;
use pocketmine\block\Block;
use pocketmine\Player;
class JsonProvider implements Provider{
    private $plugin;
    private $logs = [];
    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        $logsPath = $this->plugin->getDataFolder()."logs/";
        $files = array_diff(scandir($logsPath), ["..", "."]);
        if(count($files) > 0){
            foreach($files as $file){
                $name = substr($file, 0, strlen($file) - 5);
                $this->logs[$name] = json_decode(file_get_contents($logsPath.$file), true);
            }
        }
    }
    public function getLogsAt(Block $block) : array{
        $l = [];
        foreach($this->logs as $player => $logs){
            foreach($logs as $log){
                if($log["x"] === $block->x and $log["y"] === $block->y and $log["z"] === $block->z and $log["level"] === $block->level->getName()){
                    $log["player"] = $player;
                    $l[] = $log;
                }
            }
        }
        return $l;
    }
    public function log($action, Block $block, Player $player) : void{
        $this->logs[strtolower($player->getName())][] = [
            "action" => $action,
            "x" => $block->x,
            "y" => $block->y,
            "z" => $block->z,
            "level" => $block->level->getName(),
            "block" => $block->getName()
        ];
    }
    public function close() : void{
        foreach($this->logs as $name => $logs){
            file_put_contents($this->plugin->getDataFolder()."logs/$name.json", json_encode($logs));
        }
    }
}
