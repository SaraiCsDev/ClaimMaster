<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Banshoku\Claims\Main;

class FlySubCommand extends BaseSubCommand {

    protected function prepare(): void {

        $this->setPermission("claims.vip");

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {

        if (!$sender instanceof Player) {

            $sender->sendMessage("This command can only be used in-game.");

            return;

        }

        if (!$sender->hasPermission("claims.vip")) {

            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");

            return;

        }

        if ($sender->isCreative()) {

            $sender->sendMessage(TextFormat::RED . "This command cannot be used in creative mode.");

            return;

        }

        $plugin = Main::getInstance();

        $claimManager = $plugin->getClaimManager();

        $chunkX = $sender->getPosition()->getFloorX() >> 4;

        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;

        $chunkId = $chunkX . ":" . $chunkZ;

        $claims = $claimManager->getClaims();

        if (isset($claims[$chunkId]) && $claims[$chunkId] === $sender->getName()) {

            if ($sender->getAllowFlight()) {

                $sender->setFlying(false);

                $sender->setAllowFlight(false);

                $sender->sendMessage(TextFormat::RED . "Flight disabled.");

            } else {

                $sender->setAllowFlight(true);

                $sender->sendMessage(TextFormat::GREEN . "Flight enabled.");

            }

        } else {

            $sender->sendMessage(TextFormat::RED . "You can only enable flight in your own claim.");

        }

    }

}
