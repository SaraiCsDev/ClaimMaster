<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Banshoku\Claims\Main;

class BorderSubCommand extends BaseSubCommand {
    protected function prepare(): void {
        // No arguments needed for this subcommand
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        $plugin = $this->getOwningPlugin();
        $claimManager = $plugin->getClaimManager();

        if ($claimManager->isBorderActive($sender)) {
            $claimManager->setBorderActive($sender, false);
            $sender->sendMessage(TextFormat::RED . "Border display deactivated.");
        } else {
            $claimManager->setBorderActive($sender, true);
            $sender->sendMessage(TextFormat::GREEN . "Border display activated.");
        }
    }
}