<?php
namespace LDX\iProtector;
class Area {
  public function __construct($data) {
    $this->name = $data["name"];
    $this->flags = $data["flags"];
    $this->pos1 = $data["pos1"];
    $this->pos2 = $data["pos2"];
    $this->plugin = $data["plugin"];
    $this->plugin->registerArea($this);
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
  public function getData() {
    return array($this->name,$this->flags,$this->getPos());
  }
  public function save() {
    $name = strtolower($this->getName());
    file_put_contents($this->plugin->getDataFolder() . "areas/$name.yml",yaml_emit($this->getData()));
  }
  public function delete() {
    $name = $this->getName();
    unset($this->plugin->areas[$name]);
    unlink($this->plugin->getDataFolder() . "areas/$name.yml");
  }
  public function __destruct() { }
}
?>
