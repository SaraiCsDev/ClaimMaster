<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class DespawnSubCommand extends BaseSubCommand {

    protected function prepare(): void {
        $this->setPermission("claims.admin");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        if (!$sender->hasPermission("claims.admin")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        $plugin = $this->getOwningPlugin();
        $claimManager = $plugin->getClaimManager();

        $chunkX = $sender->getPosition()->getFloorX() >> 4;
        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;
        $chunkId = "{$chunkX}:{$chunkZ}";

        if ($claimManager->removeSpawnChunk($chunkId)) {
            $sender->sendMessage(TextFormat::GREEN . "Chunk ({$chunkX}, {$chunkZ}) has been removed from spawn chunks.");
        } else {
            $sender->sendMessage(TextFormat::RED . "This chunk is not a spawn chunk.");
        }
    }
}