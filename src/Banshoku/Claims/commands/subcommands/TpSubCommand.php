<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use Banshoku\Claims\Main;

class TpSubCommand extends BaseSubCommand {

    protected function prepare(): void {

        $this->registerArgument(0, new IntegerArgument("index", true));

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {

        if (!$sender instanceof Player) {

            $sender->sendMessage("This command can only be used in-game.");

            return;

        }

        $index = $args["index"] - 1;

        $plugin = Main::getInstance();

        $claimManager = $plugin->getClaimManager();

        $claims = $claimManager->getClaims();

        $playerName = $sender->getName();

        $playerClaims = [];

        foreach ($claims as $chunkId => $owner) {

            if ($owner === $playerName) {

                $playerClaims[] = $chunkId;

            }

        }

        if (isset($playerClaims[$index])) {

            list($chunkX, $chunkZ) = explode(":", $playerClaims[$index]);

            $world = $sender->getWorld();
            $chunkX = (int) $chunkX;
            $chunkZ = (int) $chunkZ;
            $x = ($chunkX << 4) + 8; // Center of the chunkk
            $z = ($chunkZ << 4) + 8; // Center of the chunk

            $y = $world->getHighestBlockAt((int)$x, (int)$z) + 1;

            $position = new Position($x, $y, $z, $world);

            $sender->teleport($position);

            $sender->sendMessage(TextFormat::GREEN . "Teleported to your claim #" . ($index + 1) . ".");

        } else {

            $sender->sendMessage(TextFormat::RED . "You do not have a claim with index " . ($index + 1) . ".");

        }

    }

}
