<?php

declare(strict_types=1);

namespace MINSEO\Mine;

use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use function mt_rand;

final class Mine extends PluginBase implements Listener
{
	protected function onEnable(): void
	{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onPlace(BlockPlaceEvent $event): void
	{
		$blocks = $event->getTransaction()->getBlocks();
		foreach($blocks as [$x, $y, $z, $block]) {
			$pos = $block->getPosition();
			$world = $pos->getWorld();
			if($block->getTypeId() !== BlockTypeIds::SPONGE) continue;
			$world->setBlock($pos->add(0,1,0), $this->getRandomBlock());
		}
	}

	public function onBreak(BlockBreakEvent $event): void
	{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$pos = $block->getPosition();
		$world = $pos->getWorld();
		if($world->getBlock($pos->add(0, -1, 0))->getTypeId() !== BlockTypeIds::SPONGE) return;
		$event->cancel();
		$world->setBlock($pos, $this->getRandomBlock());
        $player->getInventory()->addItem($this->getRandomItems($block->getTypeId()));

	}

	public function getRandomItems(int $block): Item
	{
		return match ($block) {
			BlockTypeIds::COAL_ORE => VanillaItems::COAL(),
			BlockTypeIds::IRON_ORE => VanillaItems::IRON_INGOT(),
			BlockTypeIds::GOLD_ORE => VanillaItems::GOLD_INGOT(),
			BlockTypeIds::REDSTONE_ORE => VanillaItems::REDSTONE_DUST()->setCount(mt_rand(1, 4)),
			BlockTypeIds::LAPIS_LAZULI_ORE => VanillaItems::LAPIS_LAZULI()->setCount(mt_rand(1, 4)),
			BlockTypeIds::DIAMOND_ORE => VanillaItems::DIAMOND(),
			BlockTypeIds::EMERALD_ORE => VanillaItems::EMERALD(),
			default => VanillaBlocks::COBBLESTONE()->asItem(),
		};
	}

	public function getRandomBlock(): Block
	{
		$rand = mt_rand(1, 100);
		if($rand === 100) return VanillaBlocks::EMERALD_ORE();
		if($rand === 99) return VanillaBlocks::DIAMOND_ORE();
		if($rand <= 50) return VanillaBlocks::STONE();
		if($rand <= 70) return VanillaBlocks::COAL_ORE();
		if($rand <= 80) return VanillaBlocks::IRON_ORE();
		if($rand <= 88) return VanillaBlocks::GOLD_ORE();
		if($rand <= 92) return VanillaBlocks::REDSTONE_ORE();
		if($rand <= 96) return VanillaBlocks::LAPIS_LAZULI_ORE();
		return VanillaBlocks::STONE();
	}
}