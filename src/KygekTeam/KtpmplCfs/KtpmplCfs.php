<?php

/*
 * Virion to easily implement common functionalities on KygekTeam PocketMine-MP plugins (e.g. config version check)
 * Copyright (C) 2021 KygekTeam
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace KygekTeam\KtpmplCfs;

use JackMD\UpdateNotifier\UpdateNotifier;
use pocketmine\plugin\Plugin;

class KtpmplCfs {

    /**
     * KtpmplCfs constructor.
     * 
     * @param Plugin $plugin
     */
    public function __construct(
        private Plugin $plugin
    ) {}

    /**
     * Checks for configuration file version.
     * 
     * If doesn't match the provided version, will send message to Logger, rename existing configuration file and generate new configuration file.
     * 
     * Returns true if configuration file version matches the provided version, otherwise false.
     *
     * @param string $version
     * @return bool
     */
    public function checkConfig(string $version) : bool {
        $plugin = $this->plugin;
        if ($plugin->getConfig()->get("config-version") !== $version) {
            $plugin->getLogger()->notice("Your configuration file is outdated, updating the config.yml...");
            $plugin->getLogger()->notice("The old configuration file can be found at config_old.yml");
            rename($plugin->getDataFolder()."config.yml", $plugin->getDataFolder()."config_old.yml");
            $plugin->saveDefaultConfig();
            $plugin->getConfig()->reload();
            return false;
        }
        return true;
    }

    /**
     * Checks for plugin updates in Poggit.
     * Plugin must include the UpdateNotifier virion.
     *
     * Returns true if update check is enabled in config.yml, otherwise false.
     *
     * @return bool
     */
    public function checkUpdates() : bool {
        $plugin = $this->plugin;
        if ($plugin->getConfig()->get("check-updates", true)) {
            UpdateNotifier::checkUpdate($plugin->getDescription()->getName(), $plugin->getDescription()->getVersion());
            return true;
        }
        return false;
    }

}