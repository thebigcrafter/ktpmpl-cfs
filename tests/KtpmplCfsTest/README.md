# KtpmplCfsTest

A PocketMine-MP 5 plugin to manually test the current static `ktpmpl-cfs`
virion API.

**WARNING:** **Do not** use this command in **production servers**! Only use this plugin for testing or debugging purposes in development server instances. Any damages or loss by using this plugin are **not under our responsibility nor warranty** in any way.

## Installation

The current `.poggit.yml` builds the virion library only; this fixture is kept
for local consumer testing and is not published as a current Poggit artifact.
Build it with a matching PocketMine-MP 5 and virion toolchain, then move the
resulting PHAR to the server's `plugins` directory.

## Usage

To start the test, first start your server. Then run the `/ktpmplcfs` command
in the console; it is intentionally unavailable to in-game senders. Wait until
the test finishes.

The fixture checks the current static configuration helper and the disabled
update-check path. It does not prove live Poggit connectivity or consumer
compatibility on every PocketMine-MP 5 release. If it fails, include the
server, virion and consumer revisions in an [issue](https://github.com/thebigcrafter/ktpmpl-cfs/issues).
