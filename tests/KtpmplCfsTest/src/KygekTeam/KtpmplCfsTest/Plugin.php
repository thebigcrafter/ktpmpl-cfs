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
use pocketmine\plugin\PluginBase;

class Plugin extends PluginBase {

    private KtpmplCfs $ktpmplCfs;

    protected function onEnable() : void {
        $this->saveDefaultConfig();
        $this->ktpmplCfs = new KtpmplCfs($this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if ($command->getName() === "ktpmplcfs") {
            $this->getLogger()->info("Starting tests...");
            $fails = 0;

            $this->getLogger()->info("Starting testCheckConfig() tests [1/3]");
            if ($this->testCheckConfig()) {
                $this->getLogger()->info("Success running testCheckConfig() tests");
            } else {
                $this->getLogger()->warning("Failed running testCheckConfig() tests");
                $fails++;
            }

            $this->getLogger()->info("Starting testCheckUpdates() tests [2/3]");
            if ($this->testCheckUpdates()) {
                $this->getLogger()->info("Success running testCheckUpdates() tests");
            } else {
                $this->getLogger()->warning("Failed running testCheckUpdates() tests");
                $fails++;
            }

            $this->getLogger()->info("Starting testWarnDevelopmentVersion() tests [3/3]");
            if ($this->testWarnDevelopmentVersion()) {
                $this->getLogger()->info("Success running testWarnDevelopmentVersion() tests");
            } else {
                $this->getLogger()->warning("Failed running testWarnDevelopmentVersion() tests");
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

    private function testCheckConfig() : bool {
        $success = $this->runTest("checkConfig(): Return true [1/2]", function () : bool {
            return $this->ktpmplCfs->checkConfig("1.0", "version", true);
        });
        if (!$success) return false;

        $success = $this->runTest("checkConfig(): Return false [2/2]", function () : bool {
            return !$this->ktpmplCfs->checkConfig("10", "version", true);
        });
        if (!$success) return false;

        return true;
    }

    private function testCheckUpdates() : bool {
        $success = $this->runTest("checkUpdates(): Return true [1/2]", function () : bool {
            return $this->ktpmplCfs->checkUpdates("updates", true);
        });
        if (!$success) return false;

        $success = $this->runTest("checkUpdates(): Return false [2/2]", function () : bool {
            $this->setConfig("updates", false);
            $result = !$this->ktpmplCfs->checkUpdates("updates", true);
            $this->setConfig("updates", true);
            return $result;
        });
        if (!$success) return false;

        return true;
    }

    private function testWarnDevelopmentVersion() : bool {
        $success = $this->runTest("warnDevelopmentVersion(): Return true [1/2]", function () : bool {
            return $this->ktpmplCfs->warnDevelopmentVersion("development", true);
        });
        if (!$success) return false;

        $success = $this->runTest("warnDevelopmentVersion(): Return false [2/2]", function () : bool {
            $this->setConfig("development", false);
            $result = !$this->ktpmplCfs->warnDevelopmentVersion("development", true);
            $this->setConfig("development", true);
            return $result;
        });
        if (!$success) return false;

        return true;
    }

    private function runTest(string $info, callable $test) : bool {
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