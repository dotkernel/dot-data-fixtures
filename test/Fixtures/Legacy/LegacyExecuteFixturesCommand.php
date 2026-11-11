<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Fixtures\Legacy;

use Dot\DataFixtures\Command\ExecuteFixturesCommand;

/**
 * Redeclares $defaultName the way a 1.x subclass would; it must keep compiling in 2.x.
 */
class LegacyExecuteFixturesCommand extends ExecuteFixturesCommand
{
    /** @var string */
    protected static string $defaultName = 'app:fixtures:execute';
}
