<?php

namespace LDX\iProtector;

use pocketmine\math\Vector3;

class Area {

  private $name;
  public $flags;
  private $pos1;
  private $pos2;
  private $level;
  private $whitelist;
  private $plugin;

  public function __construct($name,$flags,$pos1,$pos2,$level,$whitelist,$plugin) {
    $this->name = strtolower($name);
    $this->flags = $flags;
    $this->pos1 = new Vector3($pos1[0],$pos1[1],$pos1[2]);
    $this->pos2 = new Vector3($pos2[0],$pos2[1],$pos2[2]);
    $this->level = $level;
    $this->whitelist = $whitelist;
    $this->plugin = $plugin;
    $this->save();
  }

  public function getName() {
    return $this->name;
  }

  public function getPos1() {
    return array($this->pos1->getX(),$this->pos1->getY(),$this->pos1->getZ());
  }

  public function getPos2() {
    return array($this->pos2->getX(),$this->pos2->getY(),$this->pos2->getZ());
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
      $this->plugin->saveAreas();
      return true;
    }
    return false;
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
      $this->plugin->saveAreas();
      return $this->flags[$flag];
    }
    return false;
  }

  public function getLevel() {
    return $this->level;
  }

  public function isWhitelisted($n) {
    if(in_array($n,$this->whitelist)) {
      return true;
    }
    return false;
  }

  public function setWhitelisted($n,$v = true) {
    if($v) {
      if(!in_array($n,$this->whitelist)) {
        array_push($this->whitelist,$n);
        $this->plugin->saveAreas();
        return true;
      }
    } else {
      if(in_array($n,$this->whitelist)) {
        $key = array_search($n,$this->whitelist);
        array_splice($this->whitelist,$key,1);
        $this->plugin->saveAreas();
        return true;
      }
    }
    return false;
  }

  public function getWhitelist() {
    return $this->whitelist;
  }

  public function save() {
    $this->plugin->areas[$this->name] = $this;
    return true;
  }

  public function delete() {
    unset($this->plugin->areas[$this->getName()]);
    $this->plugin->saveAreas();
    return true;
  }

}
