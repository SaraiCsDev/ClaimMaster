<?php

declare(strict_types=1);

namespace Banshoku\Claims;

use pocketmine\plugin\PluginBase;
use CortexPE\Commando\PacketHooker;
use Banshoku\Claims\commands\ClaimCommand;
use Banshoku\Claims\task\BorderTask;

class Main extends PluginBase {

    private ClaimManager $claimManager;

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $this->claimManager = new ClaimManager($this);

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("claim", new ClaimCommand($this, "claim", "Manage land claims", ["claims"], "claims.command"));

        $this->getScheduler()->scheduleRepeatingTask(new BorderTask($this), 20);
    }

    public function onDisable(): void {
        $this->claimManager->saveClaimsData();
    }

    public function getClaimManager(): ClaimManager {
        return $this->claimManager;
    }
}
