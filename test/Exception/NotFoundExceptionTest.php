<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Exception;

use Dot\DataFixtures\Exception\NotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotFoundException::class)]
class NotFoundExceptionTest extends TestCase
{
    public function testKeepsMessage(): void
    {
        $exception = new NotFoundException('message');

        $this->assertSame('message', $exception->getMessage());
    }
}
