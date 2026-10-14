# ktpmpl-cfs

[![Discord](https://img.shields.io/discord/735439472992321587.svg?label=&logo=discord&logoColor=ffffff&color=7389D8&labelColor=6A7EC2)](https://discord.gg/CXtqUZv)

A PocketMine-MP API 4 and API 5 virion that provides common helpers for KygekTeam
plugins, including configuration-version checks and asynchronous Poggit update
notifications. It avoids duplicating the same integration code across plugins.

## Features

- Configuration file version check
- Plugin update check through Poggit
- An update-notification task bundled with this virion

## Compatibility

The current `main` branch declares virion version `2.0.2`, API `4.0.0` and
API `5.0.0` compatibility, and PHP `8.0` in `virion.yml`. It exposes static helpers that receive the
consuming `Plugin` instance:

```php
use KygekTeam\KtpmplCfs\KtpmplCfs;

KtpmplCfs::checkConfig($plugin, "1.0.0");
KtpmplCfs::checkUpdates($plugin);
```

The historical `v4.0.0` tag and GitHub release use an earlier instance-based
API and should not be treated as the current `main` contract. Consumers must
choose a matching revision and verify the resulting plugin build.

## Usage & Contribution

**This virion is intended to be used only for KygekTeam plugins**, however you
can contribute by forking this repository and [submitting a pull request](https://github.com/thebigcrafter/ktpmpl-cfs/pulls).

## Upcoming Features

The current source is maintained for API 4 and API 5 consumers. The retained
manual fixture exercises the current static API on API 4; API 5 compatibility is
declared because the implementation uses shared `Plugin`, scheduler and
`Internet` contracts, but consuming plugins should still run their own API 5
integration check. Request a compatible change by [creating an issue](https://github.com/thebigcrafter/ktpmpl-cfs/issues)
with the consumer and PocketMine-MP contract involved.

## Support & Contact

If you find a bug or want to give feedback, [create an issue](https://github.com/thebigcrafter/ktpmpl-cfs/issues)
on GitHub and include the virion revision and consumer context.

Join the [KygekTeam Discord server](https://discord.gg/CXtqUZv) for updates,
announcements and support.
