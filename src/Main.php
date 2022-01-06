<?php
class Main extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener {
  protected $isFlooding = false;
  protected $handleQueue = [];
  public function onLoad() 
  {
    \pocketmine\Server::getInstance()->getLogger()->notice('PocketShield plugin successfully started!');
    \pocketmine\Server::getInstance()->getLogger()->notice('GIT - https://github.com/ddosnikgit/PocketShield');
  }
  public function onEnable()
  {
	$this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  public function isFloodingPackets($ip, $port){
		if(!empty($ip) and !empty($port)){
			if(!isset($this->handleQueue[$ip][$port])){
			    $this->handleQueue[$ip][$port] = [
			        'time' => time(),
			        'packets' => 0
			    ];
		    }

		    if ($this->handleQueue[$ip][$port]['time'] < time()) {
		    	unset($this->handleQueue[$ip][$port]);

		    	return false;
		    }

		    if(time() == $this->handleQueue[$ip][$port]['time']){
		    	$this->handleQueue[$ip][$port]['packets']++;
		    }

		    if($this->handleQueue[$ip][$port]['packets'] > 450){
		    	$this->isFlooding = true;
		    	
			    \pocketmine\Server::getInstance()->getLogger()->warning("Flood detected from: {$ip}");
			    \pocketmine\Server::getInstance()->getLogger()->warning("PacketsPerSecond: {$this->handleQueue[$ip][$port]['packets']}");
                            
			    unset($this->handleQueue[$ip]);

			    return true;
		    }
		    
		    return false;
		}
	}
  public function MethodFlood(\pocketmine\event\server\DataPacketReceiveEvent $pk) {
       if($this->isFloodingPackets($pk->getPlayer()->getAddress(), $pk->getPlayer()->getPort()) == true){
	    $pk->getPlayer()->close('', 'attack reppelled');
	      $this->getServer()->getNetwork()->blockAddress($pk->getPlayer()->getAddress(), 700);
         return;
       }
  }
}
