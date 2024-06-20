<?php

declare(strict_types=1);

namespace Banshoku\Claims;

use pocketmine\plugin\PluginBase;
use Banshoku\Claims\commands\ClaimCommand;
use Banshoku\Claims\task\BorderTask;

class Main extends PluginBase {

    private static Main $instance;

    private ClaimManager $claimManager;

    protected function onEnable(): void {
        self::$instance = $this;

        $this->saveDefaultConfig();
        $this->claimManager = new ClaimManager($this);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("claim", new ClaimCommand($this));

        $this->getScheduler()->scheduleRepeatingTask(new BorderTask($this), 20);
    }

    public function onDisable(): void {
        $this->claimManager->saveClaimsData();
    }

    public static function getInstance(): Main {
        return self::$instance;
    }

    public function getClaimManager(): ClaimManager {
        return $this->claimManager;
    }
}
