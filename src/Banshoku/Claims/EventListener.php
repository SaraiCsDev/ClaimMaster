<?php

declare(strict_types=1);

namespace Banshoku\Claims;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EventListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    private function isWithinChunkBounds(int $x, int $z, int $chunkX, int $chunkZ): bool {
        return $x >= ($chunkX << 4) - 1 && $x < (($chunkX + 1) << 4) + 1 && $z >= ($chunkZ << 4) - 1 && $z < (($chunkZ + 1) << 4) + 1;
    }

    private function sendClaimInfoPopup(Player $player, string $chunkId): void {
        $claimManager = $this->plugin->getClaimManager();
        $claims = $claimManager->getClaims();
        $permissions = $claimManager->getPermissions();
        $pvpSettings = $claimManager->getPvpSettings();

        if (isset($claims[$chunkId])) {
            $owner = $claims[$chunkId];
            $pvpStatus = $pvpSettings[$chunkId] ?? "off";
            $hasPermission = in_array($player->getName(), $permissions[$chunkId] ?? []);
            $permissionStatus = $claims[$chunkId] === $player->getName() ? "Owner" : ($hasPermission ? "True" : "False");

            $player->sendPopup(
                TextFormat::GOLD . "Claim Owner: " . TextFormat::WHITE . $owner . "\n" .
                TextFormat::GOLD . "PvP: " . TextFormat::WHITE . $pvpStatus . "\n" .
                TextFormat::GOLD . "Permissions: " . TextFormat::WHITE . $permissionStatus
            );
        } else {
            $player->sendPopup(TextFormat::RED . "This chunk is not claimed.");
        }
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $chunkX = $block->getPosition()->getFloorX() >> 4;
        $chunkZ = $block->getPosition()->getFloorZ() >> 4;
        $claimManager = $this->plugin->getClaimManager();

        if (!$this->isWithinChunkBounds($block->getPosition()->getFloorX(), $block->getPosition()->getFloorZ(), $chunkX, $chunkZ)) {
            return;
        }

        $chunkId = "{$chunkX}:{$chunkZ}";

        if ($claimManager->isChunkSpawnProtected($chunkX, $chunkZ)) {
            $event->cancel();
            $player->sendMessage(TextFormat::RED . "You cannot break blocks in a spawn chunk.");
            return;
        }

        $this->sendClaimInfoPopup($player, $chunkId);

        $claims = $claimManager->getClaims();

        if (!isset($claims[$chunkId])) {
            return;
        }

        if ($claims[$chunkId] !== $player->getName() && !in_array($player->getName(), $claimManager->getPermissions())) {
            $event->cancel();
            $player->sendMessage(TextFormat::RED . "You do not have permission to break blocks in this claim.");
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlockAgainst();
        $chunkX = $block->getPosition()->getFloorX() >> 4;
        $chunkZ = $block->getPosition()->getFloorZ() >> 4;
        $claimManager = $this->plugin->getClaimManager();

        if (!$this->isWithinChunkBounds($block->getPosition()->getFloorX(), $block->getPosition()->getFloorZ(), $chunkX, $chunkZ)) {
            return;
        }

        $chunkId = "{$chunkX}:{$chunkZ}";

        if ($claimManager->isChunkSpawnProtected($chunkX, $chunkZ)) {
            $event->cancel();
            $player->sendMessage(TextFormat::RED . "You cannot place blocks in a spawn chunk.");
            return;
        }

        $this->sendClaimInfoPopup($player, $chunkId);

        $claims = $claimManager->getClaims();

        if (!isset($claims[$chunkId])) {
            return;
        }

        if ($claims[$chunkId] !== $player->getName() && !in_array($player->getName(), $claimManager->getPermissions())) {
            $event->cancel();
            $player->sendMessage(TextFormat::RED . "You do not have permission to place blocks in this claim.");
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $chunkX = $block->getPosition()->getFloorX() >> 4;
        $chunkZ = $block->getPosition()->getFloorZ() >> 4;
        $claimManager = $this->plugin->getClaimManager();

        if (!$this->isWithinChunkBounds($block->getPosition()->getFloorX(), $block->getPosition()->getFloorZ(), $chunkX, $chunkZ)) {
            return;
        }

        $chunkId = "{$chunkX}:{$chunkZ}";

        if ($claimManager->isChunkSpawnProtected($chunkX, $chunkZ)) {
            $event->cancel();
            $player->sendMessage(TextFormat::RED . "You cannot interact with blocks in a spawn chunk.");
            return;
        }

        $this->sendClaimInfoPopup($player, $chunkId);

        $claims = $claimManager->getClaims();

        if (!isset($claims[$chunkId])) {
            return;
        }

         if ($claims[$chunkId] !== $player->getName() && !in_array($player->getName(), $claimManager->getPermissions())) {
            $event->cancel();
            $player->sendMessage(TextFormat::RED . "You do not have permission to interact with blocks in this claim.");
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void {
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            $entity = $event->getEntity();

            if ($damager instanceof Player && $entity instanceof Player) {
                $chunkX = $entity->getPosition()->getFloorX() >> 4;
                $chunkZ = $entity->getPosition()->getFloorZ() >> 4;
                $claimManager = $this->plugin->getClaimManager();

                if (!$this->isWithinChunkBounds($entity->getPosition()->getFloorX(), $entity->getPosition()->getFloorZ(), $chunkX, $chunkZ)) {
                    return;
                }

                $chunkId = "{$chunkX}:{$chunkZ}";

                if ($claimManager->isChunkSpawnProtected($chunkX, $chunkZ)) {
                    $event->cancel();
                    $damager->sendMessage(TextFormat::RED . "You cannot engage in PvP in a spawn chunk.");
                    return;
                }

                $this->sendClaimInfoPopup($damager, $chunkId);

                $claims = $claimManager->getClaims();
                $pvpSettings = $claimManager->getPvpSettings();

                if (!isset($claims[$chunkId])) {
                    return;
                }

                if (($pvpSettings[$chunkId] ?? "off") === "off") {
                    $event->cancel();
                    $damager->sendMessage(TextFormat::RED . "PvP is disabled in this claim.");
                }
            }
        }
    }

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $to = $event->getTo();
        $chunkX = $to->getFloorX() >> 4;
        $chunkZ = $to->getFloorZ() >> 4;
        $claimManager = $this->plugin->getClaimManager();

        $chunkId = "{$chunkX}:{$chunkZ}";
        $this->sendClaimInfoPopup($player, $chunkId);
    }
}
