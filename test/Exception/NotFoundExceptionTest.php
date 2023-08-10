<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Exception;

use Dot\DataFixtures\Exception\NotFoundException;
use Exception;
use PHPUnit\Framework\TestCase;

class NotFoundExceptionTest extends TestCase
{
    public function testCreate()
    {
        $exception = new NotFoundException();
        $this->assertInstanceOf(NotFoundException::class, $exception);
        $this->assertInstanceOf(Exception::class, $exception);
    }
}
