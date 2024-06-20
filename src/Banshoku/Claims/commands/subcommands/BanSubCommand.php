<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Banshoku\Claims\Main;

class BanSubCommand extends BaseSubCommand {

    protected function prepare(): void {
        $this->registerArgument(0, new RawStringArgument("player", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        $bannedPlayerName = $args["player"];
        $plugin = Main::getInstance();
        $claimManager = $plugin->getClaimManager();

        $chunkX = $sender->getPosition()->getFloorX() >> 4;
        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;
        $chunkId = $chunkX . ":" . $chunkZ;

        $claims = $claimManager->getClaims();

        if (!isset($claims[$chunkId]) || $claims[$chunkId] !== $sender->getName()) {
            $sender->sendMessage(TextFormat::RED . "You do not own this chunk.");
            return;
        }

        $bans = $claimManager->getBans();

        if (!isset($bans[$chunkId])) {
            $bans[$chunkId] = [];
        }

        if (!in_array($bannedPlayerName, $bans[$chunkId])) {
            $bans[$chunkId][] = $bannedPlayerName;
            $claimManager->setBans($bans);
            $sender->sendMessage(TextFormat::GREEN . "$bannedPlayerName has been banned from this chunk.");
            
            // Expulsar al jugador si está en el chunk
            $bannedPlayer = $plugin->getServer()->getPlayerByPrefix($bannedPlayerName);
            if ($bannedPlayer instanceof Player) {
                $claimManager->checkPlayerBans($bannedPlayer);
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "$bannedPlayerName is already banned from this chunk.");
        }
    }
}
