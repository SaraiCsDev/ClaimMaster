<?php

declare(strict_types=1);

namespace Banshoku\Claims\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\world\particle\DustParticle;
use pocketmine\math\Vector3;
use pocketmine\color\Color;
use Banshoku\Claims\Main;

class BorderTask extends Task {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(): void {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            if ($this->plugin->getClaimManager()->isBorderActive($player)) {
                $this->showBorder($player);
            }
        }
    }

    private function showBorder(Player $player): void {
        $chunkX = $player->getPosition()->getFloorX() >> 4;
        $chunkZ = $player->getPosition()->getFloorZ() >> 4;
        $minX = ($chunkX << 4);
        $maxX = ($minX + 16);
        $minZ = ($chunkZ << 4);
        $maxZ = ($minZ + 16);
        $y = $player->getPosition()->getY();

        for ($x = $minX; $x <= $maxX; $x++) {
            for ($z = $minZ; $z <= $maxZ; $z++) {
                if ($x == $minX || $x == $maxX || $z == $minZ || $z == $maxZ) {
                    $position = new Vector3($x, $y + 1, $z);
                    $color = $this->plugin->getClaimManager()->getClaims()["$chunkX:$chunkZ"] === $player->getName() ? new Color(0, 255, 0) : new Color(0, 0, 0);
                    $player->getWorld()->addParticle($position, new DustParticle($color));
                }
            }
        }
    }
}
