<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;

use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

class PermAddSubCommand extends BaseSubCommand {

    protected function prepare(): void {

        $this->registerArgument(0, new RawStringArgument("player", true));

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {

        if (!$sender instanceof Player) {

            $sender->sendMessage("This command can only be used in-game.");

            return;

        }

        $plugin = $this->getOwningPlugin();

        $claimManager = $plugin->getClaimManager();

        $targetName = $args["player"];

        $target = $plugin->getServer()->getPlayerByPrefix($targetName);

        if ($target === null) {

            $sender->sendMessage(TextFormat::RED . "Player not found.");

            return;

        }

        $chunkX = $sender->getPosition()->getFloorX() >> 4;

        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;

        $chunkId = "{$chunkX}:{$chunkZ}";

        $claims = $claimManager->getClaims();

        $permissions = $claimManager->getPermissions();

        if (!isset($claims[$chunkId]) || $claims[$chunkId] !== $sender->getName()) {

            $sender->sendMessage(TextFormat::RED . "You do not own this chunk.");

            return;

        }

        if (!isset($permissions[$chunkId])) {

            $permissions[$chunkId] = [];

        }

        if (!in_array($target->getName(), $permissions[$chunkId])) {

            $permissions[$chunkId][] = $target->getName();

            $claimManager->setPermissions($permissions);

            $sender->sendMessage(TextFormat::GREEN . "Added " . $target->getName() . " to your chunk permissions.");

        } else {

            $sender->sendMessage(TextFormat::RED . "Player already has permissions for this chunk.");

        }

    }

}