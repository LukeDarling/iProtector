<?php
namespace LDX\iProtector;
use pocketmine\math\Vector3;
class Area {
  public function __construct($data,$plugin) {
    $this->name = strtolower($data["name"]);
    $this->flags = $data["flags"];
    $this->pos1 = new Vector3($data["pos1"][0],$data["pos1"][1],$data["pos1"][2]);
    $this->pos2 = new Vector3($data["pos2"][0],$data["pos2"][1],$data["pos2"][2]);
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
    return $this->flags[$flag];
  }
  public function setFlag($flag,$value) {
    $this->flags[$flag] = $value;
    $this->save();
    $this->plugin->saveAreas();
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
    $this->save();
    $this->plugin->saveAreas();
    return $this->flags[$flag];
  }
  public function getData() {
    return array("name" => $this->name,"flags" => $this->flags,"pos1" => array($this->pos1->getX(),$this->pos1->getY(),$this->pos1->getZ()),"pos2" => array($this->pos2->getX(),$this->pos2->getY(),$this->pos2->getZ()));
  }
  public function save() {
    $this->plugin->areas[$this->name] = $this;
    $this->plugin->areadata[$this->name] = $this->getData();
  }
  public function delete() {
    $name = $this->getName();
    unset($this->plugin->areas[$name]);
    unset($this->plugin->areadata[$name]);
    $this->plugin->saveAreas();
  }
}
?>
