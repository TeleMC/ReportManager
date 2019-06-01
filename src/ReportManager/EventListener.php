<?php
namespace ReportManager;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\Server;

class EventListener implements Listener {
    private $plugin;

    public function __construct(ReportManager $plugin) {
        $this->plugin = $plugin;
    }
}
