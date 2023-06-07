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

namespace KygekTeam\KtpmplCfs\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

/**
 * This class contains code from UpdateNotifyTask class in UpdateNotifier virion, with changes to be compatible with latest PMMP 4 build.
 * All credits goes to Ifera.
 * Original code: https://github.com/Ifera/UpdateNotifier/blob/master/src/JackMD/UpdateNotifier/task/UpdateNotifyTask.php
 */
class UpdateNotifyTask extends AsyncTask {

    private const POGGIT_RELEASES_URL = "https://poggit.pmmp.io/releases.json?name=";

    private string $pluginName;
    private string $pluginVersion;

    public function __construct(string $pluginName, string $pluginVersion) {
        $this->pluginName = $pluginName;
        $this->pluginVersion = $pluginVersion;
    }

    public function onRun() : void {
        $result = Internet::getURL(self::POGGIT_RELEASES_URL . $this->pluginName, 10, [], $err);
        $highestVersion = $this->pluginVersion;
        $artifactUrl = "";
        $api = "";
        if ($result !== null) {
            $releases = json_decode($result->getBody(), true);
            if ($releases === null || !is_array($releases) || !$releases) {
                $this->setResult([null, null, null, $err ?? "Unable to resolve host: " . self::POGGIT_RELEASES_URL . $this->pluginName]);
                return;
            }
            foreach ($releases as $release) {
                if (version_compare($highestVersion, $release["version"], ">=")) {
                    continue;
                }
                $highestVersion = $release["version"];
                $artifactUrl = $release["artifact_url"];
                $api = $release["api"][0]["from"] . " - " . $release["api"][0]["to"];
            }
        }

        $this->setResult([$highestVersion, $artifactUrl, $api, $err]);
    }

    public function onCompletion() : void {
        $plugin = Server::getInstance()->getPluginManager()->getPlugin($this->pluginName);

        if ($plugin === null) {
            return;
        }

        [$highestVersion, $artifactUrl, $api, $err] = $this->getResult();

        if ($err !== null) {
            $plugin->getLogger()->error("Update notify error: " . $err);
            return;
        }

        if ($highestVersion !== $this->pluginVersion) {
            $artifactUrl = $artifactUrl . "/" . $this->pluginName . "_" . $highestVersion . ".phar";
            $plugin->getLogger()->notice(vsprintf("Version %s has been released for API %s. Download the new release at %s", [$highestVersion, $api, $artifactUrl]));
        }
    }

}