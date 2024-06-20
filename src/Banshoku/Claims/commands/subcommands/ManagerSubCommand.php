<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ManagerSubCommand extends BaseSubCommand {

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
        $claims = $claimManager->getClaims();
        $permissions = $claimManager->getPermissions();

        $senderName = $sender->getName();
        $sender->sendMessage(TextFormat::GREEN . "Claim Manager:");

        foreach ($claims as $chunkId => $owner) {
            if ($owner === $senderName) {
                $sender->sendMessage(TextFormat::YELLOW . "Claim: " . $chunkId);
                $sender->sendMessage(TextFormat::YELLOW . "Permissions: " . implode(", ", $permissions[$chunkId] ?? []));
            }
        }
    }
}