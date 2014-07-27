<?php
namespace LDX\iProtector;
use pocketmine\Player;
use pocketmine\math\Vector3;
class Area {
  public function __construct($data) {
    $this->name = $data["name"];
    $this->flags = $data["flags"];
    $this->pos1 = $data["pos1"];
    $this->pos2 = $data["pos2"];
    $this->plugin = $data["plugin"];
  }
  public function getName() {
    return $this->name;
  }
  public function getFlags() {
    return $this->flags;
  }
  public function getFlag($flag) {
    return $this->flags[$flag];
  }
  public function setFlag($flag,$value) {
    $this->flags[$flag] = $value;
    return $value;
  }
  public function toggleFlag($flag) {
    $this->flags[$flag] = !$this->flags[$flag];
    return $this->flags[$flag];
  }
  public function getPos() {
    return array($this->pos1,$this->pos2);
  }
  public function __destruct() {
    $this->plugin->saveArea($this);
  }
}
?>
