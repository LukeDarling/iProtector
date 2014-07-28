<?php
namespace LDX\iProtector;
use pocketmine\math\Vector3;
class Area {
  public function __construct($data) {
    $this->name = strtolower($data["name"]);
    $this->flags = $data["flags"];
    $this->pos1 = $data["pos"][0];
    $this->pos2 = $data["pos"][1];
    $this->plugin = $data["plugin"];
    $this->save();
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
  public function contains($ppos) {
    if((min($this->pos1->getX(),$this->pos2->getX()) <= $ppos->getX()) && (max($this->pos1->getX(),$this->pos2->getX()) >= $ppos->getX()) && (min($this->pos1->getY(),$this->pos2->getY()) <= $ppos->getY()) && (max($this->pos1->getY(),$this->pos2->getY()) >= $ppos->getY()) && (min($this->pos1->getZ(),$this->pos2->getZ()) <= $ppos->getZ()) && (max($this->pos1->getZ(),$this->pos2->getZ()) >= $ppos->getZ())) {
      return true;
    } else {
      return false;
    }
  }
  public function toggleFlag($flag) {
    $this->flags[$flag] = !$this->flags[$flag];
    return $this->flags[$flag];
  }
  public function getPos() {
    return array($this->pos1,$this->pos2);
  }
  public function getData() {
    return array("name" => $this->name,"flags" => $this->flags,"pos" => $this->getPos());
  }
  public function save() {
    $this->plugin->areas[$this->name] = $this;
    $this->plugin->areadata[$this->name] = $this->getData();
    $this->plugin->saveAreas();
  }
  public function delete() {
    $name = $this->getName();
    unset($this->plugin->areas[$name]);
    unlink($this->plugin->getDataFolder() . "areas/$name.yml");
  }
  public function __destruct() { }
}
?>
