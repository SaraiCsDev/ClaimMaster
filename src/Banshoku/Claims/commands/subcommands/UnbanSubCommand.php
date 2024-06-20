<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;

use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

class UnbanSubCommand extends BaseSubCommand {

    protected function prepare(): void {

        $this->registerArgument(0, new RawStringArgument("player", true));

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {

        if (!$sender instanceof Player) {

            $sender->sendMessage("This command can only be used in-game.");

            return;

        }

        $unbannedPlayerName = $args["player"];

        $plugin = $this->getOwningPlugin();

        $claimManager = $plugin->getClaimManager();

        $chunkX = $sender->getPosition()->getFloorX() >> 4;

        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;

        $chunkId = "{$chunkX}:{$chunkZ}";

        $claims = $claimManager->getClaims();

        if (!isset($claims[$chunkId]) || $claims[$chunkId] !== $sender->getName()) {

            $sender->sendMessage(TextFormat::RED . "You do not own this chunk.");

            return;

        }

        $bans = $claimManager->getBans();

        if (isset($bans[$chunkId]) && in_array($unbannedPlayerName, $bans[$chunkId])) {

            $index = array_search($unbannedPlayerName, $bans[$chunkId]);

            unset($bans[$chunkId][$index]);

            $claimManager->setBans($bans);

            $sender->sendMessage(TextFormat::GREEN . "$unbannedPlayerName has been unbanned from this chunk.");

        } else {

            $sender->sendMessage(TextFormat::RED . "$unbannedPlayerName is not banned from this chunk.");

        }

    }

}