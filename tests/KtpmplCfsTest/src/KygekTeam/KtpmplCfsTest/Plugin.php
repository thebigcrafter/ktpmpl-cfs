<?php

/*
 * Plugin to test the ktpmpl-cfs virion
 * Copyright (C) 2022 KygekTeam
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace KygekTeam\KtpmplCfsTest;

use KygekTeam\KtpmplCfs\KtpmplCfs;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Plugin extends PluginBase {

	protected function onEnable(): void {
		$this->saveDefaultConfig();
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if ($command->getName() === "ktpmplcfs") {
			if (!$sender instanceof ConsoleCommandSender) {
				$sender->sendMessage(KnownTranslationFactory::pocketmine_command_error_permission($command->getName())->prefix(TextFormat::RED));
				return true;
			}

			$this->getLogger()->info("Starting tests...");
			$fails = 0;

			$this->getLogger()->info("Starting testCheckConfig() tests [1/2]");
			if ($this->testCheckConfig()) {
				$this->getLogger()->info("Success running testCheckConfig() tests");
			} else {
				$this->getLogger()->warning("Failed running testCheckConfig() tests");
				$fails++;
			}

			$this->getLogger()->info("Starting testCheckUpdates() tests [2/2]");
			if ($this->testCheckUpdates()) {
				$this->getLogger()->info("Success running testCheckUpdates() tests");
			} else {
				$this->getLogger()->warning("Failed running testCheckUpdates() tests");
				$fails++;
			}

			$this->getLogger()->info("Finished tests!");
			if ($fails === 0) {
				$this->getLogger()->info("SUCCESS: All tests run successfully");
			} else {
				$this->getLogger()->warning("FAILED: $fails test(s) were failed");
			}

			return true;
		}
		return false;
	}

	private function testCheckConfig(): bool {
		$success = $this->runTest("checkConfig(): Matching version", function (): bool {
			KtpmplCfs::checkConfig($this, "1.0");
			return $this->getConfig()->get("config-version") === "1.0";
		});
		if (!$success) return false;

		$success = $this->runTest("checkConfig(): Restore outdated file", function (): bool {
			$this->setConfig("config-version", "0.0");
			KtpmplCfs::checkConfig($this, "1.0");
			return $this->getConfig()->get("config-version") === "1.0" && is_file($this->getDataFolder() . "config_old.yml");
		});
		if (!$success) return false;

		return true;
	}

	private function testCheckUpdates(): bool {
		return $this->runTest("checkUpdates(): Disabled setting", function (): bool {
			$this->setConfig("check-updates", false);
			KtpmplCfs::checkUpdates($this);
			return $this->getConfig()->get("check-updates") === false;
		});
	}

	private function runTest(string $info, callable $test): bool {
		$this->getLogger()->info("Testing ktpmpl-cfs method $info");
		if ($test()) {
			$this->getLogger()->info("Success testing ktpmpl-cfs method $info");
		} else {
			$this->getLogger()->warning("Failed testing ktpmpl-cfs method $info");
			return false;
		}
		return true;
	}

	private function setConfig(string $key, mixed $value) {
		$this->getConfig()->setNested($key, $value);
		$this->getConfig()->save();
		$this->getConfig()->reload();
	}
}
