<?php
namespace LDX\iProtector;
use LDX\iProtector\Area;
use pocketmine\math\Vector3;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat as Color;
use pocketmine\event\entity\EntityDamageEvent;
class Main extends PluginBase implements Listener {
  public function onEnable() {
    $this->getServer()->getPluginManager()->registerEvents($this,$this);
    
  }
  public function onCommand(CommandSender $p,Command $cmd,$label,array $args) {
    if(!$player instanceof Player) {
      $player->sendMessage(Color::RED . "Command must be used in-game.");
      return true;
    }
    if(!isset($args[0])) {
      return false;
    }
    
  }
  /**
  * @param EntityDamageEvent $event
  *
  * @priority HIGHEST
  * @ignoreCancelled true
  */
  public function onHurt(EntityDamageEvent $event) {
    
  }
  public function onDisable() {
    
  }
}
?>
