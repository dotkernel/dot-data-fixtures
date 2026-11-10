# Upgrading from 1.x to 2.0

Version 2.0 fixes the `fixtures:list` output, makes the package work with Symfony Console 8, and hardens the `--class` option. Most applications only need to update the version constraint:

```shell
composer require dotkernel/dot-data-fixtures:^2.0
```

## Changes that can break your code

| Change | Symptom | Fix |
| --- | --- | --- |
| PHP 8.2 is no longer supported | Composer refuses to install 2.0 on PHP 8.2 | Upgrade to PHP 8.3 or later, or stay on 1.x |
| `symfony/console` 7.0.0 to 7.0.2 and `symfony/string` 6.4.0 to 6.4.2 and 7.0.0 to 7.0.2 are no longer allowed | Composer refuses to install 2.0 while one of them is pinned | Update them (`composer update symfony/console symfony/string`) |
| The protected static `$defaultName` property was removed from both commands | A subclass that reads `self::$defaultName` or `static::$defaultName` fails with "Access to undeclared static property" | Use `ExecuteFixturesCommand::NAME` or `ListFixturesCommand::NAME` |
| Command names and descriptions come from `#[AsCommand]` | Code that calls `Command::getDefaultName()` fails on Symfony Console 8, where that method was removed | Use the `NAME` constants |
| `--class` requires a value | `fixtures:execute --class` without a value prints `The "--class" option requires a value.` | Pass a fixture name, or omit the option to run all fixtures |
| `--class` is a path relative to the fixtures folder | Fixtures in subfolders need the subfolder, for example `--class=User/UserLoader` | Use the command that `fixtures:list` prints |
| `--class` cannot point outside the fixtures folder | `--class=../Other/File` used to load that file; it is now refused | Move the fixture into the fixtures folder |
| A missing fixture returns exit code `1` | No exception and stack trace; an error line is printed instead | Check the exit code |
| The `--class` default is `null` instead of `false` | Only code that inspects the command's input definition sees it | Compare with `null` |

## Before and after

Reading the command name in a subclass or a script:

```php
// 1.x
$name = ExecuteFixturesCommand::getDefaultName(); // null on Symfony Console 7.3+, fatal error on 8.x
```

```php
// 2.0
$name = ExecuteFixturesCommand::NAME; // 'fixtures:execute'
```

Executing a fixture from a subfolder:

```shell
# 1.x: fixtures:list suggested this, and it failed
php ./bin/doctrine fixtures:execute --class=UserLoader

# 2.0
php ./bin/doctrine fixtures:execute --class=User/UserLoader
```

## Unchanged

- The configuration key (`doctrine.fixtures`) and the `ConfigProvider`
- The service keys `ExecuteFixturesCommand::class` and `ListFixturesCommand::class`, and the container key `Doctrine\ORM\EntityManager`
- The command names `fixtures:execute` and `fixtures:list`
- Fixtures are appended; nothing is purged
