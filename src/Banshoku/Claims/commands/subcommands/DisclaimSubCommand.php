<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

class DisclaimSubCommand extends BaseSubCommand {

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

        $chunkX = $sender->getPosition()->getFloorX() >> 4;

        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;

        $chunkId = "{$chunkX}:{$chunkZ}";

        $claims = $claimManager->getClaims();

        if (isset($claims[$chunkId]) && $claims[$chunkId] === $sender->getName()) {

            $claimManager->removeClaim($chunkId);

            $sender->sendMessage(TextFormat::GREEN . "You have successfully disclaimed this chunk.");

        } else {

            $sender->sendMessage(TextFormat::RED . "You do not own this chunk.");

        }

    }

}