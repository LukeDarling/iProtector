<?php

namespace LDX\iProtector;

use pocketmine\math\Vector3;

class Area {

  private $name;
  private $flags;
  private $pos1;
  private $pos2;
  private $level;
  private $plugin;

  public function __construct($name,$flags,$pos1,$pos2,$level,$plugin) {
    $this->name = strtolower($name);
    $this->flags = $flags;
    $this->pos1 = new Vector3($pos1[0],$pos1[1],$pos1[2]);
    $this->pos2 = new Vector3($pos2[0],$pos2[1],$pos2[2]);
    $this->level = strtolower($level);
    $this->plugin = $plugin;
    $this->save();
  }

  public function getName() {
    return $this->name;
  }

  public function getFlags() {
    return $this->flags;
  }

  public function getFlag($flag) {
    if(isset($this->flags[$flag])) {
      return $this->flags[$flag];
    }
    return false;
  }

  public function setFlag($flag,$value) {
    if(isset($this->flags[$flag])) {
      $this->flags[$flag] = $value;
      $this->save();
      $this->plugin->saveAreas();
      return true;
    }
    return false;
  }

  public function getPos1() {
    return $this->pos1;
  }

  public function getPos2() {
    return $this->pos2;
  }

  public function getLevel() {
    return $this->level;
  }

  public function contains($pos,$level) {
    if((min($this->pos1->getX(),$this->pos2->getX()) <= $pos->getX()) && (max($this->pos1->getX(),$this->pos2->getX()) >= $pos->getX()) && (min($this->pos1->getY(),$this->pos2->getY()) <= $pos->getY()) && (max($this->pos1->getY(),$this->pos2->getY()) >= $pos->getY()) && (min($this->pos1->getZ(),$this->pos2->getZ()) <= $pos->getZ()) && (max($this->pos1->getZ(),$this->pos2->getZ()) >= $pos->getZ()) && ($this->level == $level)) {
      return true;
    }
    return false;
  }

  public function toggleFlag($flag) {
    if(isset($this->flags[$flag])) {
      $this->flags[$flag] = !$this->flags[$flag];
      $this->save();
      $this->plugin->saveAreas();
      return $this->flags[$flag];
    }
    return false;
  }

  public function save() {
    $this->plugin->areas[$this->name] = $this;
    return true;
  }

  public function delete() {
    $name = $this->getName();
    unset($this->plugin->areas[$name]);
    $this->plugin->saveAreas();
    return true;
  }

}
?>
