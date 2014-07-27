<?php
namespace LDX\iProtector;
class Area {
  public function __construct($name,$flags,$pos1,$pos2,$plugin) {
    $this->name = $name;
    $this->flags = $flags;
    $this->pos1 = $pos1;
    $this->pos2 = $pos2;
    $this->plugin = $plugin;
  }
  public function setFlag($flag,$value) {
    $this->flags[$flag] = $value;
  }
  public function toggleFlag($flag) {
    $this->flags[$flag] = !$this->flags[$flag];
    return $this->flags[$flag];
  }
  public function __destruct() {
    $this->plugin->saveArea($this);
  }
}
?>
