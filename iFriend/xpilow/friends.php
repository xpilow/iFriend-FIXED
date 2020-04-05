<?php

namespace xpilow;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerChatEvent;
use xpilow\form_by_jojoe\CustomForm;

class friends extends PluginBase implements Listener{


public $prefix = "§0[§aiFriend§0] §7» ";

public function onEnable(){
@mkdir($this->getDataFolder());
$this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->getLogger()->info($this->prefix."§aSudah diaktifkan oleh §cxpilow RezaG§a!");
} 

public function onJoin(PlayerJoinEvent $event){
$player = $event->getPlayer();
$name = $player->getName();
if(!file_exists($this->getDataFolder().$name.".yml")){
$playerfile = new Config($this->getDataFolder().$name.".yml", Config::YAML);
$playerfile->set("Friend", array());
$playerfile->set("", array());
$playerfile->set("blocked", false);
$playerfile->save();
}else{
$playerfile = new Config($this->getDataFolder().$name.".yml", Config::YAML);
if(!empty($playerfile->get("Invitations"))){
foreach($playerfile->get("Invitations") as $e){
$player->sendMessage($this->prefix."§a".$e."adalah temanmu sekarang!");
}
}

if(!empty($playerfile->get("Friend"))){
foreach($playerfile->get("Friend") as $f){
$v = $this->getServer()->getPlayerExact($f);
if(!$v == null){
$v->sendMessage($this->prefix."§a".$player->getName()." Sedang Online");
}
}
}
}
}
public function onQuit(PlayerQuitEvent $event){
$player = $event->getPlayer();
$name = $player->getName();
$playerfile = new Config($this->getDataFolder().$name.".yml", Config::YAML);
if(!empty($playerfile->get("Friend"))){
foreach($playerfile->get("Friend") as $f){
$v = $this->getServer()->getPlayerExact($f);
if(!$v == null){
$v->sendMessage($this->prefix."§a".$player->getName()." Sedang offline sekarang");
}
}
}
}
public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
if($cmd->getName() == "frend"){
if($sender instanceof Player){
$playerfile = new Config($this->getDataFolder().$sender->getName().".yml", Config::YAML);
if(empty($args[0])){
$sender->sendMessage("§2» iFriend Fixed By xpilow «");
$sender->sendMessage("§2/frend » §2accept » §fTerima pertanyaan");
$sender->sendMessage("§2/frend » §2add » §fUndang teman");
$sender->sendMessage("§2/frend » §2list » §fMemperlihatkan teman Anda");
$sender->sendMessage("§2/frend » §2decline » §fTolak penyelidikan");
$sender->sendMessage("§2/frend » §2remove » §fHapus teman");
$sender->sendMessage("§2/frend » §2block » §fNonaktifkan permintaan teman");
}else{
if($args[0] == "add"){
if(empty($args[1])){
$this->Add($sender);
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if($vplayerfile->get("blocked") == false){
$einladungen = $vplayerfile->get("Invitations");
$einladungen[] = $sender->getName();
$vplayerfile->set("Invitations", $einladungen);
$vplayerfile->save();
$sender->sendMessage($this->prefix."§aPermintaan teman Anda telah dikirim ke  ".$args[1]);
$v = $this->getServer()->getPlayerExact($args[1]);
if(!$v == null){
$v->sendMessage("§a".$sender->getName()." telah mengirimi Anda permintaan pertemanan, menerimanya §2/frend accept [".$sender->getName()."] atau tolak dengan §2 /frend decline ".$sender->getName()."§a!");
}
}else{
$sender->sendMessage($this->prefix."§aPemain ini tidak menerima permintaan pertemanan Anda!");
}
}else{
$sender->sendMessage($this->prefix."§aPemain ini tidak online!");
}
}
}
if($args[0] == "accept"){
if(empty($args[1])){
$this->Accept($sender);
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if(in_array($args[1], $playerfile->get("Invitations"))){
$old = $playerfile->get("Invitations");
unset($old[array_search($args[1], $old)]);
$playerfile->set("Invitations", $old);
$newfriend = $playerfile->get("Friend");
$newfriend[] = $args[1];
$playerfile->set("Friend", $newfriend);
$playerfile->save();
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
$newfriend = $vplayerfile->get("Friend");
$newfriend[] = $sender->getName();
$vplayerfile->set("Friend", $newfriend);
$vplayerfile->save();
if(!$this->getServer()->getPlayerExact($args[1]) == null){
$this->getServer()->getPlayerExact($args[1])->sendMessage($this->prefix."§a".$sender->getName()." telah menerima permintaan pertemanan Anda!");
}
$sender->sendMessage($this->prefix."§a".$args[1]." adalah temanmu sekarang!");
}else{
$sender->sendMessage($this->prefix."§aPemain ini belum mengirimi Anda permintaan pertemanan!");
}
}else{
$sender->sendMessage($this->prefix."§aTidak ada pemain seperti itu!");
}
}
}

if($args[0] == "decline"){
if(empty($args[1])){
$sender->sendMessage($this->prefix."§eGunakan: §2 /frend decline teman [pemain]");
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if(in_array($args[1], $playerfile->get("Invitations"))){
$old = $playerfile->get("Invitations");
unset($old[array_search($args[1], $old)]);
$playerfile->set("Invitations", $old);
$playerfile->save();
$sender->sendMessage($this->prefix."§aPermintaan dari ".$args[1]." ditolak!");
}else{
$sender->sendMessage($this->prefix."§aPemain ini belum mengirimi Anda permintaan pertemanan!");
}
}else{
$sender->sendMessage($this->prefix."§aTidak ada pemain seperti itu!");
}
}
}

if($args[0] == "remove"){
if(empty($args[1])){
$this->Remove($sender);
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if(in_array($args[1], $playerfile->get("Friend"))){
$old = $playerfile->get("Friend");
unset($old[array_search($args[1], $old)]);
$playerfile->set("Friend", $old);
$playerfile->save();
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
$old = $vplayerfile->get("Friend");
unset($old[array_search($sender->getName(), $old)]);
$vplayerfile->set("Friend", $old);
$vplayerfile->save();
$sender->sendMessage($this->prefix."§a".$args[1]." bukan lagi temanmu!");
}else{
$sender->sendMessage($this->prefix."§aPemain ini bukan temanmu!");
}
}else{
$sender->sendMessage($this->prefix."§aTidak ada pemain seperti itu!");
}
}
}

if($args[0] == "list"){
if(empty($playerfile->get("Friend"))){
$sender->sendMessage($this->prefix."§aAnda tidak punya teman!");
}else{
$sender->sendMessage("§b+-+-+-+ §0[§aTemanmu§0] §b+-+-+-+");
foreach($playerfile->get("Friend") as $f){
if($this->getServer()->getPlayerExact($f) == null){
$sender->sendMessage("§e".$f." » §7§cOffline§7");
}else{
$sender->sendMessage("§e".$f." » §7§aOnline§7");
}
}
}
if($args[0] == "block"){
if($playerfile->get("blocked") === false){
$playerfile->set("blocked", true);
$playerfile->save();
$sender->sendMessage($this->prefix."§aAnda tidak akan lagi menerima permintaan pertemanan!");
}else{
$sender->sendMessage($this->prefix."§aAnda sekarang akan menerima permintaan pertemanan lagi!");
$playerfile->set("blocked", false);
$playerfile->save();
}
}



}
}else{
$this->getLogger()->info($this->prefix."§2Konsol tidak memiliki teman!");
}
}
return true;
}
public function onChat(PlayerChatEvent $event){
$player = $event->getPlayer();
$msg = $event->getMessage();
$playerfile = new Config($this->getDataFolder().$player->getName().".yml", Config::YAML);
$words = explode(" ", $msg);
if(in_array(str_replace("@", "", $words[0]), $playerfile->get("Friend"))){
$f = $this->getServer()->getPlayerExact(str_replace("@", "", $words[0]));
if(!$f == null){
$f->sendMessage($this->prefix." §7[§e".str_replace("@", "", $words[0])."§7] §l>>§r ".str_replace($words[0], "", $msg));
$player->sendMessage($this->prefix." §7[§e".str_replace("@", "", $words[0])."§7] §l>>§r ".str_replace($words[0], "", $msg));
}else{
$player->sendMessage($this->prefix."§c".str_replace("@", "", $words[0])." tidak online!");
}
$event->setCancelled();
}
}

public function Add($sender){ 
$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
$form = $api->createCustomForm(function (Player $sender, int $data = null) {
$result = $data;
if($result === null){
return true;
}             
switch($result){
case 0:
$sender->sendMessage("§f[§6Friends§f] > §aSending request friends!");
$command = "friend add $data[0]";
$this->getServer()->getCommandMap()->dispatch($sender, $command);
break;

}
});
$form->setTitle("ADD FRIEND");
$form->addInput("Player Name:","steve");
$form->sendToPlayer($sender);
}

public function Remove($sender){ 
$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
$form = $api->createCustomForm(function (Player $sender, int $data = null) {
$result = $data;
if($result === null){
return true;
}             
switch($result){
case 0:
$sender->sendMessage("§f[§6Friends§f] > §aSending request friends!");
$command = "friend remove $data[0]";
$this->getServer()->getCommandMap()->dispatch($sender, $command);
break;

}
});
$form->setTitle("REMOVE FRIEND");
$form->addInput("Player Name:","steve");
$form->sendToPlayer($sender);
}

public function Accept($sender){ 
$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
$form = $api->createCustomForm(function (Player $sender, int $data = null) {
$result = $data;
if($result === null){
return true;
}             
switch($result){
case 0:
$sender->sendMessage("§f[§6Friends§f] > §aSending request friends!");
$command = "friend accept $data[0]";
$this->getServer()->getCommandMap()->dispatch($sender, $command);
break;

}
});
$form->setTitle("FRIEND ACCEPT");
$form->addInput("Player Name:","steve");
$form->sendToPlayer($sender);
}
}
