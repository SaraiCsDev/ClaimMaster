<?php

declare(strict_types=1);

namespace Banshoku\Claims\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Banshoku\Claims\Main;

class ClaimCommand extends Command {

    private PluginBase $plugin;

    public function __construct(PluginBase $plugin) {
        parent::__construct("claim", "Claim command with multiple actions", null, ["claim"]);
        $this->plugin = $plugin;
        $this->setPermission("claims.command");
    }

    public function execute(CommandSender $sender, string $label, array $args) : bool {
        if (!$this->testPermission($sender)) {
            return false;
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return false;
        }

        if (count($args) === 0) {
            $sender->sendMessage(TextFormat::RED . "Usage: /claim <action>");
            return false;
        }

        $plugin = Main::getInstance();
        $claimManager = $plugin->getClaimManager();

        switch ($args[0]) {
            case "reclaim":
                // Lógica para reclaim
                break;

            case "permadd":
                if (count($args) < 3) {
                    $sender->sendMessage(TextFormat::RED . "Usage: /claim permadd <player> <permission>");
                    return false;
                }
                // Lógica para permadd
                break;

            case "permdel":
                if (count($args) < 3) {
                    $sender->sendMessage(TextFormat::RED . "Usage: /claim permdel <player> <permission>");
                    return false;
                }
                // Lógica para permdel
                break;

            case "manager":
                // Lógica para manager
                break;

            case "pvp":
                // Lógica para pvp
                break;

            case "border":
                // Lógica para border
                break;

            case "tp":
                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::RED . "Usage: /claim tp <player>");
                    return false;
                }
            
        $index = $args[1] - 1;
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
                break;

            case "spawn":
            
    if (!$sender->hasPermission("claims.admin")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;

        }

        $chunkX = $sender->getPosition()->getFloorX() >> 4;
        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;
        $claimManager->addSpawnChunk($chunkX, $chunkZ);
        $sender->sendMessage(TextFormat::GREEN . "Chunk ({$chunkX}, {$chunkZ}) has been added as a spawn chunk.");
                break;

            case "ban":
                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::RED . "Usage: /claim ban <player>");
                    return false;
                }
                 $bannedPlayerName = $args[1];
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
                break;

            case "unban":
                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::RED . "Usage: /claim unban <player>");
                    return false;
                }
        $unbannedPlayerName = $args[1];

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
                break;

            case "disclaim":
       $chunkX = $sender->getPosition()->getFloorX() >> 4;

        $chunkZ = $sender->getPosition()->getFloorZ() >> 4;

        $chunkId = "{$chunkX}:{$chunkZ}";

        $claims = $claimManager->getClaims();

        if (isset($claims[$chunkId]) && $claims[$chunkId] === $sender->getName()) {

            $claimManager->removeClaim($chunkId);

            $sender->sendMessage(TextFormat::GREEN . "You have successfully disclaimed this chunk.");

        } else {

            $sender->sendMessage(TextFormat::RED . "You do not own this chunk.");

        }
                break;

            case "fly":
            
            if (!$sender->hasPermission("claims.vip")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        if ($sender->isCreative()) {
            $sender->sendMessage(TextFormat::RED . "This command cannot be used in creative mode.");
            return;
        }
            
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
                break;

            case "info":
            
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
            
                break;

            default:
                $sender->sendMessage(TextFormat::RED . "Unknown action: " . $args[0]);
                return false;
        }

        return true;
    }
}
