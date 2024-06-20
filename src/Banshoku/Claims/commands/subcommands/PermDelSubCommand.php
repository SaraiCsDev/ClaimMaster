<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Banshoku\Claims\Main;

class PermDelSubCommand extends BaseSubCommand {

    protected function prepare(): void {

        $this->registerArgument(0, new RawStringArgument("player", true));

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {

        if (!$sender instanceof Player) {

            $sender->sendMessage("This command can only be used in-game.");

            return;

        }

        $plugin = Main::getInstance();

        $claimManager = $plugin->getClaimManager();

        $targetName = $args["player"];

        $chunkX = $sender->getPosition()->getFloorX() >> 4;

        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;

        $chunkId = "{$chunkX}:{$chunkZ}";

        $claims = $claimManager->getClaims();

        $permissions = $claimManager->getPermissions();

        if (!isset($claims[$chunkId]) || $claims[$chunkId] !== $sender->getName()) {

            $sender->sendMessage(TextFormat::RED . "You do not own this chunk.");

            return;

        }

        if (!isset($permissions[$chunkId]) || !in_array($targetName, $permissions[$chunkId])) {

            $sender->sendMessage(TextFormat::RED . "Player does not have permission in this chunk.");

            return;

        }

        $permissions[$chunkId] = array_filter($permissions[$chunkId], function($name) use ($targetName) {

            return $name !== $targetName;

        });

        $claimManager->setPermissions($permissions);

        $sender->sendMessage(TextFormat::GREEN . "Removed " . $targetName . " from your chunk permissions.");

    }

}
