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
            return;
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
                // Lógica para tp
                break;

            case "spawn":
                // Lógica para spawn
                break;

            case "ban":
                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::RED . "Usage: /claim ban <player>");
                    return false;
                }
                // Lógica para ban
                break;

            case "unban":
                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::RED . "Usage: /claim unban <player>");
                    return false;
                }
                // Lógica para unban
                break;

            case "disclaim":
                // Lógica para disclaim
                break;

            case "fly":
                // Lógica para fly
                break;

            case "info":
                // Lógica para info
                break;

            default:
                $sender->sendMessage(TextFormat::RED . "Unknown action: " . $args[0]);
                return false;
        }

        return true;
    }
}
