<?php

declare(strict_types=1);

namespace DotTest\DataFixtures\Exception;

use Dot\DataFixtures\Exception\NotFoundException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NotFoundExceptionTest extends TestCase
{
    protected NotFoundException|MockObject $exceptionMock;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->exceptionMock = $this->createMock(NotFoundException::class);
    }

    public function testCreate()
    {
        $this->assertInstanceOf(NotFoundException::class, $this->exceptionMock);
    }
}
