<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Exception;

use Dot\DataFixtures\Exception\NotFoundException;
use Exception;
use PHPUnit\Framework\TestCase;

class NotFoundExceptionTest extends TestCase
{
    public function testCreate(): void
    {
        $exception = new NotFoundException();
        $this->assertContainsOnlyInstancesOf(NotFoundException::class, [$exception]);
        $this->assertContainsOnlyInstancesOf(Exception::class, [$exception]);
    }
}
