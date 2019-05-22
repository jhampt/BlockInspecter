<?php
namespace BlockInspecter\Main\Providers;
use BlockInspecter\Main\Main;
use pocketmine\block\Block;
use pocketmine\Player;
class SQLite3Provider implements Provider{
    public $logs;
    private $plugin;
    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        if(!file_exists($this->plugin->getDataFolder()."logs/players.db")){
            $this->logs = new \SQLite3($this->plugin->getDataFolder()."logs/logs.db", SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
            $this->logs->exec("CREATE TABLE IF NOT EXISTS logs (player TEXT, x INTEGER, y INTEGER, z INTEGER, block TEXT, action TEXT)");
        }else{
            $this->logs = new \SQLite3($this->plugin->getDataFolder()."logs/logs.db", SQLITE3_OPEN_READWRITE);
        }
    }
    public function getLogsAt(Block $block) : array{
        $l = [];
        $prepare = $this->logs->prepare("SELECT * FROM logs WHERE x = :x AND y = :y AND z = :z");
        $prepare->bindValue(":x", $block->x, SQLITE3_INTEGER);
        $prepare->bindValue(":y", $block->y, SQLITE3_INTEGER);
        $prepare->bindValue(":z", $block->z, SQLITE3_INTEGER);
        $results = $prepare->execute();
        while($row = $results->fetchArray()){
            $l[] = $row;
        }
        return $l;
    }
    public function log($action, Block $block, Player $player) : void{
        $prepare = $this->logs->prepare("INSERT INTO logs (player, x, y, z, block, action) VALUES (:player, :x, :y, :z, :block, :action)");
        $prepare->bindValue(":player", strtolower($player->getName()), SQLITE3_TEXT);
        $prepare->bindValue(":x", $block->x, SQLITE3_INTEGER);
        $prepare->bindValue(":y", $block->y, SQLITE3_INTEGER);
        $prepare->bindValue(":z", $block->z, SQLITE3_INTEGER);
        $prepare->bindValue(":block", $block->getName(), SQLITE3_TEXT);
        $prepare->bindValue(":action", $action, SQLITE3_TEXT);
        $prepare->execute();
    }
    public function close() : void{
        $this->logs->close();
    }
}
