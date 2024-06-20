<?php

namespace Banshoku\Claims\commands;

use Banshoku\Claims\commands\subcommands\ReclaimSubCommand;
use Banshoku\Claims\commands\subcommands\PermAddSubCommand;
use Banshoku\Claims\commands\subcommands\PermDelSubCommand;
use Banshoku\Claims\commands\subcommands\ManagerSubCommand;
use Banshoku\Claims\commands\subcommands\PvpSubCommand;
use Banshoku\Claims\commands\subcommands\BorderSubCommand;
use Banshoku\Claims\commands\subcommands\TpSubCommand;
use Banshoku\Claims\commands\subcommands\SpawnSubCommand;
use Banshoku\Claims\commands\subcommands\BanSubCommand;
use Banshoku\Claims\commands\subcommands\UnbanSubCommand;
use Banshoku\Claims\commands\subcommands\DisclaimSubCommand;
use Banshoku\Claims\commands\subcommands\FlySubCommand;
use Banshoku\Claims\commands\subcommands\InfoSubCommand;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;

class ClaimCommand extends BaseCommand {

    protected function prepare(): void {
        $plugin = $this->getOwningPlugin();
        $this->setPermission("claims.command");
        $this->registerSubCommand(new ReclaimSubCommand($plugin, "reclaim"));
        $this->registerSubCommand(new PermAddSubCommand($plugin, "permadd"));
        $this->registerSubCommand(new PermDelSubCommand($plugin, "permdel"));
        $this->registerSubCommand(new ManagerSubCommand($plugin, "manager"));
        $this->registerSubCommand(new PvpSubCommand($plugin, "pvp"));
        $this->registerSubCommand(new BorderSubCommand($plugin, "border"));
        $this->registerSubCommand(new TpSubCommand($plugin, "tp"));
        $this->registerSubCommand(new SpawnSubCommand($plugin, "spawn"));
        $this->registerSubCommand(new BanSubCommand($plugin, "ban"));
        $this->registerSubCommand(new UnbanSubCommand($plugin, "unban"));
        $this->registerSubCommand(new DisclaimSubCommand($plugin, "disclaim"));
        $this->registerSubCommand(new FlySubCommand($plugin, "fly"));
        $this->registerSubCommand(new InfoSubCommand($plugin, "info"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $this->sendUsage($sender);
    }
}
