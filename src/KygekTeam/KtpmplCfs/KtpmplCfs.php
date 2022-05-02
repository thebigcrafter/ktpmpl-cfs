<?php

/*
 * Virion to easily implement common functionalities on KygekTeam PocketMine-MP plugins (e.g. config version check)
 * Copyright (C) 2021-2022 KygekTeam
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace KygekTeam\KtpmplCfs;

use JackMD\UpdateNotifier\UpdateNotifier;
use pocketmine\plugin\PluginBase;

class KtpmplCfs {

	/**
	 * KtpmplCfs constructor.
	 * 
	 * @param PluginBase $plugin
	 */
	public function __construct(
		private PluginBase $plugin
	) {
	}

	/**
	 * Checks for configuration file version.
	 *
	 * If doesn't match the provided version, will send message to Logger, rename existing configuration file and generate new configuration file.
	 *
	 * @param string $version The configuration file version to check.
	 * @param string $key Configuration file key to check, default is config-version.
	 * @param bool $onlyCheck Whether to only check the configuration file version, default is false.
	 * @return bool True if configuration file version matches the provided version, otherwise false.
	 */
	public function checkConfig(string $version, string $key = "config-version", bool $onlyCheck = false): bool {
		$plugin = $this->plugin;
		if ($plugin->getConfig()->get($key) !== $version) {
			if (!$onlyCheck) {
				$plugin->getLogger()->notice("Your configuration file is outdated, updating the config.yml...");
				$plugin->getLogger()->notice("The old configuration file can be found at config_old.yml");
				rename($plugin->getDataFolder() . "config.yml", $plugin->getDataFolder() . "config_old.yml");
				$plugin->saveDefaultConfig();
				$plugin->getConfig()->reload();
			}
			return false;
		}
		return true;
	}

	/**
	 * Checks for plugin updates in Poggit using the UpdateNotifier virion.
	 *
	 * @param string $key Configuration file key to check, default is check-updates.
	 * @param bool $onlycheck Whether to only check if plugin updates checking is enabled in configuration file, default is false.
	 * @return bool True if plugin updates checking is enabled in configuration file, otherwise false.
	 */
	public function checkUpdates(string $key = "check-updates", bool $onlycheck = false): bool {
		$plugin = $this->plugin;
		if ($plugin->getConfig()->get($key, true)) {
			if (!$onlycheck) {
				UpdateNotifier::checkUpdate($plugin->getDescription()->getName(), $plugin->getDescription()->getVersion());
			}
			return true;
		}
		return false;
	}

	/**
	 * Send a warning to the console that the plugin is running on a development version.
	 *
	 * @param string $key Configuration file key to check, default is warn-development.
	 * @param bool $onlyCheck Whether to only check if arn development version is enabled in configuration file, default is false.
	 * @return bool True if warn development version is enabled in configuration file, otherwise false.
	 */
	public function warnDevelopmentVersion(string $key = "warn-development", bool $onlyCheck = false): bool {
		$plugin = $this->plugin;
		if ($plugin->getConfig()->get($key, true)) {
			if (!$onlyCheck) {
				$name = $plugin->getName();
				$plugin->getLogger()->warning("This plugin is running on a development version. There might be some major bugs. If you found one, please submit an issue in https://github.com/thebigcrafter/$name/issues.");
			}
			return true;
		}
		return false;
	}
}
