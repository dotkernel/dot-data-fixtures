# Changelog

## 2.0.0 - TBD

### Changed

- `ExecuteFixturesCommand` and `ListFixturesCommand` take their name and description from `#[AsCommand]`
- `--class` requires a value and takes a path relative to the fixtures directory, for example `--class=User/UserLoader`
- `fixtures:execute` prints an error and returns exit code `1` when a fixture cannot be loaded, instead of throwing
- `fixtures:execute` prints the "Executing" lines before running the fixtures
- `ExecuteFixturesCommandFactory` no longer creates an unused `ORMPurger`
- Bumped `phpunit/phpunit` to `^12`; strict `phpunit.xml` (fail on deprecations, notices, warnings and risky tests)
- PHPStan level raised from 5 to 8
- Tests use real fixtures, a real `Loader` and an in-memory SQLite entity manager instead of mocks
- Documentation for v2

### Added

- Support for PHP 8.6
- Support for `symfony/console` 8
- `ExecuteFixturesCommand::NAME` and `ListFixturesCommand::NAME` constants
- `psr/container` declared as a direct requirement
- Upgrade guide from 1.x
- `.gitattributes` to keep line endings LF

### Deprecated

- Nothing

### Removed

- Support for PHP 8.2
- The protected static `$defaultName` property from both commands

### Fixed

- `fixtures:list` printed an empty command name on `symfony/console` 7.3+ and failed on 8.x
- `fixtures:list` suggested a `--class` value that did not work for fixtures in subdirectories
- `fixtures:execute --class` without a value tried to load `.php`
- `fixtures:execute --class` could include PHP files outside the fixtures directory
- `ListFixturesCommand` did not handle `false` from `getFileName()` and `filemtime()`
- `ExecuteFixturesCommandFactory` failed with a `TypeError` inside Doctrine when the entity manager service had the wrong type

## 1.5.0 - 2025-10-30

### Changed

- Nothing

### Added

- Support for PHP 8.5 (#33)

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.4.0 - 2025-02-28

### Changed

- Replaced Psalm with PHPStan (#31)

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- `vimeo/psalm`

### Fixed

- Nothing

## 1.3.1 - 2025-01-27

### Changed

- Bumped `vimeo/psalm` to `^6.0` (#29)

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.3.0 - 2025-01-27

### Changed

- Bumped `doctrine/data-fixtures` from `^1.7` to `^2.0` and `doctrine/orm` from `*` to `^3.0`
- Bumped `laminas/laminas-coding-standard` to `^3.0`
- Updated README.md (#25)

### Added

- Support for PHP 8.4 (#27)

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.2.3 - 2024-07-01

### Changed

- Marked the package as a wrapper of `doctrine/data-fixtures` (#24)

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.2.2 - 2024-06-14

### Changed

- Bumped `doctrine/data-fixtures` to `^1.7` (#22)

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.2.1 - 2024-05-21

### Changed

- Bumped `symfony/console` from `^5.1` to `^7.0` (#20)

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Support for PHP 8.1 (#20)

### Fixed

- Nothing

## 1.2.0 - 2024-05-03

### Changed

- Nothing

### Added

- Documentation for v1 (#17, #18)
- `symfony/console` `^5.1` as a direct requirement

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.1.3 - 2023-11-30

### Changed

- Updated README.md

### Added

- Support for PHP 8.3 (#15)

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.1.2 - 2023-08-29

### Changed

- Nothing

### Added

- Codecov workflow and badge (#10, #12)

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.1.1 - 2023-08-11

### Changed

- Nothing

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- `ORMPurger` not found (#8)

## 1.1.0 - 2023-08-10

### Changed

- Nothing

### Added

- PHPUnit tests, PHP_CodeSniffer and Psalm (#6)

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.0.3 - 2022-08-26

### Changed

- src/Factory/ListFixturesCommandFactory.php
- src/Factory/ExecuteFixturesCommandFactory.php
- CHANGELOG.md

### Added

- Support for PHP 7.4
- src/Command/ListFixturesCommandFactory.php
- src/Command/ExecuteFixturesCommandFactory.php
- CHANGELOG.md

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Added support for PHP 7.4

## 1.0.2 - 2022-08-23

### Changed

- README.md
- src/Command/ListFixturesCommand.php
- src/Command/ExecuteFixturesCommand.php
- CHANGELOG.md

### Added

- README.md
- src/Command/ListFixturesCommand.php
- src/Command/ExecuteFixturesCommand.php
- CHANGELOG.md

### Deprecated

- Nothing

### Removed

- Removed the `--append` option from `ExecuteFixturesCommand`

### Fixed

- Nothing

## 1.0.1 - 2022-08-23

### Changed

- README.md
- src/Command/ListFixturesCommand.php
- CHANGELOG.md

### Added

- README.md
- src/Command/ListFixturesCommand.php
- CHANGELOG.md

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.0.0 - 2022-08-23

### Changed

- Nothing

### Added

- Everything

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing
