<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;

use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

class PvpSubCommand extends BaseSubCommand {

    protected function prepare(): void {

        $this->registerArgument(0, new RawStringArgument("state", true));

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {

        if (!$sender instanceof Player) {

            $sender->sendMessage("This command can only be used in-game.");

            return;

        }

        $state = strtolower($args["state"]);

        if ($state !== "on" && $state !== "off") {

            $sender->sendMessage(TextFormat::RED . "Usage: /claim pvp <on|off>");

            return;

        }

        $plugin = $this->getOwningPlugin();

        $claimManager = $plugin->getClaimManager();

        $chunkX = $sender->getPosition()->getFloorX() >> 4;

        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;

        $chunkId = "{$chunkX}:{$chunkZ}";

        $claims = $claimManager->getClaims();

        $pvpSettings = $claimManager->getPvpSettings();

        if (!isset($claims[$chunkId]) || $claims[$chunkId] !== $sender->getName()) {

            $sender->sendMessage(TextFormat::RED . "You do not own this chunk.");

            return;

        }

        $pvpSettings[$chunkId] = $state;

        $claimManager->setPvpSettings($pvpSettings);

        $sender->sendMessage(TextFormat::GREEN . "PvP " . ($state === "on" ? "enabled" : "disabled") . " in your chunk.");

    }

}