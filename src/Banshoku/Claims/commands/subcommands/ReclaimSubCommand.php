<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Banshoku\economy\Main as Economy;
use Banshoku\Claims\Main;

class ReclaimSubCommand extends BaseSubCommand {

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

        $cost = $plugin->getConfig()->get("claim_cost", 100);

        $balance = Economy::getInstance()->getProvider()->getCoins($sender);

        if ($balance < $cost) {

            $sender->sendMessage(TextFormat::RED . "You do not have enough money to claim this chunk.");

            return;

        }

        Economy::getInstance()->getProvider()->removeCoins($sender, $cost);

        $chunkX = $sender->getPosition()->getFloorX() >> 4;

        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;

        $chunkId = "{$chunkX}:{$chunkZ}";

        $claims = $claimManager->getClaims();

        if (isset($claims[$chunkId])) {

            $sender->sendMessage(TextFormat::RED . "This chunk is already claimed.");

            return;

        }

        $claims[$chunkId] = $sender->getName();

        $claimManager->setClaims($claims);

        $sender->sendMessage(TextFormat::GREEN . "You have successfully claimed this chunk.");

    }

}
