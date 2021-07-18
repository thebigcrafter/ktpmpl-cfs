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

use KygekTeam\KtpmplCfs\task\UpdateNotifyTask;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class KtpmplCfs {

    /**
     * Checks for configuration file version.
     * If doesn't match the provided version, will send message to Logger, rename existing configuration file and generate new configuration file.
     *
     * @param Plugin $plugin
     * @param string $version
     */
    public static function checkConfig(Plugin $plugin, string $version) {
        if ($plugin->getConfig()->get("config-version") !== $version) {
            $plugin->getLogger()->notice("Your configuration file is outdated, updating the config.yml...");
            $plugin->getLogger()->notice("The old configuration file can be found at config_old.yml");
            rename($plugin->getDataFolder()."config.yml", $plugin->getDataFolder()."config_old.yml");
            $plugin->saveDefaultConfig();
            $plugin->getConfig()->reload();
        }
    }

    /**
     * Checks for plugin updates in Poggit.
     * Plugin must include the UpdateNotifier virion.
     *
     * @param Plugin $plugin
     */
    public static function checkUpdates(Plugin $plugin) {
        if ($plugin->getConfig()->get("check-updates", true)) {
            Server::getInstance()->getAsyncPool()->submitTask(new UpdateNotifyTask(
                $plugin->getDescription()->getName(),
                $plugin->getDescription()->getVersion()
            ));
        }
    }

}