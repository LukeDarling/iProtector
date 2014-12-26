<?php

namespace LDX\iProtector;

use pocketmine\math\Vector3;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;

class Main extends PluginBase implements Listener {

  public function onEnable() {
    $this->getServer()->getPluginManager()->registerEvents($this,$this);
    if(!file_exists($this->getDataFolder() . "areas.json")) {
      if(!is_dir($this->getDataFolder())) {
        mkdir($this->getDataFolder());
      }
      file_put_contents($this->getDataFolder() . "areas.json","[]");
    }
    if(!file_exists($this->getDataFolder() . "config.yml")) {
      if(!is_dir($this->getDataFolder())) {
        mkdir($this->getDataFolder());
      }
      $c = $this->getResource("config.yml");
      $o = fread($c,1048576);
      fclose($c);
      file_put_contents($this->getDataFolder() . "config.yml",str_replace("{DEFAULT}",$this->getServer()->getDefaultLevel()->getName(),$o));
    }
    $this->areas = array();
    $data = json_decode(file_get_contents($this->getDataFolder() . "areas.json"),true);
    foreach($data as $datum) {
      $area = new Area($datum["name"],$datum["flags"],$datum["pos1"],$datum["pos2"],$datum["level"],$this);
    }
    $c = yaml_parse(file_get_contents($this->getDataFolder() . "config.yml"));
    $this->god = $c["Default"]["God"];
    $this->edit = $c["Default"]["Edit"];
    $this->touch = $c["Default"]["Touch"];
    $this->levels = array();
    foreach($c["Worlds"] as $level => $flags) {
      $this->levels[$level] = $flags;
    }
  }

  public function onCommand(CommandSender $p,Command $cmd,$label,array $args) {
    if(!($p instanceof Player)) {
      $p->sendMessage(TextFormat::RED . "Command must be used in-game.");
      return true;
    }
    if(!isset($args[0])) {
      return false;
    }
    $n = strtolower($p->getName());
    $action = strtolower($args[0]);
    switch($action) {
      case "pos1":
        if($p->hasPermission("iprotector") || $p->hasPermission("iprotector.command") || $p->hasPermission("iprotector.command.area") || $p->hasPermission("iprotector.command.area.pos1")) {
          if(isset($this->sel1[$n]) || isset($this->sel2[$n])) {
            $o = "You're already selecting a position!";
          } else {
            $this->sel1[$n] = true;
            $o = "Please place or break the first position.";
          }
        } else {
          $o = "You do not have permission to use this subcommand.";
        }
      break;
      case "pos2":
        if($p->hasPermission("iprotector") || $p->hasPermission("iprotector.command") || $p->hasPermission("iprotector.command.area") || $p->hasPermission("iprotector.command.area.pos2")) {
          if(isset($this->sel1[$n]) || isset($this->sel2[$n])) {
            $o = "You're already selecting a position!";
          } else {
            $this->sel2[$n] = true;
            $o = "Please place or break the second position.";
          }
        } else {
          $o = "You do not have permission to use this subcommand.";
        }
      break;
      case "create":
        if($p->hasPermission("iprotector") || $p->hasPermission("iprotector.command") || $p->hasPermission("iprotector.command.area") || $p->hasPermission("iprotector.command.area.create")) {
          if(isset($args[1])) {
            if(isset($this->pos1[$n]) && isset($this->pos2[$n])) {
              if(!isset($this->areas[strtolower($args[1])])) {
                $area = new Area(array("name" => strtolower($args[1]),"flags" => array("edit" => true,"god" => false,"touch" => false),"pos1" => array($this->pos1[$n]->getX(),$this->pos1[$n]->getY(),$this->pos1[$n]->getZ()),"pos2" => array($this->pos2[$n]->getX(),$this->pos2[$n]->getY(),$this->pos2[$n]->getZ())),$this);
                $this->saveAreas();
                unset($this->pos1[$n]);
                unset($this->pos2[$n]);
                $o = "Area created!";
              } else {
                $o = "An area with that name already exists.";
              }
            } else {
              $o = "Please select both positions first.";
            }
          } else {
            $o = "Please specify a name for this area.";
          }
        } else {
          $o = "You do not have permission to use this subcommand.";
        }
      break;
      case "list":
        if($p->hasPermission("iprotector") || $p->hasPermission("iprotector.command") || $p->hasPermission("iprotector.command.area") || $p->hasPermission("iprotector.command.area.list")) {
          $o = "Areas:";
          foreach($this->areas as $area) {
            $o = $o . " " . $area->getName() . ";";
          }
        }
      break;
      case "flag":
        if($p->hasPermission("iprotector") || $p->hasPermission("iprotector.command") || $p->hasPermission("iprotector.command.area") || $p->hasPermission("iprotector.command.area.flag")) {
          if(isset($args[1])) {
            if(isset($this->areas[strtolower($args[1])])) {
              $area = $this->areas[strtolower($args[1])];
              if(isset($args[2])) {
                if(isset($area->flags[strtolower($args[2])])) {
                  $flag = strtolower($args[2]);
                  if(isset($args[3])) {
                    $mode = strtolower($args[3]);
                    if($mode == ("true" || "on")) {
                      $mode = true;
                    } else {
                      $mode = false;
                    }
                    $area->setFlag($flag,$mode);
                  } else {
                    $area->toggleFlag($flag);
                  }
                  if($area->getFlag($flag)) {
                    $status = "on";
                  } else {
                    $status = "off";
                  }
                  $o = "Flag " . $flag . " set to " . $status . " for area " . $area->getName() . "!";
                } else {
                  $o = "Flag not found. (Flags: edit, god)";
                }
              } else {
                $o = "Please specify a flag. (Flags: god, edit, touch)";
              }
            } else {
              $o = "Area doesn't exist.";
            }
          } else {
            $o = "Please specify the area you would like to flag.";
          }
        } else {
          $o = "You do not have permission to use this subcommand.";
        }
      break;
      case "delete":
        if($p->hasPermission("iprotector") || $p->hasPermission("iprotector.command") || $p->hasPermission("iprotector.command.area") || $p->hasPermission("iprotector.command.area.delete")) {
          if(isset($args[1])) {
            if(isset($this->areas[strtolower($args[1])])) {
              $area = $this->areas[strtolower($args[1])];
              $area->delete();
              $o = "Area deleted!";
            } else {
              $o = "Area does not exist.";
            }
          } else {
            $o = "Please specify an area to delete.";
          }
        } else {
          $o = "You do not have permission to use this subcommand.";
        }
      break;
      default:
        return false;
      break;
    }
    $p->sendMessage($o);
    return true;
  }

  public function onHurt(EntityDamageEvent $event) {
    if($event->getEntity() instanceof Player) {
      $p = $event->getEntity();
      $cancel = false;
      $pos = new Vector3($p->getX(),$p->getY(),$p->getZ());
      foreach($this->areas as $area) {
        if($area->contains($pos,$p->getLevel()->getName()) && $area->getFlag("god")) {
          $cancel = true;
        }
      }
      if($cancel) {
        $event->setCancelled();
      } else {
        if((isset($this->levels[$p->getLevel()->getName()]) ? $this->levels[$p->getLevel()->getName()]["God"] : $this->god)) {
          $event->setCancelled();
        }
      }
    }
  }

  public function onBlockBreak(BlockBreakEvent $event) {
    $b = $event->getBlock();
    $p = $event->getPlayer();
    $n = strtolower($p->getName());
    $cancel = false;
    if(isset($this->sel1[$n])) {
      unset($this->sel1[$n]);
      $this->pos1[$n] = new Vector3($b->getX(),$b->getY(),$b->getZ());
      $p->sendMessage("Position 1 set to: (" . $this->pos1[$n]->getX() . ", " . $this->pos1[$n]->getY() . ", " . $this->pos1[$n]->getZ() . ")");
      $event->setCancelled();
    } else if(isset($this->sel2[$n])) {
      unset($this->sel2[$n]);
      $this->pos2[$n] = new Vector3($b->getX(),$b->getY(),$b->getZ());
      $p->sendMessage("Position 2 set to: (" . $this->pos2[$n]->getX() . ", " . $this->pos2[$n]->getY() . ", " . $this->pos2[$n]->getZ() . ")");
      $event->setCancelled();
    } else {
      $pos = new Vector3($b->x,$b->y,$b->z);
      foreach($this->areas as $area) {
        if($area->contains($pos,$b->getLevel()->getName()) && $area->getFlag("edit") && !($p->hasPermission("iprotector") || $p->hasPermission("iprotector.access"))) {
          $cancel = true;
        }
      }
      if($cancel) {
        $event->setCancelled();
      } else {
        if((isset($this->levels[$b->getLevel()->getName()]) ? $this->levels[$b->getLevel()->getName()]["Edit"] : $this->edit) && !($p->hasPermission("iprotector") || $p->hasPermission("iprotector.access"))) {
          $event->setCancelled();
        }
      }
    }
  }

  public function onBlockPlace(BlockPlaceEvent $event) {
    $b = $event->getBlock();
    $p = $event->getPlayer();
    $n = strtolower($p->getName());
    $cancel = false;
    if(isset($this->sel1[$n])) {
      unset($this->sel1[$n]);
      $this->pos1[$n] = new Vector3($b->getX(),$b->getY(),$b->getZ());
      $p->sendMessage("Position 1 set to: (" . $this->pos1[$n]->getX() . ", " . $this->pos1[$n]->getY() . ", " . $this->pos1[$n]->getZ() . ")");
      $event->setCancelled();
    } else if(isset($this->sel2[$n])) {
      unset($this->sel2[$n]);
      $this->pos2[$n] = new Vector3($b->getX(),$b->getY(),$b->getZ());
      $p->sendMessage("Position 2 set to: (" . $this->pos2[$n]->getX() . ", " . $this->pos2[$n]->getY() . ", " . $this->pos2[$n]->getZ() . ")");
      $event->setCancelled();
    } else {
      $pos = new Vector3($b->x,$b->y,$b->z);
      foreach($this->areas as $area) {
        if($area->contains($pos,$b->getLevel()->getName()) && $area->getFlag("edit") && !($p->hasPermission("iprotector") || $p->hasPermission("iprotector.access"))) {
          $cancel = true;
        }
      }
      if($cancel) {
        $event->setCancelled();
      } else {
        if((isset($this->levels[$b->getLevel()->getName()]) ? $this->levels[$b->getLevel()->getName()]["Edit"] : $this->edit) && !($p->hasPermission("iprotector") || $p->hasPermission("iprotector.access"))) {
          $event->setCancelled();
        }
      }
    }
  }

  public function onBlockTouch(PlayerInteractEvent $event) {
    $b = $event->getBlock();
    $p = $event->getPlayer();
    $cancel = false;
    $pos = new Vector3($b->x,$b->y,$b->z);
    foreach($this->areas as $area) {
      if($area->contains($pos,$b->getLevel()->getName()) && $area->getFlag("touch") && !($p->hasPermission("iprotector") || $p->hasPermission("iprotector.access"))) {
        $cancel = true;
      } else if($area->contains($pos) && $area->getFlag("touch") == false) {
        $allow = true;
      }
    }
    if($cancel) {
      $event->setCancelled();
    } else {
      if((isset($this->levels[$b->getLevel()->getName()]) ? $this->levels[$b->getLevel()->getName()]["Edit"] : $this->edit) && !($p->hasPermission("iprotector") || $p->hasPermission("iprotector.access"))) {
        $event->setCancelled();
      }
    }
  }

  public function saveAreas() {
    file_put_contents($this->getDataFolder() . "areas.json",json_encode($this->areadata));
  }

}
?>
