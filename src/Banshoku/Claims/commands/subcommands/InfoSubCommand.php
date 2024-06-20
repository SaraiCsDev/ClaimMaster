<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Banshoku\Claims\Main;

class InfoSubCommand extends BaseSubCommand {

    protected function prepare(): void {

        // No arguments needed for this subcommand

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {

        if (!$sender instanceof Player) {

            $sender->sendMessage("This command can only be used in-game.");

            return;

        }

        $plugin = Main::getInstance();

        $claimManager = $plugin->getClaimManager();

        $chunkX = $sender->getPosition()->getFloorX() >> 4;

        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;

        $chunkId = "{$chunkX}:{$chunkZ}";

        $claims = $claimManager->getClaims();

        $permissions = $claimManager->getPermissions();

        $bans = $claimManager->getBans();

        $pvpSettings = $claimManager->getPvpSettings();

        if (!isset($claims[$chunkId])) {

            $sender->sendMessage(TextFormat::RED . "This chunk is not claimed.");

            return;

        }

        $owner = $claims[$chunkId];

        $allowedPlayers = implode(", ", $permissions[$chunkId] ?? []);

        $bannedPlayers = implode(", ", $bans[$chunkId] ?? []);

        $pvpStatus = $pvpSettings[$chunkId] ?? "off";

        $sender->sendMessage(TextFormat::GREEN . "Claim Info:");

        $sender->sendMessage(TextFormat::YELLOW . "Owner: " . TextFormat::WHITE . $owner);

        $sender->sendMessage(TextFormat::YELLOW . "Permissions: " . TextFormat::WHITE . $allowedPlayers);

        $sender->sendMessage(TextFormat::YELLOW . "Banned: " . TextFormat::WHITE . $bannedPlayers);

        $sender->sendMessage(TextFormat::YELLOW . "PvP: " . TextFormat::WHITE . $pvpStatus);

    }

}
