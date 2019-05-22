<?php
namespace BlockInspecter\Providers;
use pocketmine\block\Block;
use pocketmine\Player;
interface Provider{
	public function getLogsAt(Block $block) : array;
	public function log($action, Block $block, Player $player) : void;
	public function close() : void;
}
