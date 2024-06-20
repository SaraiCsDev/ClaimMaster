<?php

declare(strict_types=1);

namespace Banshoku\Claims;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class ClaimManager {

    private Main $plugin;
    private array $claims = [];
    private array $permissions = [];
    private array $pvpSettings = [];
    private array $bans = [];
    private array $borderActive = [];
    private array $spawnChunks = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->reloadClaimsData();
    }

    public function reloadClaimsData(): void {
        $claimsConfig = new Config($this->plugin->getDataFolder() . "claims.yml", Config::YAML);
        $this->claims = $claimsConfig->get("claims", []);
        $this->permissions = $claimsConfig->get("permissions", []);
        $this->pvpSettings = $claimsConfig->get("pvpSettings", []);
        $this->bans = $claimsConfig->get("bans", []);
        $this->spawnChunks = $this->plugin->getConfig()->get("spawnChunks", []);
    }

    public function saveClaimsData(): void {
        $claimsConfig = new Config($this->plugin->getDataFolder() . "claims.yml", Config::YAML);
        $claimsConfig->set("claims", $this->claims);
        $claimsConfig->set("permissions", $this->permissions);
        $claimsConfig->set("pvpSettings", $this->pvpSettings);
        $claimsConfig->set("bans", $this->bans);
        $claimsConfig->save();
        $this->plugin->getConfig()->set("spawnChunks", $this->spawnChunks);
        $this->plugin->getConfig()->save();
    }

    public function getClaims(): array {
        return $this->claims;
    }

    public function getPermissions(): array {
        return $this->permissions;
    }

    public function getPvpSettings(): array {
        return $this->pvpSettings;
    }

    public function getBans(): array {
        return $this->bans;
    }

    public function setClaims(array $claims): void {
        $this->claims = $claims;
    }

    public function setPermissions(array $permissions): void {
        $this->permissions = $permissions;
    }

    public function setPvpSettings(array $pvpSettings): void {
        $this->pvpSettings = $pvpSettings;
    }

    public function setBans(array $bans): void {
        $this->bans = $bans;
        $this->saveClaimsData();
    }

    public function isChunkSpawnProtected(int $chunkX, int $chunkZ): bool {
        return in_array("{$chunkX}:{$chunkZ}", $this->spawnChunks);
    }

    public function addSpawnChunk(int $chunkX, int $chunkZ): void {
        $chunkId = "{$chunkX}:{$chunkZ}";
        if (!in_array($chunkId, $this->spawnChunks)) {
            $this->spawnChunks[] = $chunkId;
        }
        $this->plugin->getConfig()->set("spawnChunks", $this->spawnChunks);
        $this->plugin->getConfig()->save();
    }

    public function removeSpawnChunk(string $chunkId): bool {
        if (($key = array_search($chunkId, $this->spawnChunks)) !== false) {
            unset($this->spawnChunks[$key]);
            $this->plugin->getConfig()->set("spawnChunks", array_values($this->spawnChunks));
            $this->plugin->getConfig()->save();
            return true;
        }
        return false;
    }

    public function removeClaim(string $chunkId): void {
        unset($this->claims[$chunkId]);
        unset($this->permissions[$chunkId]);
        unset($this->pvpSettings[$chunkId]);
        unset($this->bans[$chunkId]);
        $this->saveClaimsData();
    }

    public function checkPlayerBans(Player $player): void {
        $chunkX = $player->getPosition()->getFloorX() >> 4;
        $chunkZ = $player->getPosition()->getFloorZ() >> 4;
        $chunkId = "{$chunkX}:{$chunkZ}";
        $bans = $this->getBans();

        if (isset($bans[$chunkId]) && in_array($player->getName(), $bans[$chunkId])) {
            // Teleport player out of the banned chunk
            $safeX = ($chunkX + 1) << 4; // Move to the next chunk
            $safeZ = ($chunkZ + 1) << 4; // Move to the next chunk
            $safeY = $player->getWorld()->getHighestBlockAt($safeX, $safeZ) + 1;
            $player->teleport(new \pocketmine\world\Position($safeX, $safeY, $safeZ, $player->getWorld()));
            $player->sendMessage(TextFormat::RED . "You have been removed from the claim because you are banned.");
        }
    }

    public function isBorderActive(Player $player): bool {
        return $this->borderActive[$player->getName()] ?? false;
    }

    public function setBorderActive(Player $player, bool $active): void {
        $this->borderActive[$player->getName()] = $active;
    }

    public function isClaimableChunk(int $chunkX, int $chunkZ): bool {
        return !in_array("{$chunkX}:{$chunkZ}", $this->spawnChunks);
    }
}
